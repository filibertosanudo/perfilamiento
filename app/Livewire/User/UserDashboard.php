<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\TestAssignment;
use App\Models\TestResponse;
use App\Models\ResponseDetail;
use Illuminate\Support\Collection;

class UserDashboard extends Component
{
    public bool $showDetailModal = false;
    public ?TestResponse $selectedResult = null;
    public $selectedDetails = [];
    public function render()
    {
        $user = auth()->user();

        // Obtener todas las asignaciones activas del usuario
        $assignments = $this->getUserAssignments($user);

        // Clasificar asignaciones
        $pendingTests = $this->getPendingTests($assignments, $user);
        $inProgressTests = $this->getInProgressTests($assignments, $user);
        $completedTests = $this->getCompletedTests($user);

        // Estadísticas
        $stats = [
            'pending_count' => $pendingTests->count(),
            'in_progress_count' => $inProgressTests->count(),
            'completed_count' => $completedTests->count(),
            'completed_this_month' => $completedTests->filter(function ($response) {
                return $response->finished_at && $response->finished_at->isCurrentMonth();
            })->count(),
        ];

        // Próxima fecha de vencimiento
        $nextDueDate = $pendingTests->sortBy(function ($item) {
            return $item['assignment']->due_date;
        })->first();

        $completedTestsCount = TestResponse::where('user_id', auth()->id())
            ->where('completed', true)
            ->count();

        $quotes = [
            "Cada pequeño paso te acerca a tu mejor versión.",
            "Tus emociones son válidas, siempre.",
            "Pedir ayuda es un acto de gran valentía.",
            "Tómate un respiro, estás haciendo un gran trabajo.",
            "Tu bienestar mental es la base de todo lo que construyes.",
        ];
        $motivationalQuote = $quotes[array_rand($quotes)];

        return view('livewire.user.user-dashboard', [
            'stats' => $stats,
            'pendingTests' => $pendingTests,
            'inProgressTests' => $inProgressTests,
            'completedTests' => $completedTests->take(5), // Últimos 5
            'nextDueDate' => $nextDueDate,
            'completedTestsCount' => $completedTestsCount,
            'motivationalQuote' => $motivationalQuote,
        ]);
    }

    /**
     * Obtener todas las asignaciones activas del usuario
     */
    private function getUserAssignments($user): Collection
    {
        // Asignaciones individuales
        $individualAssignments = TestAssignment::where('user_id', $user->id)
            ->where('active', true)
            ->with('test')
            ->get();

        // Asignaciones por grupo
        $groupIds = $user->groups->pluck('id');
        $groupAssignments = TestAssignment::whereIn('group_id', $groupIds)
            ->where('active', true)
            ->with('test')
            ->get();

        // Asignaciones por área
        $areaAssignments = TestAssignment::where('area_id', $user->area_id)
            ->where('active', true)
            ->with('test')
            ->get();

        return $individualAssignments
            ->merge($groupAssignments)
            ->merge($areaAssignments)
            ->unique('id');
    }

    /**
     * Tests pendientes (sin empezar)
     */
    private function getPendingTests(Collection $assignments, $user): Collection
    {
        return $assignments->filter(function ($assignment) use ($user) {
            // Verificar si el usuario ya tiene una respuesta
            $response = TestResponse::where('test_assignment_id', $assignment->id)
                ->where('user_id', $user->id)
                ->first();

            return !$response; // No ha empezado
        })->map(function ($assignment) {
            return [
                'assignment' => $assignment,
                'test' => $assignment->test,
            ];
        })->values();
    }

    /**
     * Tests en progreso (empezados pero no completados)
     */
    private function getInProgressTests(Collection $assignments, $user): Collection
    {
        return $assignments->map(function ($assignment) use ($user) {
            $response = TestResponse::where('test_assignment_id', $assignment->id)
                ->where('user_id', $user->id)
                ->where('completed', false)
                ->first();

            if ($response) {
                return [
                    'assignment' => $assignment,
                    'test' => $assignment->test,
                    'response' => $response,
                    'progress' => $response->progress,
                ];
            }

            return null;
        })->filter()->values();
    }

    /**
     * Tests completados
     */
    private function getCompletedTests($user): Collection
    {
        return TestResponse::where('user_id', $user->id)
            ->where('completed', true)
            ->with(['assignment.test'])
            ->orderBy('finished_at', 'desc')
            ->get();
    }

    /**
     * Verificar si el usuario puede reintentar un test
     */
    public function canRetakeTest($testId): bool
    {
        $user = auth()->user();
        $test = \App\Models\Test::find($testId);

        if (!$test || !$test->minimum_retest_time) {
            return false;
        }

        $lastResponse = TestResponse::where('user_id', $user->id)
            ->whereHas('assignment', function ($q) use ($testId) {
                $q->where('test_id', $testId);
            })
            ->where('completed', true)
            ->orderBy('finished_at', 'desc')
            ->first();

        if (!$lastResponse) {
            return true;
        }

        $daysSinceLastTest = $lastResponse->finished_at->diffInDays(now());
        return $daysSinceLastTest >= $test->minimum_retest_time;
    }

    public function showResultDetails(int $responseId)
    {
        $this->selectedResult = TestResponse::with([
            'assignment.test',
            'assignment.assignedBy'
        ])->findOrFail($responseId);

        if ($this->selectedResult->user_id !== auth()->id()) {
            abort(403);
        }

        $this->selectedDetails = ResponseDetail::where('test_response_id', $responseId)
            ->with(['question', 'answerOption'])
            ->get();

        $this->showDetailModal = true;
    }

    public function closeModal()
    {
        $this->showDetailModal = false;
        $this->selectedResult = null;
        $this->selectedDetails = [];
    }
}