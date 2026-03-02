<?php

namespace App\Livewire\Advisor;

use Livewire\Component;
use App\Models\User;
use App\Models\Group;
use App\Models\Test;
use App\Models\TestResponse;
use Illuminate\Support\Collection;

class AdvisorStatistics extends Component
{
    public string $period = 'month'; // month, quarter, semester, year

    public function render()
    {
        $advisor = auth()->user();
        
        // IDs de mis usuarios
        $myUserIds = $this->getMyUserIds($advisor);

        // Estadísticas por test
        $testStats = $this->getTestStatistics($myUserIds);

        // Distribución de niveles
        $levelDistribution = $this->getLevelDistribution($myUserIds);

        // Tendencias mensuales (últimos 6 meses)
        $monthlyTrends = $this->getMonthlyTrends($myUserIds);

        // Comparativa por grupo
        $groupComparison = $this->getGroupComparison($advisor);

        // Estadísticas generales
        $generalStats = $this->getGeneralStats($myUserIds);

        return view('livewire.advisor.advisor-statistics', [
            'testStats' => $testStats,
            'levelDistribution' => $levelDistribution,
            'monthlyTrends' => $monthlyTrends,
            'groupComparison' => $groupComparison,
            'generalStats' => $generalStats,
        ]);
    }

    private function getMyUserIds($advisor): array
    {
        return User::whereHas('groups', function ($q) use ($advisor) {
            $q->where('creator_id', $advisor->id);
        })
        ->where('role_id', 3)
        ->where('active', true)
        ->pluck('id')
        ->toArray();
    }

    private function getTestStatistics($userIds): Collection
    {
        return Test::where('active', true)
            ->withCount([
                'assignments as total_completed' => function ($q) use ($userIds) {
                    $q->whereHas('responses', function ($q) use ($userIds) {
                        $q->whereIn('user_id', $userIds)->where('completed', true);
                    });
                }
            ])
            ->get()
            ->map(function ($test) use ($userIds) {
                $responses = TestResponse::whereIn('user_id', $userIds)
                    ->whereHas('assignment', function ($q) use ($test) {
                        $q->where('test_id', $test->id);
                    })
                    ->where('completed', true)
                    ->get();

                $avgScore = $responses->avg('numeric_result') ?? 0;

                // Categorizar en bajo, medio, alto según el puntaje
                $maxScore = $test->max_score > 0 ? $test->max_score : 100;
                $distribution = [
                    'bajo' => $responses->filter(fn($r) => ($r->numeric_result / $maxScore) * 100 <= 33)->count(),
                    'medio' => $responses->filter(fn($r) => ($r->numeric_result / $maxScore) * 100 > 33 && ($r->numeric_result / $maxScore) * 100 <= 66)->count(),
                    'alto' => $responses->filter(fn($r) => ($r->numeric_result / $maxScore) * 100 > 66)->count(),
                ];

                return [
                    'test' => $test->name,
                    'completed' => $responses->count(),
                    'average' => round($avgScore, 1),
                    'distribution' => $distribution,
                ];
            })
            ->filter(fn($stat) => $stat['completed'] > 0);
    }

    private function getLevelDistribution($userIds): array
    {
        $categories = ['mínima', 'leve', 'moderada', 'severa', 'normal', 'baja', 'alta'];
        
        $distribution = [];
        foreach ($categories as $category) {
            $count = TestResponse::whereIn('user_id', $userIds)
                ->where('completed', true)
                ->where('result_category', 'like', "%{$category}%")
                ->count();
            
            if ($count > 0) {
                $distribution[$category] = $count;
            }
        }

        return $distribution;
    }

    private function getMonthlyTrends($userIds): array
    {
        $months = [];
        $completed = [];
        $pending = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M');
            
            $completedCount = TestResponse::whereIn('user_id', $userIds)
                ->where('completed', true)
                ->whereMonth('finished_at', $date->month)
                ->whereYear('finished_at', $date->year)
                ->count();
            
            $completed[] = $completedCount;
            
            // Pendientes: asignados pero no completados en ese mes
            $assignedInMonth = \DB::table('test_assignments')
                ->whereMonth('assigned_at', $date->month)
                ->whereYear('assigned_at', $date->year)
                ->where('active', true)
                ->count();
            
            $pending[] = max(0, $assignedInMonth - $completedCount);
        }

        return [
            'labels' => $months,
            'completed' => $completed,
            'pending' => $pending,
        ];
    }

    private function getGroupComparison($advisor): Collection
    {
        return Group::where('creator_id', $advisor->id)
            ->where('active', true)
            ->withCount('users')
            ->get()
            ->map(function ($group) {
                $userIds = $group->users->pluck('id');
                
                $completedTests = TestResponse::whereIn('user_id', $userIds)
                    ->where('completed', true)
                    ->count();
                
                $avgScore = TestResponse::whereIn('user_id', $userIds)
                    ->where('completed', true)
                    ->avg('numeric_result') ?? 0;

                return [
                    'name' => $group->name,
                    'users' => $group->users_count,
                    'completed' => $completedTests,
                    'average' => round($avgScore, 1),
                ];
            });
    }

    private function getGeneralStats($userIds): array
    {
        $totalCompleted = TestResponse::whereIn('user_id', $userIds)
            ->where('completed', true)
            ->count();

        $thisMonth = TestResponse::whereIn('user_id', $userIds)
            ->where('completed', true)
            ->whereMonth('finished_at', now()->month)
            ->count();

        $avgTime = TestResponse::whereIn('user_id', $userIds)
            ->where('completed', true)
            ->get()
            ->avg(function ($response) {
                return $response->started_at->diffInMinutes($response->finished_at);
            });

        $totalAssignments = \DB::table('test_assignments')
            ->where('assigned_by', auth()->id())
            ->where('active', true)
            ->count();

        $completionRate = $totalAssignments > 0 
            ? round(($totalCompleted / $totalAssignments) * 100, 1) 
            : 0;

        return [
            'total_completed' => $totalCompleted,
            'this_month' => $thisMonth,
            'avg_time' => round($avgTime ?? 0),
            'completion_rate' => $completionRate,
            'active_users' => count($userIds),
        ];
    }
}