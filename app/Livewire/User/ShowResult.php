<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\TestResponse;
use App\Models\ResponseDetail;
use Illuminate\Support\Collection;

class ShowResult extends Component
{
    public int $responseId;
    public ?TestResponse $response = null;
    public Collection $details;
    public array $scoreBySection = [];
    public bool $canRetake = false;
    public ?string $recommendation = null;

    public function mount(int $responseId)
    {
        $this->responseId = $responseId;
        $this->response = TestResponse::with([
            'assignment.test.questions.answerOptions',
            'assignment.assignedBy',
            'user',
            'details.question',
            'details.answerOption'
        ])->findOrFail($responseId);

        // Verificar permisos
        $currentUser = auth()->user();
        
        // Admin puede ver todo
        if ($currentUser->role_id === 1) {
            // Admin tiene acceso total
        } 
        // Orientador solo ve sus usuarios
        elseif ($currentUser->role_id === 2) {
            $userBelongsToAdvisor = $this->response->user->groups()
                ->where('creator_id', $currentUser->id)
                ->exists();

            if (!$userBelongsToAdvisor) {
                abort(403, 'No tienes permiso para ver este resultado.');
            }
        }
        // Otros roles no tienen acceso
        else {
            abort(403, 'No tienes permiso para ver este resultado.');
        }

        $this->details = $this->response->details;
        $this->calculateScoreBySection();
        $this->loadUserHistory();
        $this->recommendation = $this->generateRecommendation();
        $this->trend = $this->calculateTrend();
    }

    /**
     * Calcular puntaje por sección si aplica
     */
    private function calculateScoreBySection(): void
    {
        $totalScore = 0;
        $questionCount = 0;

        foreach ($this->details as $detail) {
            $totalScore += $detail->answerOption->weight ?? 0;
            $questionCount++;
        }

        $this->scoreBySection = [
            'total' => $totalScore,
            'questions' => $questionCount,
            'average' => $questionCount > 0 ? round($totalScore / $questionCount, 2) : 0,
        ];
    }

    /**
     * Verificar si puede reintentar
     */
    private function checkCanRetake(): bool
    {
        $test = $this->response->assignment->test;
        
        if (!$test->minimum_retest_time) {
            return false;
        }

        $daysSinceTest = $this->response->finished_at->diffInDays(now());
        return $daysSinceTest >= $test->minimum_retest_time;
    }

    /**
     * Obtener recomendación basada en el resultado
     */
    private function generateRecommendation(): ?string
    {
        $category = strtolower($this->response->result_category ?? '');

        // Recomendaciones para Ansiedad
        if (str_contains($category, 'ansiedad')) {
            if (str_contains($category, 'severa') || str_contains($category, 'moderada')) {
                return 'Se recomienda consultar con un profesional de salud mental. Tu orientador puede ayudarte a encontrar el apoyo adecuado.';
            } elseif (str_contains($category, 'leve')) {
                return 'Considera practicar técnicas de relajación, ejercicio regular y mantener buenos hábitos de sueño. Habla con tu orientador si los síntomas persisten.';
            } else {
                return 'Continúa con tus hábitos saludables. Si notas cambios, no dudes en comunicarte con tu orientador.';
            }
        }

        // Recomendaciones para Depresión
        if (str_contains($category, 'depresión')) {
            if (str_contains($category, 'severa')) {
                return 'Es importante buscar apoyo profesional lo antes posible. Tu orientador puede guiarte en este proceso.';
            } elseif (str_contains($category, 'moderada')) {
                return 'Se recomienda hablar con tu orientador sobre opciones de apoyo profesional y estrategias de afrontamiento.';
            } elseif (str_contains($category, 'leve')) {
                return 'Mantén conexiones sociales, actividad física regular y una rutina saludable. Conversa con tu orientador si necesitas apoyo.';
            } else {
                return 'Mantén tus hábitos saludables actuales. El autocuidado es importante para tu bienestar.';
            }
        }

        // Recomendaciones para Autoestima
        if (str_contains($category, 'autoestima')) {
            if (str_contains($category, 'baja')) {
                return 'Considera trabajar en identificar tus fortalezas y logros. Tu orientador puede ayudarte con estrategias para mejorar tu autoestima.';
            } elseif (str_contains($category, 'alta')) {
                return 'Excelente nivel de autoestima. Continúa reconociendo tus logros y mantén una actitud positiva.';
            } else {
                return 'Tu nivel de autoestima es saludable. Sigue trabajando en tu desarrollo personal.';
            }
        }

        return 'Habla con tu orientador sobre estos resultados para obtener orientación personalizada.';
    }

    public function render()
    {
        return view('livewire.user.show-result');
    }
}