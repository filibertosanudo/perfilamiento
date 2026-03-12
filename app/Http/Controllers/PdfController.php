<?php

namespace App\Http\Controllers;

use App\Models\TestResponse;
use App\Models\User;
use App\Models\Test;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TestAssignment;
use App\Models\Group;         
use App\Models\Area;   
use Illuminate\Support\Str;   

class PdfController extends Controller
{
    protected $pdfService;

    public function __construct(PdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     * Descargar PDF de resultado individual de test
     */
    public function downloadTestResult($responseId)
    {
        $response = TestResponse::with(['assignment.test', 'user.area'])
            ->findOrFail($responseId);

        // Verificar permisos
        $user = Auth::user();
        if ($user->role_id === 3 && $response->user_id !== $user->id) {
            abort(403, 'No tienes permiso para acceder a este resultado.');
        } elseif ($user->role_id === 2) {
            // Un orientador solo puede ver el resultado si él realizó la asignación
            // o si el test fue asignado a un grupo que él administra.
            $hasAccess = $response->assignment->assigned_by === $user->id
                || ($response->assignment->group_id && $response->assignment->group->creator_id === $user->id);
                
            if (!$hasAccess) {
                abort(403, 'No tienes permiso para acceder a este resultado específico.');
            }
        }

        $data = [
            'response' => $response,
            'test' => $response->assignment->test,
            'user' => $response->user,
            'institution' => $response->user->area->name ?? 'Evaluación Individual',
            'title' => 'Resultado de Test - ' . $response->user->full_name,
        ];

        $filename = 'resultado_' 
            . Str::slug($response->assignment->test->name, '_') 
            . '_' 
            . Str::slug($response->user->first_name, '_') 
            . '_' 
            . now()->format('Y-m-d') 
            . '.pdf';

        return $this->pdfService->downloadFromView(
            'pdfs.test-result',
            $data,
            $filename
        );
    }

    /**
     * Descargar PDF de historial completo del usuario
     */
    public function downloadUserHistory($userId = null)
    {
        $user = Auth::user();
        
        // Si es usuario normal, solo puede descargar su propio historial
        if ($user->role_id === 3) {
            $userId = $user->id;
        } else {
            // Admin u orientador puede especificar el usuario
            $userId = $userId ?? $user->id;
        }

        $targetUser = User::with(['area'])->findOrFail($userId);

        // Obtener respuestas accesibles para el orientador
        $responsesQuery = TestResponse::with(['assignment.test'])
            ->where('user_id', $userId)
            ->where('completed', true);

        if ($user->role_id === 2) {
            // Filtrar solo las respuestas que el orientador tiene permiso de ver
            $responsesQuery->where(function($q) use ($user) {
                $q->whereHas('assignment', function($sub) use ($user) {
                    $sub->where('assigned_by', $user->id);
                })
                ->orWhereHas('assignment.group', function($sub) use ($user) {
                    $sub->where('creator_id', $user->id);
                });
            });
        }

        $responses = $responsesQuery->orderBy('finished_at', 'desc')->get();

        if ($user->role_id === 2 && $responses->isEmpty()) {
            abort(403, 'No tienes acceso a ninguna evaluación de este usuario.');
        }

        $data = [
            'user' => $targetUser,
            'responses' => $responses,
            'institution' => $targetUser->area->name ?? 'Historial Académico',
            'title' => 'Historial Académico - ' . $targetUser->full_name,
        ];

        $filename = 'historial_' 
            . Str::slug($targetUser->first_name, '_') 
            . '_' 
            . Str::slug($targetUser->last_name, '_') 
            . '_' 
            . now()->format('Y-m-d') 
            . '.pdf';

        return $this->pdfService->streamFromView(
            'pdfs.user-history',
            $data,
            $filename,
            ['landscape' => true]
        );
    }

    /**
     * Descargar PDF de estadísticas del orientador
     */
    public function downloadAdvisorStatistics(Request $request)
    {
        $user = Auth::user();

        if ($user->role_id !== 2) {
            abort(403, 'Solo los orientadores pueden generar este reporte.');
        }

        // Obtener el componente Livewire de estadísticas
        $advisorStats = app(\App\Livewire\Advisor\AdvisorStatistics::class);
        $advisorStats->period = $request->input('period', 'month');

        // Obtener los datos renderizados
        $data = $advisorStats->render()->getData();

        // Agregar información del orientador
        $data['advisor'] = $user;
        $data['period'] = $advisorStats->period;
        $data['generated_at'] = now();
        $data['institution'] = $user->area->name ?? 'Reporte Orientador';
        $data['title'] = 'Estadísticas del Orientador';

        $filename = 'estadisticas_orientador_' 
            . Str::slug($user->first_name, '_') 
            . '_' 
            . now()->format('Y-m-d') 
            . '.pdf';

        return $this->pdfService->downloadFromView(
            'pdfs.advisor-statistics',
            $data,
            $filename,
            [
                'landscape' => true,
                'margin' => [
                    'top' => 15,
                    'right' => 10,
                    'bottom' => 15,
                    'left' => 10,
                ],
            ]
        );
    }

    /**
     * Descargar PDF de reporte de grupo
     */
    public function downloadGroupReport($groupId)
    {
        $user = Auth::user();

        $group = \App\Models\Group::with(['users', 'creator', 'area'])
            ->findOrFail($groupId);

        // Verificar permisos
        if ($user->role_id === 2 && $group->creator_id !== $user->id) {
            abort(403, 'No tienes permiso para generar este reporte.');
        }

        $userIds = $group->users->pluck('id')->toArray();

        // Estadísticas del grupo
        $stats = [
            'total_users' => count($userIds),
            'total_completed' => \App\Models\TestResponse::whereIn('user_id', $userIds)
                ->where('completed', true)
                ->count(),
            'avg_score' => \App\Models\TestResponse::whereIn('user_id', $userIds)
                ->where('completed', true)
                ->avg('numeric_result'),
        ];

        // Tests completados por test
        $testStats = \App\Models\Test::where('active', true)
            ->get()
            ->map(function ($test) use ($userIds) {
                $responses = \App\Models\TestResponse::whereIn('user_id', $userIds)
                    ->whereHas('assignment', function ($q) use ($test) {
                        $q->where('test_id', $test->id);
                    })
                    ->where('completed', true)
                    ->get();

                return [
                    'test' => $test->name,
                    'completed' => $responses->count(),
                    'average' => round($responses->avg('numeric_result'), 1),
                ];
            })
            ->filter(fn($stat) => $stat['completed'] > 0);

        $data = [
            'group' => $group,
            'stats' => $stats,
            'testStats' => $testStats,
            'advisor' => $user,
            'institution' => $group->area->name ?? 'Reporte de Grupo',
            'title' => 'Reporte de Grupo - ' . $group->name,
        ];

        $filename = 'reporte_grupo_' 
            . Str::slug($group->name, '_') 
            . '_' 
            . now()->format('Y-m-d') 
            . '.pdf';

        return $this->pdfService->downloadFromView(
            'pdfs.group-report',
            $data,
            $filename,
            ['landscape' => true]
        );
    }

    /**
     * Descargar PDF de reporte integral del usuario
     */
    public function downloadUserIntegralReport($userId = null)
    {
        $user = Auth::user();
        
        // Si es usuario normal, solo puede descargar su propio reporte
        if ($user->role_id === 3) {
            $userId = $user->id;
        } else {
            $userId = $userId ?? $user->id;
        }

        $targetUser = User::with(['area'])->findOrFail($userId);

        // Obtener solo las respuestas que el orientador tiene permiso de ver
        $responsesQuery = TestResponse::with(['assignment.test'])
            ->where('user_id', $userId)
            ->where('completed', true);

        if ($user->role_id === 2) {
            $responsesQuery->where(function($q) use ($user) {
                $q->whereHas('assignment', function($sub) use ($user) {
                    $sub->where('assigned_by', $user->id);
                })
                ->orWhereHas('assignment.group', function($sub) use ($user) {
                    $sub->where('creator_id', $user->id);
                });
            });
        }

        $responses = $responsesQuery->orderBy('finished_at', 'desc')->get();

        // Verificar que tenga al menos 3 tests accesibles
        if ($responses->count() < 3) {
            abort(400, 'Se requieren al menos 3 evaluaciones completadas y autorizadas para generar el reporte integral.');
        }

        // Calcular período
        $startDate = $responses->last()->finished_at;
        $endDate = $responses->first()->finished_at;

        // Agrupar por dimensión
        $dimensionData = [];
        $testsByDimension = [
            'Ansiedad' => ['GAD', 'Ansiedad'],
            'Depresión' => ['PHQ', 'Depresión'],
            'Autoestima' => ['Rosenberg', 'Autoestima'],
            'Estrés' => ['PSS', 'Estrés'],
            'Int. Emocional' => ['TMMS', 'Emocional'],
            'Resiliencia' => ['CD-RISC', 'Resiliencia'],
        ];

        foreach ($testsByDimension as $dimension => $keywords) {
            $dimensionResponses = $responses->filter(function ($response) use ($keywords) {
                foreach ($keywords as $keyword) {
                    if (str_contains($response->assignment->test->name, $keyword)) {
                        return true;
                    }
                }
                return false;
            });

            if ($dimensionResponses->isNotEmpty()) {
                $latestResponse = $dimensionResponses->first();
                $score = $latestResponse->numeric_result;
                $maxScore = $latestResponse->assignment->test->max_score;
                $percentage = ($score / $maxScore) * 100;

                // Determinar estado
                if ($percentage <= 33) {
                    $status = 'good';
                    $interpretation = 'Tus niveles en esta dimensión son óptimos y saludables.';
                } elseif ($percentage <= 66) {
                    $status = 'moderate';
                    $interpretation = 'Tus niveles en esta dimensión requieren atención. Considera estrategias de mejora.';
                } else {
                    $status = 'concern';
                    $interpretation = 'Tus niveles en esta dimensión son preocupantes. Se recomienda buscar apoyo profesional.';
                }

                // Invertir para autoestima
                if (str_contains($dimension, 'Autoestima')) {
                    if ($percentage > 66) {
                        $status = 'good';
                        $interpretation = 'Tu autoestima se encuentra en un nivel saludable y positivo.';
                    } elseif ($percentage > 33) {
                        $status = 'moderate';
                        $interpretation = 'Tu autoestima podría beneficiarse de trabajo en autoconocimiento y aceptación.';
                    } else {
                        $status = 'concern';
                        $interpretation = 'Tu autoestima está baja. Considera trabajar con un profesional en esta área.';
                    }
                }

                $dimensionData[] = [
                    'name' => $dimension,
                    'score' => $score,
                    'max' => $maxScore,
                    'status' => $status,
                    'interpretation' => $interpretation,
                ];
            }
        }

        // Estado general
        $concernCount = collect($dimensionData)->where('status', 'concern')->count();
        $moderateCount = collect($dimensionData)->where('status', 'moderate')->count();
        
        if ($concernCount > 0) {
            $overallStatus = 'concern';
        } elseif ($moderateCount > 1) {
            $overallStatus = 'moderate';
        } else {
            $overallStatus = 'good';
        }

        // Recomendaciones
        $recommendations = [];
        foreach ($dimensionData as $dim) {
            if ($dim['status'] !== 'good') {
                $recommendations[] = [
                    'area' => $dim['name'],
                    'action' => $this->getRecommendation($dim['name'], $dim['status']),
                ];
            }
        }

        $data = [
            'user' => $targetUser,
            'totalTests' => $responses->count(),
            'areasEvaluated' => count($dimensionData),
            'overallStatus' => $overallStatus,
            'dimensionData' => $dimensionData,
            'recommendations' => $recommendations,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'institution' => $targetUser->area->name ?? 'Reporte Integral',
            'title' => 'Reporte Integral - ' . $targetUser->full_name,
        ];

        $filename = 'reporte_integral_' 
            . Str::slug($targetUser->first_name, '_') 
            . '_' 
            . Str::slug($targetUser->last_name, '_') 
            . '_' 
            . now()->format('Y-m-d') 
            . '.pdf';

        return $this->pdfService->streamFromView(
            'pdfs.user-integral-report',
            $data,
            $filename
        );
    }

    private function getRecommendation(string $dimension, string $status): string
    {
        $recommendations = [
            'Ansiedad' => [
                'moderate' => 'Practica técnicas de respiración profunda y mindfulness diariamente. Considera establecer una rutina de ejercicio regular.',
                'concern' => 'Busca apoyo profesional de un psicólogo especializado en manejo de ansiedad. Las terapias cognitivo-conductuales han demostrado ser muy efectivas.',
            ],
            'Depresión' => [
                'moderate' => 'Mantén una rutina diaria estructurada, prioriza el sueño y la actividad física. Busca conexiones sociales significativas.',
                'concern' => 'Es importante que consultes con un profesional de salud mental lo antes posible. La terapia y, en algunos casos, medicación pueden ser muy beneficiosas.',
            ],
            'Autoestima' => [
                'moderate' => 'Practica la autocompasión y el diálogo interno positivo. Identifica y desafía pensamientos negativos sobre ti mismo.',
                'concern' => 'Trabaja con un terapeuta para explorar las raíces de tu baja autoestima y desarrollar una imagen más saludable de ti mismo.',
            ],
            'Estrés' => [
                'moderate' => 'Implementa técnicas de gestión del tiempo y establece límites saludables. Dedica tiempo diario a actividades relajantes.',
                'concern' => 'El estrés crónico requiere intervención. Consulta con un profesional para desarrollar estrategias de afrontamiento efectivas.',
            ],
            'Int. Emocional' => [
                'moderate' => 'Practica la identificación y el etiquetado de tus emociones. Lee sobre inteligencia emocional y aplica los conceptos en tu vida diaria.',
                'concern' => 'Considera trabajar con un terapeuta para desarrollar habilidades emocionales más sólidas.',
            ],
            'Resiliencia' => [
                'moderate' => 'Fortalece tu red de apoyo social y desarrolla una mentalidad de crecimiento. Aprende de los desafíos pasados.',
                'concern' => 'La baja resiliencia puede mejorar significativamente con intervención profesional. Busca apoyo terapéutico.',
            ],
        ];

        return $recommendations[$dimension][$status] ?? 'Consulta con un profesional para orientación personalizada.';
    }

    /**
     * Descargar PDF de dashboard global del admin
     */
    public function downloadAdminDashboard(Request $request)
    {
        $user = Auth::user();

        if ($user->role_id !== 1) {
            abort(403, 'Solo los administradores pueden generar este reporte.');
        }

        $period = $request->input('period', 'month');
        
        // Determinar rango de fechas
        $endDate = now();
        $startDate = match($period) {
            'month' => now()->subMonth(),
            'quarter' => now()->subMonths(3),
            'semester' => now()->subMonths(6),
            'year' => now()->subYear(),
            default => now()->subMonth(),
        };

        $period_label = match($period) {
            'month' => 'Último Mes',
            'quarter' => 'Último Trimestre',
            'semester' => 'Último Semestre',
            'year' => 'Último Año',
            default => 'Último Mes',
        };

        // Métricas generales
        $total_users = User::where('active', true)->count();
        $users_last_month = User::where('active', true)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->count();
        $users_growth = $total_users > 0 ? round(($users_last_month / $total_users) * 100, 1) : 0;

        $total_tests = TestResponse::where('completed', true)
            ->whereBetween('finished_at', [$startDate, $endDate])
            ->count();
        
        $tests_this_month = TestResponse::where('completed', true)
            ->whereMonth('finished_at', now()->month)
            ->count();
        $tests_last_month = TestResponse::where('completed', true)
            ->whereMonth('finished_at', now()->subMonth()->month)
            ->count();
        $tests_growth = $tests_last_month > 0 ? round((($tests_this_month - $tests_last_month) / $tests_last_month) * 100, 1) : 0;

        $total_assignments = TestAssignment::where('active', true)
            ->whereBetween('assigned_at', [$startDate, $endDate])
            ->distinct('user_id', 'test_id')
            ->count();

        $completion_rate = $total_assignments > 0 
            ? min(100, round(($total_tests / $total_assignments) * 100, 1)) 
            : 0;

        $active_areas = \App\Models\Area::where('active', true)->count();
        $total_areas = \App\Models\Area::count();

        $metrics = [
            'total_users' => $total_users,
            'users_growth' => $users_growth,
            'total_tests' => $total_tests,
            'tests_growth' => $tests_growth,
            'completion_rate' => $completion_rate,
            'completion_trend' => 0, // Placeholder
            'active_areas' => $active_areas,
            'total_areas' => $total_areas,
        ];

        // Estadísticas por área
        $areaStats = \App\Models\Area::where('active', true)
            ->get()
            ->map(function ($area) use ($startDate, $endDate) {
                $users = User::where('area_id', $area->id)
                    ->where('active', true)
                    ->count();
                
                $userIds = User::where('area_id', $area->id)
                    ->pluck('id');
                
                $tests = TestResponse::whereIn('user_id', $userIds)
                    ->where('completed', true)
                    ->whereBetween('finished_at', [$startDate, $endDate])
                    ->count();

                return [
                    'name' => $area->name,
                    'users' => $users,
                    'tests' => $tests,
                ];
            })
            ->sortByDesc('tests')
            ->values();

        // Distribución de tests
        $testDistribution = \App\Models\Test::where('active', true)
            ->get()
            ->map(function ($test) use ($startDate, $endDate) {
                $count = TestResponse::whereHas('assignment', function ($q) use ($test) {
                    $q->where('test_id', $test->id);
                })
                ->where('completed', true)
                ->whereBetween('finished_at', [$startDate, $endDate])
                ->count();

                $colors = [
                    'GAD' => '#F59E0B',
                    'PHQ' => '#DC2626',
                    'Rosenberg' => '#10B981',
                ];
                $color = '#6B7280';
                foreach($colors as $k => $c) {
                    if(str_contains($test->name, $k)) {
                        $color = $c;
                        break;
                    }
                }

                return [
                    'name' => $test->name,
                    'count' => $count,
                    'color' => $color,
                ];
            })
            ->filter(fn($t) => $t['count'] > 0)
            ->values();

        // Detalle por área
        $areaDetails = \App\Models\Area::where('active', true)
            ->get()
            ->map(function ($area) use ($startDate, $endDate) {
                $users = User::where('area_id', $area->id)
                    ->where('active', true)
                    ->count();
                
                $advisors = User::where('area_id', $area->id)
                    ->where('role_id', 2)
                    ->where('active', true)
                    ->count();
                
                $groups = \App\Models\Group::where('area_id', $area->id)
                    ->where('active', true)
                    ->count();
                
                $userIds = User::where('area_id', $area->id)->pluck('id');
                
                $completed = TestResponse::whereIn('user_id', $userIds)
                    ->where('completed', true)
                    ->whereBetween('finished_at', [$startDate, $endDate])
                    ->count();
                
                // CORRECCIÓN: Contar asignaciones correctamente
                $assigned = TestAssignment::whereIn('user_id', $userIds)
                    ->where('active', true)
                    ->whereBetween('assigned_at', [$startDate, $endDate])
                    ->count();
                
                // CORRECCIÓN: Asegurar que no supere 100%
                $completion_rate = $assigned > 0 
                    ? min(100, round(($completed / $assigned) * 100, 1)) 
                    : 0;

                // Determinar rendimiento
                if ($completion_rate >= 80) {
                    $performance = 'high';
                    $notes = 'Excelente participación y completación de tests.';
                } elseif ($completion_rate >= 50) {
                    $performance = 'medium';
                    $notes = 'Rendimiento aceptable, con margen de mejora.';
                } else {
                    $performance = 'low';
                    $notes = 'Se recomienda incentivar la participación activa.';
                }

                return [
                    'name' => $area->name,
                    'users' => $users,
                    'advisors' => $advisors,
                    'groups' => $groups,
                    'completion_rate' => $completion_rate,
                    'performance' => $performance,
                    'notes' => $notes,
                ];
            })
            ->sortByDesc('completion_rate')
            ->values();

        // Top Performers
        $topPerformers = $areaDetails->take(5)->map(function($areaData) use ($startDate, $endDate) {
            $userIds = User::where('area_id', \App\Models\Area::where('name', $areaData['name'])->first()->id ?? 0)
                ->pluck('id');
            
            $testsCompleted = TestResponse::whereIn('user_id', $userIds)
                ->where('completed', true)
                ->whereBetween('finished_at', [$startDate, $endDate])
                ->count();
            
            $avgScore = TestResponse::whereIn('user_id', $userIds)
                ->where('completed', true)
                ->whereBetween('finished_at', [$startDate, $endDate])
                ->avg('numeric_result');

            return [
                'name' => $areaData['name'],
                'tests_completed' => $testsCompleted,
                'completion_rate' => $areaData['completion_rate'],
                'avg_score' => round($avgScore ?? 0, 1),
            ];
        });

        // Recomendaciones
        $recommendations = [];
        
        if ($completion_rate < 60) {
            $recommendations[] = 'La tasa de completación general es baja. Considere implementar recordatorios automáticos más frecuentes.';
        }
        
        if ($areaDetails->where('performance', 'low')->count() > 0) {
            $recommendations[] = 'Algunas áreas necesitan apoyo adicional. Programe sesiones de capacitación para orientadores.';
        }
        
        if ($tests_growth < 5) {
            $recommendations[] = 'El crecimiento en evaluaciones es bajo. Promueva activamente el uso del sistema entre las áreas.';
        }
        
        if (empty($recommendations)) {
            $recommendations[] = 'El sistema opera de manera óptima. Continúe monitoreando las métricas regularmente.';
            $recommendations[] = 'Considere expandir el catálogo de tests para ofrecer evaluaciones más diversas.';
        }

        $data = [
            'period_label' => $period_label,
            'metrics' => $metrics,
            'areaStats' => $areaStats,
            'testDistribution' => $testDistribution,
            'areaDetails' => $areaDetails,
            'topPerformers' => $topPerformers,
            'recommendations' => $recommendations,
        ];

        $filename = 'dashboard_admin_global_' . now()->format('Y-m-d') . '.pdf';

        return $this->pdfService->downloadFromView(
            'pdfs.admin-dashboard',
            $data,
            $filename,
            ['landscape' => true]
        );
    }
}