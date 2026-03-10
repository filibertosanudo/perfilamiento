<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Group;
use App\Models\Institution;
use App\Models\Test;
use App\Models\TestAssignment;
use App\Models\TestResponse;
use Illuminate\Support\Collection;

class AdminDashboard extends Component
{
    public $period = 'month';

    public function render()
    {
        // Estadísticas generales
        $stats = $this->getGeneralStats();

        // Tests completados por mes (últimos 6 meses)
        $monthlyCompletions = $this->getMonthlyCompletions();

        // Distribución de resultados por categoría
        $categoryDistribution = $this->getCategoryDistribution();

        // Instituciones más activas
        $topInstitutions = $this->getTopInstitutions();

        // Tests más utilizados
        $topTests = $this->getTopTests();

        // Orientadores más activos
        $topAdvisors = $this->getTopAdvisors();

        // Usuarios con resultados preocupantes
        $concerningResults = $this->getConcerningResults();

        // Tests completados recientemente (últimos 10)
        $recentCompletions = $this->getRecentCompletions();

        return view('livewire.admin.admin-dashboard', [
            'stats' => $stats,
            'monthlyCompletions' => $monthlyCompletions,
            'categoryDistribution' => $categoryDistribution,
            'topInstitutions' => $topInstitutions,
            'topTests' => $topTests,
            'topAdvisors' => $topAdvisors,
            'concerningResults' => $concerningResults,
            'recentCompletions' => $recentCompletions,
            'period' => $this->period,
        ]);
    }

    /**
     * Estadísticas generales del sistema
     */
    private function getGeneralStats(): array
    {
        $totalUsers = User::where('role_id', 3)->where('active', true)->count();
        $totalAdvisors = User::where('role_id', 2)->where('active', true)->count();
        $totalGroups = Group::where('active', true)->count();
        $totalInstitutions = Institution::where('active', true)->count();

        $totalAssignments = TestAssignment::where('active', true)->count();
        $totalCompleted = TestResponse::where('completed', true)->count();
        $totalPending = $totalAssignments - $totalCompleted;

        $completedThisMonth = TestResponse::where('completed', true)
            ->whereMonth('finished_at', now()->month)
            ->whereYear('finished_at', now()->year)
            ->count();

        $completedThisWeek = TestResponse::where('completed', true)
            ->whereBetween('finished_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        $completionRate = $totalAssignments > 0 
            ? round(($totalCompleted / $totalAssignments) * 100, 1) 
            : 0;

        // Usuarios que requieren atención
        $concerningCategories = ['severa', 'moderada', 'baja'];
        $usersNeedingAttention = TestResponse::where('completed', true)
            ->where(function ($q) use ($concerningCategories) {
                foreach ($concerningCategories as $category) {
                    $q->orWhere('result_category', 'like', "%{$category}%");
                }
            })
            ->distinct('user_id')
            ->count('user_id');

        return [
            'total_users' => $totalUsers,
            'total_advisors' => $totalAdvisors,
            'total_groups' => $totalGroups,
            'total_institutions' => $totalInstitutions,
            'total_assignments' => $totalAssignments,
            'total_completed' => $totalCompleted,
            'total_pending' => $totalPending,
            'completed_this_month' => $completedThisMonth,
            'completed_this_week' => $completedThisWeek,
            'completion_rate' => $completionRate,
            'users_needing_attention' => $usersNeedingAttention,
        ];
    }

    /**
     * Tests completados por mes (últimos 6 meses)
     */
    private function getMonthlyCompletions(): array
    {
        $months = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
            
            $count = TestResponse::where('completed', true)
                ->whereMonth('finished_at', $date->month)
                ->whereYear('finished_at', $date->year)
                ->count();
            
            $data[] = $count;
        }

        return [
            'labels' => $months,
            'data' => $data,
        ];
    }

    /**
     * Distribución de resultados por categoría
     */
    private function getCategoryDistribution(): Collection
    {
        return TestResponse::where('completed', true)
            ->selectRaw('result_category, COUNT(*) as count')
            ->groupBy('result_category')
            ->orderBy('count', 'desc')
            ->get();
    }

    /**
     * Instituciones más activas
     */
    private function getTopInstitutions(): Collection
    {
        return Institution::withCount([
            'users' => function ($q) {
                $q->where('active', true);
            }
        ])
        ->withCount([
            'users as completed_tests_count' => function ($q) {
                $q->whereHas('testResponses', function ($q) {
                    $q->where('completed', true);
                });
            }
        ])
        ->where('active', true)
        ->orderBy('completed_tests_count', 'desc')
        ->limit(5)
        ->get();
    }

    /**
     * Tests más utilizados
     */
    private function getTopTests(): Collection
    {
        return Test::withCount('assignments')
            ->withCount([
                'assignments as completed_count' => function ($q) {
                    $q->whereHas('responses', function ($q) {
                        $q->where('completed', true);
                    });
                }
            ])
            ->where('active', true)
            ->orderBy('completed_count', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Orientadores más activos
     */
    private function getTopAdvisors(): Collection
    {
        $advisors = User::where('role_id', 2)
            ->where('active', true)
            ->with('institution')
            ->withCount('assignedTests')
            ->orderBy('assigned_tests_count', 'desc')
            ->limit(5)
            ->get();

        // Calcular grupos y usuarios manualmente
        foreach ($advisors as $advisor) {
            // Contar grupos del orientador
            $advisor->groups_count = Group::where('creator_id', $advisor->id)
                ->where('active', true)
                ->count();
            
            // Contar usuarios únicos en sus grupos
            $advisor->total_users = \DB::table('group_user')
                ->join('groups', 'group_user.group_id', '=', 'groups.id')
                ->where('groups.creator_id', $advisor->id)
                ->distinct()
                ->count('group_user.user_id');
        }

        return $advisors;
    }

    /**
     * Resultados preocupantes recientes
     */
    private function getConcerningResults(): Collection
    {
        $concerningCategories = ['severa', 'moderada'];

        return TestResponse::where('completed', true)
            ->where(function ($q) use ($concerningCategories) {
                foreach ($concerningCategories as $category) {
                    $q->orWhere('result_category', 'like', "%{$category}%");
                }
            })
            ->with(['user', 'assignment.test'])
            ->orderBy('finished_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Tests completados recientemente
     */
    private function getRecentCompletions(): Collection
    {
        return TestResponse::where('completed', true)
            ->with(['user', 'assignment.test', 'assignment.assignedBy'])
            ->orderBy('finished_at', 'desc')
            ->limit(10)
            ->get();
    }
}