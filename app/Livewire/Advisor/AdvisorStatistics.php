<?php

namespace App\Livewire\Advisor;

use Livewire\Component;
use App\Models\User;
use App\Models\Group;
use App\Models\Test;
use App\Models\TestResponse;
use App\Models\TestAssignment;
use Illuminate\Support\Collection;
use Carbon\Carbon;

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

        // Tendencias mensuales (basadas en el período seleccionado)
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

    private function getDateRange(): array
    {
        $endDate = now();
        
        $startDate = match($this->period) {
            'month' => now()->subMonth(),
            'quarter' => now()->subMonths(3),
            'semester' => now()->subMonths(6),
            'year' => now()->subYear(),
            default => now()->subMonth(),
        };

        return [$startDate, $endDate];
    }

    private function getTestStatistics($userIds): Collection
    {
        [$startDate, $endDate] = $this->getDateRange();

        return Test::where('active', true)
            ->get()
            ->map(function ($test) use ($userIds, $startDate, $endDate) {
                $responses = TestResponse::whereIn('user_id', $userIds)
                    ->whereHas('assignment', function ($q) use ($test) {
                        $q->where('test_id', $test->id);
                    })
                    ->where('completed', true)
                    ->whereBetween('finished_at', [$startDate, $endDate])
                    ->get();

                if ($responses->isEmpty()) {
                    return null;
                }

                $avgScore = $responses->avg('numeric_result') ?? 0;

                // Categorizar en bajo, medio, alto según el puntaje máximo del test
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
            ->filter();
    }

    private function getLevelDistribution($userIds): array
    {
        [$startDate, $endDate] = $this->getDateRange();
        
        // Mapeo de categorías específicas
        $categoryMapping = [
            'mínima' => ['mínima', 'Ansiedad mínima', 'Depresión mínima'],
            'leve' => ['leve', 'Ansiedad leve', 'Depresión leve'],
            'moderada' => ['moderada', 'moderadamente', 'Ansiedad moderada', 'Depresión moderada', 'Depresión moderadamente severa'],
            'severa' => ['severa', 'Ansiedad severa', 'Depresión severa'],
            'normal' => ['normal', 'Autoestima normal'],
            'baja' => ['baja', 'Autoestima baja'],
            'alta' => ['alta', 'Autoestima alta'],
        ];
        
        $distribution = [];
        
        foreach ($categoryMapping as $mainCategory => $variants) {
            $count = TestResponse::whereIn('user_id', $userIds)
                ->where('completed', true)
                ->whereBetween('finished_at', [$startDate, $endDate])
                ->where(function ($q) use ($variants) {
                    foreach ($variants as $variant) {
                        $q->orWhere('result_category', 'like', "%{$variant}%");
                    }
                })
                ->count();
            
            if ($count > 0) {
                $distribution[$mainCategory] = $count;
            }
        }

        return $distribution;
    }

    private function getMonthlyTrends($userIds): array
    {
        // Determinar número de meses según el período
        $monthsCount = match($this->period) {
            'month' => 4,      // 4 semanas
            'quarter' => 3,    // 3 meses
            'semester' => 6,   // 6 meses
            'year' => 12,      // 12 meses
            default => 6,
        };

        $months = [];
        $completed = [];
        $pending = [];

        for ($i = $monthsCount - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            
            // Formato de etiqueta según período
            if ($this->period === 'month') {
                $months[] = 'S' . ceil($date->day / 7); // Semana
            } else {
                $months[] = $date->format('M'); // Mes abreviado
            }
            
            // Completados en ese período
            $completedCount = TestResponse::whereIn('user_id', $userIds)
                ->where('completed', true)
                ->whereMonth('finished_at', $date->month)
                ->whereYear('finished_at', $date->year)
                ->count();
            
            $completed[] = $completedCount;
            
            // Pendientes: asignaciones activas de ese mes que NO han sido completadas
            $assignedUserIds = TestAssignment::whereIn('user_id', $userIds)
                ->whereMonth('assigned_at', $date->month)
                ->whereYear('assigned_at', $date->year)
                ->where('active', true)
                ->pluck('user_id')
                ->unique();

            $completedUserIds = TestResponse::whereIn('user_id', $assignedUserIds)
                ->where('completed', true)
                ->whereMonth('finished_at', $date->month)
                ->whereYear('finished_at', $date->year)
                ->pluck('user_id')
                ->unique();

            $pendingCount = $assignedUserIds->diff($completedUserIds)->count();
            $pending[] = $pendingCount;
        }

        return [
            'labels' => $months,
            'completed' => $completed,
            'pending' => $pending,
        ];
    }

    private function getGroupComparison($advisor): Collection
    {
        [$startDate, $endDate] = $this->getDateRange();

        return Group::where('creator_id', $advisor->id)
            ->where('active', true)
            ->withCount('users')
            ->get()
            ->map(function ($group) use ($startDate, $endDate) {
                $userIds = $group->users->pluck('id');
                
                $completedTests = TestResponse::whereIn('user_id', $userIds)
                    ->where('completed', true)
                    ->whereBetween('finished_at', [$startDate, $endDate])
                    ->count();
                
                $avgScore = TestResponse::whereIn('user_id', $userIds)
                    ->where('completed', true)
                    ->whereBetween('finished_at', [$startDate, $endDate])
                    ->avg('numeric_result') ?? 0;

                return [
                    'name' => $group->name,
                    'users' => $group->users_count,
                    'completed' => $completedTests,
                    'average' => round($avgScore, 1),
                ];
            })
            ->filter(fn($g) => $g['users'] > 0);
    }

    private function getGeneralStats($userIds): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        $totalCompleted = TestResponse::whereIn('user_id', $userIds)
            ->where('completed', true)
            ->whereBetween('finished_at', [$startDate, $endDate])
            ->count();

        $thisMonth = TestResponse::whereIn('user_id', $userIds)
            ->where('completed', true)
            ->whereMonth('finished_at', now()->month)
            ->whereYear('finished_at', now()->year)
            ->count();

        // Calcular tiempo promedio correctamente (solo respuestas con ambas fechas)
        $avgTime = TestResponse::whereIn('user_id', $userIds)
            ->where('completed', true)
            ->whereBetween('finished_at', [$startDate, $endDate])
            ->whereNotNull('started_at')
            ->whereNotNull('finished_at')
            ->get()
            ->filter(function($response) {
                // Filtrar respuestas con tiempos razonables (menos de 2 horas)
                $minutes = $response->started_at->diffInMinutes($response->finished_at);
                return $minutes > 0 && $minutes < 120;
            })
            ->avg(function ($response) {
                return $response->started_at->diffInMinutes($response->finished_at);
            });

        // Total de asignaciones activas del orientador en el período
        $totalAssignments = TestAssignment::where('assigned_by', auth()->id())
            ->where('active', true)
            ->whereBetween('assigned_at', [$startDate, $endDate])
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