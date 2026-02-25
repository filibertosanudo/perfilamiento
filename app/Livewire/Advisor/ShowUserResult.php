<?php

namespace App\Livewire\Advisor;

use Livewire\Component;
use App\Models\TestResponse;
use App\Models\ResponseDetail;
use Illuminate\Support\Collection;

class ShowUserResult extends Component
{
    public int $responseId;
    public ?TestResponse $response = null;
    public Collection $details;
    public Collection $userHistory;
    public array $scoreBySection = [];
    public string $timeUsed = '';
    
    // Propiedades calculadas
    public string $recommendation = '';
    public ?array $trend = null;

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

        // Verificar que el orientador tiene acceso a este usuario
        $advisor = auth()->user();
        $userBelongsToAdvisor = $this->response->user->groups()
            ->where('creator_id', $advisor->id)
            ->exists();

        if (!$userBelongsToAdvisor) {
            abort(403, 'No tienes permiso para ver este resultado.');
        }

        $this->details = $this->response->details;
        $this->calculateScoreBySection();
        $this->loadUserHistory();
        $this->recommendation = $this->generateRecommendation();
        $this->trend = $this->calculateTrend();
        $this->timeUsed = $this->formatTimeUsed();
    }

    /**
     * Calcular puntaje por sección
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
     * Cargar historial del usuario en este test
     */
    private function loadUserHistory(): void
    {
        $this->userHistory = TestResponse::where('user_id', $this->response->user_id)
            ->whereHas('assignment', function ($q) {
                $q->where('test_id', $this->response->assignment->test_id);
            })
            ->where('completed', true)
            ->where('id', '!=', $this->response->id)
            ->with('assignment.test')
            ->orderBy('finished_at', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Generar recomendación basada en resultado
     */
    private function generateRecommendation(): string
    {
        $category = strtolower($this->response->result_category ?? '');

        if (str_contains($category, 'severa')) {
            return 'Atención prioritaria: Se recomienda contactar al usuario de inmediato y considerar referencia a profesional de salud mental.';
        } elseif (str_contains($category, 'moderada')) {
            return 'Seguimiento necesario: Programa una sesión individual para evaluar necesidades de apoyo.';
        } elseif (str_contains($category, 'leve')) {
            return 'Monitoreo regular: Mantén comunicación y ofrece recursos de bienestar.';
        } elseif (str_contains($category, 'baja')) {
            return 'Apoyo en autoestima: Considera actividades de desarrollo personal y refuerzo positivo.';
        } elseif (str_contains($category, 'alta') || str_contains($category, 'normal')) {
            return 'Estado saludable: Continúa con el seguimiento regular.';
        }

        return 'Revisa los detalles y evalúa si requiere seguimiento personalizado.';
    }

    /**
     * Calcular tendencia comparando con intento anterior
     */
    private function calculateTrend(): ?array
    {
        if ($this->userHistory->isEmpty()) {
            return null;
        }

        $previousResponse = $this->userHistory->first();
        $currentScore = $this->response->numeric_result;
        $previousScore = $previousResponse->numeric_result;

        $diff = $currentScore - $previousScore;
        $percentChange = $previousScore > 0 
            ? round(($diff / $previousScore) * 100, 1) 
            : 0;

        return [
            'direction' => $diff > 0 ? 'up' : ($diff < 0 ? 'down' : 'same'),
            'diff' => abs($diff),
            'percent' => abs($percentChange),
            'previous_score' => $previousScore,
            'previous_date' => $previousResponse->finished_at,
        ];
    }

    public function render()
    {
        return view('livewire.advisor.show-user-result');
    }

    /**
     * Formatear tiempo utilizado
     */
    private function formatTimeUsed(): string
    {
        $totalSeconds = $this->response->started_at->diffInSeconds($this->response->finished_at);
        
        if ($totalSeconds < 60) {
            return "Menos de 1 minuto";
        }
        
        $minutes = floor($totalSeconds / 60);
        $seconds = $totalSeconds % 60;
        
        if ($seconds == 0) {
            return $minutes . ($minutes == 1 ? ' minuto' : ' minutos');
        }
        
        return $minutes . ($minutes == 1 ? ' minuto' : ' minutos') . ' ' . $seconds . ' segundos';
    }
}