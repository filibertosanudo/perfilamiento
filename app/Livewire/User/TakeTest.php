<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\TestAssignment;
use App\Models\TestResponse;
use App\Models\ResponseDetail;
use App\Models\Question;
use Illuminate\Support\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; 

class TakeTest extends Component
{
    use AuthorizesRequests;
        
    public int $assignmentId;
    public ?TestAssignment $assignment = null;
    public ?TestResponse $response = null;
    public Collection $questions;
    public int $currentQuestionIndex = 0;
    public ?Question $currentQuestion = null;
    public array $answers = [];

    // Estado del test
    public bool $isStarted = false;
    public bool $isCompleted = false;
    public float $progress = 0;

    public function mount(int $assignmentId)
    {
        $this->assignmentId = $assignmentId;
        $this->assignment = TestAssignment::with(['test.questions.answerOptions'])->findOrFail($assignmentId);

        // Verificar que el usuario tiene acceso
        $this->authorize('take', $this->assignment);

        // Verificar si ya existe una respuesta
        $this->response = TestResponse::where('test_assignment_id', $this->assignmentId)
            ->where('user_id', auth()->id())
            ->first();

        // Cargar preguntas ordenadas
        $this->questions = $this->assignment->test->questions;

        // Si ya tiene respuesta, cargar progreso
        if ($this->response) {
            $this->loadProgress();
        }
    }

    /**
     * Iniciar el test
     */
    public function startTest(): void
    {
        if (!$this->response) {
            $this->response = TestResponse::create([
                'test_assignment_id' => $this->assignmentId,
                'user_id' => auth()->id(),
                'started_at' => now(),
                'completed' => false,
            ]);

            \Log::info('Usuario inició test', [
                'user_id' => auth()->id(),
                'test_id' => $this->assignment->test_id,
                'assignment_id' => $this->assignmentId,
            ]);
        }

        $this->isStarted = true;
        $this->currentQuestion = $this->questions[$this->currentQuestionIndex];
    }

    /**
     * Cargar progreso existente
     */
    private function loadProgress(): void
    {
        $savedAnswers = ResponseDetail::where('test_response_id', $this->response->id)
            ->get()
            ->keyBy('question_id');

        foreach ($savedAnswers as $questionId => $detail) {
            $this->answers[$questionId] = $detail->answer_option_id;
        }

        $this->progress = $this->response->progress;
        $this->isStarted = true;

        // Si está completado
        if ($this->response->completed) {
            $this->isCompleted = true;
        } else {
            // Encontrar la primera pregunta sin responder
            $answeredQuestionIds = array_keys($this->answers);
            $this->currentQuestionIndex = 0;

            foreach ($this->questions as $index => $question) {
                if (!in_array($question->id, $answeredQuestionIds)) {
                    $this->currentQuestionIndex = $index;
                    break;
                }
            }

            $this->currentQuestion = $this->questions[$this->currentQuestionIndex];
        }
    }

    /**
     * Guardar respuesta y avanzar
     */
    public function answerQuestion(int $answerOptionId): void
    {
        $questionId = $this->currentQuestion->id;

        // Guardar/actualizar respuesta
        ResponseDetail::updateOrCreate(
            [
                'test_response_id' => $this->response->id,
                'question_id' => $questionId,
            ],
            [
                'answer_option_id' => $answerOptionId,
                'answered_at' => now(),
            ]
        );

        // Actualizar array local
        $this->answers[$questionId] = $answerOptionId;

        // Calcular progreso
        $this->progress = (count($this->answers) / $this->questions->count()) * 100;

        // Verificar si es la última pregunta
        if ($this->currentQuestionIndex < $this->questions->count() - 1) {
            $this->nextQuestion();
        } else {
            $this->completeTest();
        }
    }

    /**
     * Siguiente pregunta
     */
    public function nextQuestion(): void
    {
        if ($this->currentQuestionIndex < $this->questions->count() - 1) {
            $this->currentQuestionIndex++;
            $this->currentQuestion = $this->questions[$this->currentQuestionIndex];
        }
    }

    /**
     * Pregunta anterior
     */
    public function previousQuestion(): void
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
            $this->currentQuestion = $this->questions[$this->currentQuestionIndex];
        }
    }

    /**
     * Completar test y calcular resultado
     */
    private function completeTest(): void
    {
        $totalScore = 0;

        // Calcular puntaje total
        foreach ($this->answers as $questionId => $answerOptionId) {
            $answerOption = \App\Models\AnswerOption::find($answerOptionId);
            $totalScore += $answerOption->weight ?? 0;
        }

        // Determinar categoría según el test
        $resultCategory = $this->determineResultCategory($totalScore);

        // Actualizar respuesta
        $this->response->update([
            'finished_at' => now(),
            'completed' => true,
            'numeric_result' => $totalScore,
            'result_category' => $resultCategory,
        ]);

        $this->isCompleted = true;

        \Log::info('Usuario completó test', [
            'user_id' => auth()->id(),
            'test_id' => $this->assignment->test_id,
            'assignment_id' => $this->assignmentId,
            'score' => $totalScore,
            'category' => $resultCategory,
        ]);

        session()->flash('test_completed', true);
    }

    /**
     * Determinar categoría del resultado según el test
     */
    private function determineResultCategory(float $score): string
    {
        $testName = $this->assignment->test->name;

        // GAD-7 (Ansiedad)
        if (str_contains($testName, 'GAD-7') || str_contains($testName, 'Ansiedad')) {
            if ($score <= 4) return 'Ansiedad mínima';
            if ($score <= 9) return 'Ansiedad leve';
            if ($score <= 14) return 'Ansiedad moderada';
            return 'Ansiedad severa';
        }

        // PHQ-9 (Depresión)
        if (str_contains($testName, 'PHQ-9') || str_contains($testName, 'Depresión')) {
            if ($score <= 4) return 'Depresión mínima';
            if ($score <= 9) return 'Depresión leve';
            if ($score <= 14) return 'Depresión moderada';
            if ($score <= 19) return 'Depresión moderadamente severa';
            return 'Depresión severa';
        }

        // Rosenberg (Autoestima)
        if (str_contains($testName, 'Rosenberg') || str_contains($testName, 'Autoestima')) {
            if ($score < 25) return 'Autoestima baja';
            if ($score <= 30) return 'Autoestima normal';
            return 'Autoestima alta';
        }

        return 'Resultado normal';
    }

    /**
     * Guardar y salir
     */
    public function saveAndExit()
    {
        session()->flash('message', 'Progreso guardado. Puedes continuar más tarde.');
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.user.take-test');
    }
}