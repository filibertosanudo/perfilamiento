<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Test;
use App\Models\Question;
use App\Models\AnswerOption;
use App\Models\TestRecommendation;

class TestManagement extends Component
{
    use WithPagination;

    // Search & filters
    public string $search = '';
    public bool $showInactive = false;
    public string $sortField = 'id';
    public string $sortDirection = 'desc';

    // Modal state
    public bool $isOpen = false;
    public bool $isViewMode = false;
    public int $currentStep = 1;

    // Step 1: Test Info
    public ?int $testId = null;
    public string $testName = '';
    public string $testDescription = '';
    public string $testObjective = '';
    public ?int $estimatedTime = null;
    public ?int $minimumRetestTime = null;
    public bool $testActive = true;

    // Step 2: Questions
    public array $questions = [];

    // Step 3: Scoring Ranges
    public array $recommendations = [];

    // Likert presets
    public array $likertPresets = [
        'likert_4' => [
            'label' => 'Likert 4 opciones (0-3)',
            'options' => [
                ['text' => 'Nunca', 'weight' => 0],
                ['text' => 'Varios días', 'weight' => 1],
                ['text' => 'Más de la mitad de los días', 'weight' => 2],
                ['text' => 'Casi todos los días', 'weight' => 3],
            ],
        ],
        'likert_4_agree' => [
            'label' => 'Likert 4 opciones (1-4, Acuerdo)',
            'options' => [
                ['text' => 'Totalmente en desacuerdo', 'weight' => 1],
                ['text' => 'En desacuerdo', 'weight' => 2],
                ['text' => 'De acuerdo', 'weight' => 3],
                ['text' => 'Totalmente de acuerdo', 'weight' => 4],
            ],
        ],
        'likert_5' => [
            'label' => 'Likert 5 opciones (1-5)',
            'options' => [
                ['text' => 'Muy en desacuerdo', 'weight' => 1],
                ['text' => 'En desacuerdo', 'weight' => 2],
                ['text' => 'Neutral', 'weight' => 3],
                ['text' => 'De acuerdo', 'weight' => 4],
                ['text' => 'Muy de acuerdo', 'weight' => 5],
            ],
        ],
        'custom' => [
            'label' => 'Personalizado',
            'options' => [],
        ],
    ];

    // Reset pagination on search
    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingShowInactive(): void { $this->resetPage(); }

