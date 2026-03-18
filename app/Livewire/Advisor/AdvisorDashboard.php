<?php

namespace App\Livewire\Advisor;

use Livewire\Component;
use App\Models\Group;
use App\Models\User;
use App\Models\TestAssignment;
use App\Models\TestResponse;
use Illuminate\Support\Collection;

class AdvisorDashboard extends Component
{
    public function render()
    {
        $advisor = auth()->user();

        // Obtener grupos del orientador
        $myGroups = Group::where('creator_id', $advisor->id)
            ->where('active', true)
            ->withCount('users')
            ->get();

        // Obtener usuarios de mis grupos
        $myUsers = $this->getMyUsers($advisor);

        // Estadísticas generales
        $stats = $this->getStatistics($advisor, $myUsers);

        // Tests completados recientemente
        $recentCompletions = $this->getRecentCompletions($myUsers);

        // Usuarios que requieren atención
        $usersNeedingAttention = $this->getUsersNeedingAttention($myUsers);

        // Tests pendientes por vencer
        $upcomingDeadlines = $this->getUpcomingDeadlines($advisor);

        return view('livewire.advisor.advisor-dashboard', [
            'myGroups' => $myGroups,
            'stats' => $stats,
            'recentCompletions' => $recentCompletions,
            'usersNeedingAttention' => $usersNeedingAttention,
            'upcomingDeadlines' => $upcomingDeadlines,
        ]);
    }

    /**
     * Obtener usuarios de mis grupos
     */
    private function getMyUsers($advisor): Collection
    {
        return User::whereHas('groups', function ($q) use ($advisor) {
            $q->where('creator_id', $advisor->id);
        })
        ->where('role_id', 3)
        ->where('active', true)
        ->distinct()
        ->get();
    }

    /**
     * Estadísticas generales
     */
    private function getStatistics($advisor, $myUsers): array
    {
        $userIds = $myUsers->pluck('id');

        // Tests completados
        $completedTests = TestResponse::whereIn('user_id', $userIds)
            ->where('completed', true)
            ->get();

        $completedThisMonth = $completedTests->filter(fn($r) => $r->finished_at->isCurrentMonth());

        // Tests asignados y pendientes
        $myAssignments = TestAssignment::where('assigned_by', $advisor->id)
            ->orWhereHas('group', function ($q) use ($advisor) {
                $q->where('creator_id', $advisor->id);
            })
            ->where('active', true)
            ->get();

        $totalAssigned = $myAssignments->sum(function ($assignment) {
            return $assignment->affected_users->count();
        });

        $totalCompleted = TestResponse::whereIn('test_assignment_id', $myAssignments->pluck('id'))
            ->where('completed', true)
            ->count();

        $pendingCount = $totalAssigned - $totalCompleted;

        return [
            'total_users' => $myUsers->count(),
            'total_groups' => Group::where('creator_id', $advisor->id)->where('active', true)->count(),
            'tests_completed' => $completedTests->count(),
            'tests_completed_this_month' => $completedThisMonth->count(),
            'tests_pending' => $pendingCount > 0 ? $pendingCount : 0,
            'avg_completion_rate' => $totalAssigned > 0 ? round(($totalCompleted / $totalAssigned) * 100, 1) : 0,
        ];
    }

    /**
     * Tests completados recientemente
     */
    private function getRecentCompletions($myUsers): Collection
    {
        $userIds = $myUsers->pluck('id');

        return TestResponse::whereIn('user_id', $userIds)
            ->where('completed', true)
            ->with(['user', 'assignment.test'])
            ->orderBy('finished_at', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Usuarios que requieren atención
     */
    private function getUsersNeedingAttention($myUsers): Collection
    {
        $userIds = $myUsers->pluck('id');

        $concerningCategories = [
            'severa',
            'moderada',
            'baja',
        ];

        return TestResponse::whereIn('user_id', $userIds)
            ->where('completed', true)
            ->where(function ($q) use ($concerningCategories) {
                foreach ($concerningCategories as $category) {
                    $q->orWhere('result_category', 'like', "%{$category}%");
                }
            })
            ->with(['user', 'assignment.test'])
            ->orderBy('finished_at', 'desc')
            ->limit(10)
            ->get()
            ->unique('user_id')
            ->take(5);
    }

    /**
     * Tests próximos a vencer
     */
    private function getUpcomingDeadlines($advisor): Collection
    {
        $myAssignments = TestAssignment::where(function ($q) use ($advisor) {
            $q->where('assigned_by', $advisor->id)
              ->orWhereHas('group', function ($q) use ($advisor) {
                  $q->where('creator_id', $advisor->id);
              });
        })
        ->where('active', true)
        ->whereNotNull('due_date')
        ->where('due_date', '>=', now())
        ->where('due_date', '<=', now()->addDays(7))
        ->with(['test', 'user', 'group'])
        ->orderBy('due_date', 'asc')
        ->get();

        // Filtrar solo los que tienen respuestas incompletas
        return $myAssignments->filter(function ($assignment) {
            $affectedUserIds = $assignment->affected_users->pluck('id');
            
            $completedCount = TestResponse::where('test_assignment_id', $assignment->id)
                ->whereIn('user_id', $affectedUserIds)
                ->where('completed', true)
                ->count();

            return $completedCount < $affectedUserIds->count();
        })->take(5);
    }
}