    // Sorting
    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'desc';
        }
    }

    // ==========================================
    // MODAL NAVIGATION
    // ==========================================

    public function openModal(): void
    {
        $this->isOpen = true;
    }

    public function closeModal(): void
    {
        $this->isOpen = false;
        $this->isViewMode = false;
        $this->currentStep = 1;
        $this->resetInputFields();
    }

    public function nextStep(): void
    {
        if ($this->currentStep === 1) {
            $this->validateStep1();
        }
        if ($this->currentStep < 3) {
            $this->currentStep++;
        }
    }

    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function goToStep(int $step): void
    {
        // Only allow going to steps already visited or current
        if ($step >= 1 && $step <= 3 && ($step <= $this->currentStep || $this->testId)) {
            $this->currentStep = $step;
        }
    }

    // ==========================================
    // CRUD: CREATE / EDIT
    // ==========================================

    public function create(): void
    {
        $this->resetInputFields();
        $this->isViewMode = false;
        $this->currentStep = 1;
        // Add one empty question by default
        $this->addQuestion();
        $this->openModal();
    }

    public function edit(int $id): void
    {
        $test = Test::with(['questions.answerOptions', 'recommendations'])->findOrFail($id);

        $this->testId = $test->id;
        $this->testName = $test->name;
        $this->testDescription = $test->description ?? '';
        $this->testObjective = $test->objective ?? '';
        $this->estimatedTime = $test->estimated_time;
        $this->minimumRetestTime = $test->minimum_retest_time;
        $this->testActive = $test->active;

        // Load questions
        $this->questions = [];
        foreach ($test->questions as $question) {
            $options = [];
            foreach ($question->answerOptions as $opt) {
                $options[] = [
                    'id' => $opt->id,
                    'text' => $opt->text,
                    'weight' => (float)$opt->weight,
                ];
            }
            $this->questions[] = [
                'id' => $question->id,
                'text' => $question->text,
                'answer_type' => $question->answer_type,
                'options' => $options,
            ];
        }

        // Load recommendations
        $this->recommendations = [];
        foreach ($test->recommendations as $rec) {
            $this->recommendations[] = [
                'id' => $rec->id,
                'min_range' => (float)$rec->min_range,
                'max_range' => (float)$rec->max_range,
                'result_category' => $rec->result_category ?? '',
                'recommendation_text' => $rec->recommendation_text,
            ];
        }

        $this->isViewMode = false;
        $this->currentStep = 1;
        $this->openModal();
    }

    public function viewTest(int $id): void
    {
        $this->edit($id);
        $this->isViewMode = true;
    }

    // ==========================================
    // QUESTIONS MANAGEMENT
    // ==========================================

    public function addQuestion(): void
    {
        $this->questions[] = [
            'id' => null,
            'text' => '',
            'answer_type' => 'likert_4',
            'options' => $this->likertPresets['likert_4']['options'],
        ];
    }

    public function removeQuestion(int $index): void
    {
        if (count($this->questions) > 1) {
            unset($this->questions[$index]);
            $this->questions = array_values($this->questions);
        }
    }

    public function moveQuestionUp(int $index): void
    {
        if ($index > 0) {
            $temp = $this->questions[$index - 1];
            $this->questions[$index - 1] = $this->questions[$index];
            $this->questions[$index] = $temp;
        }
    }

    public function moveQuestionDown(int $index): void
    {
        if ($index < count($this->questions) - 1) {
            $temp = $this->questions[$index + 1];
            $this->questions[$index + 1] = $this->questions[$index];
            $this->questions[$index] = $temp;
        }
    }

    public function changeAnswerType(int $questionIndex, string $type): void
    {
        $this->questions[$questionIndex]['answer_type'] = $type;
        if ($type !== 'custom' && isset($this->likertPresets[$type])) {
            $this->questions[$questionIndex]['options'] = $this->likertPresets[$type]['options'];
        }
    }

    public function addOption(int $questionIndex): void
    {
        $this->questions[$questionIndex]['options'][] = [
            'text' => '',
            'weight' => 0,
        ];
    }

    public function removeOption(int $questionIndex, int $optionIndex): void
    {
        if (count($this->questions[$questionIndex]['options']) > 2) {
            unset($this->questions[$questionIndex]['options'][$optionIndex]);
            $this->questions[$questionIndex]['options'] = array_values($this->questions[$questionIndex]['options']);
        }
    }

    // ==========================================
    // RECOMMENDATIONS MANAGEMENT
    // ==========================================

    public function addRecommendation(): void
    {
        $this->recommendations[] = [
            'id' => null,
            'min_range' => 0,
            'max_range' => 0,
            'result_category' => '',
            'recommendation_text' => '',
        ];
    }

    public function removeRecommendation(int $index): void
    {
        unset($this->recommendations[$index]);
        $this->recommendations = array_values($this->recommendations);
    }

    public function autoSuggestRanges(): void
    {
        // Calculate max possible score
        $maxScore = 0;
        foreach ($this->questions as $q) {
            $maxWeight = 0;
            foreach ($q['options'] as $opt) {
                if ((float)($opt['weight'] ?? 0) > $maxWeight) {
                    $maxWeight = (float)$opt['weight'];
                }
            }
            $maxScore += $maxWeight;
        }

        if ($maxScore <= 0) return;

        // Create 4 evenly distributed ranges
        $rangeSize = ceil($maxScore / 4);
        $categories = [
            ['name' => 'Mínima', 'text' => 'Los resultados indican un nivel mínimo. No se identifican señales de alerta significativas.'],
            ['name' => 'Leve', 'text' => 'Los resultados sugieren un nivel leve. Se recomienda seguimiento y actividades preventivas.'],
            ['name' => 'Moderada', 'text' => 'Los resultados indican un nivel moderado. Se recomienda atención profesional y seguimiento periódico.'],
            ['name' => 'Severa', 'text' => 'Los resultados indican un nivel severo. Se recomienda atención especializada prioritaria.'],
        ];

        $this->recommendations = [];
        for ($i = 0; $i < 4; $i++) {
            $min = $i * $rangeSize;
            $max = min(($i + 1) * $rangeSize - ($i < 3 ? 1 : 0), $maxScore);
            $this->recommendations[] = [
                'id' => null,
                'min_range' => $min,
                'max_range' => $max,
                'result_category' => $categories[$i]['name'],
                'recommendation_text' => $categories[$i]['text'],
            ];
        }
    }

    // ==========================================
    // VALIDATION
    // ==========================================

    private function validateStep1(): void
    {
        $this->validate([
            'testName' => 'required|string|min:3|max:100',
            'testDescription' => 'nullable|string|max:1000',
            'testObjective' => 'nullable|string|max:1000',
            'estimatedTime' => 'nullable|integer|min:1|max:300',
            'minimumRetestTime' => 'nullable|integer|min:1|max:730',
        ], [
            'testName.required' => 'El nombre del test es obligatorio.',
            'testName.min' => 'El nombre debe tener al menos 3 caracteres.',
            'testName.max' => 'El nombre no puede exceder 100 caracteres.',
            'estimatedTime.min' => 'El tiempo estimado mínimo es 1 minuto.',
            'minimumRetestTime.min' => 'El tiempo mínimo de reprueba es 1 día.',
        ]);
    }

    // ==========================================
    // SAVE
    // ==========================================

    public function save(): void
    {
        // Validate step 1
        $this->validateStep1();

        // Validate questions
        if (empty($this->questions)) {
            session()->flash('error', 'Debes agregar al menos una pregunta.');
            $this->currentStep = 2;
            return;
        }

        foreach ($this->questions as $i => $q) {
            if (empty(trim($q['text'] ?? ''))) {
                session()->flash('error', 'La pregunta #' . ($i + 1) . ' no puede estar vacía.');
                $this->currentStep = 2;
                return;
            }
            if (empty($q['options']) || count($q['options']) < 2) {
                session()->flash('error', 'La pregunta #' . ($i + 1) . ' necesita al menos 2 opciones.');
                $this->currentStep = 2;
                return;
            }
            foreach ($q['options'] as $j => $opt) {
                if (empty(trim($opt['text'] ?? ''))) {
                    session()->flash('error', 'Todas las opciones de respuesta deben tener texto.');
                    $this->currentStep = 2;
                    return;
                }
            }
        }

        // Save test
        $test = Test::updateOrCreate(
            ['id' => $this->testId],
            [
                'name' => trim($this->testName),
                'description' => $this->testDescription ?: null,
                'objective' => $this->testObjective ?: null,
                'estimated_time' => $this->estimatedTime,
                'minimum_retest_time' => $this->minimumRetestTime,
                'active' => $this->testActive,
            ]
        );

        // Sync questions: delete removed, update existing, create new
        $existingQuestionIds = $test->questions()->pluck('id')->toArray();
        $keptQuestionIds = [];

        foreach ($this->questions as $order => $qData) {
            if (!empty($qData['id']) && in_array($qData['id'], $existingQuestionIds)) {
                // Update existing question
                $question = Question::find($qData['id']);
                $question->update([
                    'text' => trim($qData['text']),
                    'order' => $order + 1,
                    'answer_type' => $qData['answer_type'],
                ]);
                $keptQuestionIds[] = $question->id;
            } else {
                // Create new question
                $question = Question::create([
                    'test_id' => $test->id,
                    'text' => trim($qData['text']),
                    'order' => $order + 1,
                    'answer_type' => $qData['answer_type'],
                ]);
            }

            // Sync options: delete all and recreate (simpler for reordering)
            $question->answerOptions()->delete();
            foreach ($qData['options'] as $optOrder => $optData) {
                AnswerOption::create([
                    'question_id' => $question->id,
                    'text' => trim($optData['text']),
                    'weight' => (float)($optData['weight'] ?? 0),
                    'order' => $optOrder + 1,
                ]);
            }
        }

        // Delete removed questions
        $toDelete = array_diff($existingQuestionIds, $keptQuestionIds);
        if (!empty($toDelete)) {
            Question::whereIn('id', $toDelete)->delete();
        }

        // Sync recommendations
        $test->recommendations()->delete();
        foreach ($this->recommendations as $recData) {
            TestRecommendation::create([
                'test_id' => $test->id,
                'min_range' => (float)$recData['min_range'],
                'max_range' => (float)$recData['max_range'],
                'result_category' => $recData['result_category'] ?: null,
                'recommendation_text' => $recData['recommendation_text'],
            ]);
        }

        $message = $this->testId ? 'Test actualizado correctamente.' : 'Test creado correctamente.';
        session()->flash('message', $message);
        $this->closeModal();
    }

    // ==========================================
    // TOGGLE ACTIVE
    // ==========================================

    public function toggleActive(int $id): void
    {
        $test = Test::findOrFail($id);
        $test->update(['active' => !$test->active]);

        $status = $test->active ? 'activado' : 'desactivado';
        session()->flash('message', "Test {$status} correctamente.");
    }

    // ==========================================
    // DELETE
    // ==========================================

    public function deleteTest(int $id): void
    {
        $test = Test::findOrFail($id);

        if ($test->has_responses) {
            session()->flash('error', 'No se puede eliminar un test que ya tiene respuestas. Desactívalo en su lugar.');
            return;
        }

        $test->recommendations()->delete();
        $test->questions()->each(function ($q) {
            $q->answerOptions()->delete();
        });
        $test->questions()->delete();
        $test->delete();

        session()->flash('message', 'Test eliminado correctamente.');
    }

    // ==========================================
    // COMPUTED: MAX SCORE
    // ==========================================

    public function getCalculatedMaxScoreProperty(): float
    {
        $total = 0;
        foreach ($this->questions as $q) {
            $maxWeight = 0;
            foreach ($q['options'] ?? [] as $opt) {
                if ((float)($opt['weight'] ?? 0) > $maxWeight) {
                    $maxWeight = (float)$opt['weight'];
                }
            }
            $total += $maxWeight;
        }
        return $total;
    }

    // ==========================================
    // HELPERS
    // ==========================================

    private function resetInputFields(): void
    {
        $this->testId = null;
        $this->testName = '';
        $this->testDescription = '';
        $this->testObjective = '';
        $this->estimatedTime = null;
        $this->minimumRetestTime = null;
        $this->testActive = true;
        $this->questions = [];
        $this->recommendations = [];
        $this->resetValidation();
    }

    // ==========================================
    // RENDER
    // ==========================================

    public function render()
    {
        $tests = Test::withCount('questions')
            ->with('recommendations')
            ->when(!$this->showInactive, fn ($q) => $q->where('active', true))
            ->when($this->search, fn ($q) =>
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%")
            )
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.admin.test-management', [
            'tests' => $tests,
        ]);
    }
}
