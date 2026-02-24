<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Test;
use App\Models\Question;
use App\Models\AnswerOption;

class TestSeeder extends Seeder
{
    public function run(): void
    {
        $this->createGAD7Test();
        $this->createPHQ9Test();
        $this->createRosenbergTest();
    }

    /**
     * Test de Ansiedad Generalizada (GAD-7)
     */
    private function createGAD7Test(): void
    {
        $test = Test::create([
            'name' => 'Escala de Ansiedad Generalizada (GAD-7)',
            'description' => 'El GAD-7 es una herramienta de detección breve diseñada para identificar la presencia y gravedad de la ansiedad generalizada.',
            'objective' => 'Evaluar la frecuencia de síntomas de ansiedad durante las últimas dos semanas.',
            'estimated_time' => 5,
            'minimum_retest_time' => 180, // 6 meses en días
            'active' => true,
        ]);

        $likertScale = [
            ['text' => 'Nunca', 'weight' => 0],
            ['text' => 'Varios días', 'weight' => 1],
            ['text' => 'Más de la mitad de los días', 'weight' => 2],
            ['text' => 'Casi todos los días', 'weight' => 3],
        ];

        $questions = [
            'Sentirse nervioso/a, ansioso/a o muy alterado/a',
            'No poder parar o controlar la preocupación',
            'Preocuparse demasiado por diferentes cosas',
            'Dificultad para relajarse',
            'Estar tan inquieto/a que es difícil quedarse quieto/a',
            'Irritabilidad o mal genio',
            'Sentir miedo como si algo terrible pudiera pasar',
        ];

        foreach ($questions as $index => $questionText) {
            $question = Question::create([
                'test_id' => $test->id,
                'text' => $questionText,
                'order' => $index + 1,
                'answer_type' => 'likert_4',
            ]);

            foreach ($likertScale as $optionIndex => $option) {
                AnswerOption::create([
                    'question_id' => $question->id,
                    'text' => $option['text'],
                    'weight' => $option['weight'],
                    'order' => $optionIndex + 1,
                ]);
            }
        }
    }

    /**
     * Test de Depresión (PHQ-9)
     */
    private function createPHQ9Test(): void
    {
        $test = Test::create([
            'name' => 'Cuestionario de Salud del Paciente (PHQ-9)',
            'description' => 'El PHQ-9 es una herramienta de detección y diagnóstico de depresión ampliamente utilizada.',
            'objective' => 'Identificar la presencia y severidad de síntomas depresivos en las últimas dos semanas.',
            'estimated_time' => 10,
            'minimum_retest_time' => 180,
            'active' => true,
        ]);

        $likertScale = [
            ['text' => 'Nunca', 'weight' => 0],
            ['text' => 'Varios días', 'weight' => 1],
            ['text' => 'Más de la mitad de los días', 'weight' => 2],
            ['text' => 'Casi todos los días', 'weight' => 3],
        ];

        $questions = [
            'Poco interés o placer en hacer las cosas',
            'Sentirse decaído/a, deprimido/a o sin esperanza',
            'Problemas para dormir o dormir demasiado',
            'Sentirse cansado/a o con poca energía',
            'Poco apetito o comer en exceso',
            'Sentirse mal consigo mismo/a, sentir que es un fracaso o que ha decepcionado a su familia',
            'Dificultad para concentrarse en cosas como leer o ver televisión',
            'Moverse o hablar tan lentamente que otras personas lo han notado, o estar tan inquieto/a que se mueve más de lo habitual',
            'Pensamientos de que estaría mejor muerto/a o de hacerse daño de alguna manera',
        ];

        foreach ($questions as $index => $questionText) {
            $question = Question::create([
                'test_id' => $test->id,
                'text' => $questionText,
                'order' => $index + 1,
                'answer_type' => 'likert_4',
            ]);

            foreach ($likertScale as $optionIndex => $option) {
                AnswerOption::create([
                    'question_id' => $question->id,
                    'text' => $option['text'],
                    'weight' => $option['weight'],
                    'order' => $optionIndex + 1,
                ]);
            }
        }
    }

    /**
     * Test de Autoestima (Rosenberg)
     */
    private function createRosenbergTest(): void
    {
        $test = Test::create([
            'name' => 'Escala de Autoestima de Rosenberg',
            'description' => 'Una medida ampliamente utilizada de la autoestima global.',
            'objective' => 'Evaluar los sentimientos generales de valía personal y autoaceptación.',
            'estimated_time' => 5,
            'minimum_retest_time' => 180,
            'active' => true,
        ]);

        $likertScale = [
            ['text' => 'Totalmente en desacuerdo', 'weight' => 1],
            ['text' => 'En desacuerdo', 'weight' => 2],
            ['text' => 'De acuerdo', 'weight' => 3],
            ['text' => 'Totalmente de acuerdo', 'weight' => 4],
        ];

        // Notas: preguntas 3, 5, 8, 9, 10 tienen puntuación inversa
        $questions = [
            ['text' => 'Siento que soy una persona de valía, al menos tanto como las demás', 'reverse' => false],
            ['text' => 'Creo que tengo algunas cualidades buenas', 'reverse' => false],
            ['text' => 'En general, tiendo a pensar que soy un fracaso', 'reverse' => true],
            ['text' => 'Puedo hacer las cosas tan bien como la mayoría de la gente', 'reverse' => false],
            ['text' => 'Siento que no tengo mucho de qué estar orgulloso/a', 'reverse' => true],
            ['text' => 'Tengo una actitud positiva hacia mí mismo/a', 'reverse' => false],
            ['text' => 'En general, estoy satisfecho/a conmigo mismo/a', 'reverse' => false],
            ['text' => 'Desearía poder tener más respeto por mí mismo/a', 'reverse' => true],
            ['text' => 'A veces me siento inútil', 'reverse' => true],
            ['text' => 'A veces pienso que no sirvo para nada', 'reverse' => true],
        ];

        foreach ($questions as $index => $questionData) {
            $question = Question::create([
                'test_id' => $test->id,
                'text' => $questionData['text'],
                'order' => $index + 1,
                'answer_type' => $questionData['reverse'] ? 'likert_4_reverse' : 'likert_4',
            ]);

            $scale = $likertScale;
            if ($questionData['reverse']) {
                // Invertir los pesos para preguntas inversas
                $scale = array_map(function($option, $index) {
                    return [
                        'text' => $option['text'],
                        'weight' => 5 - $option['weight'], // 1->4, 2->3, 3->2, 4->1
                    ];
                }, $likertScale, array_keys($likertScale));
            }

            foreach ($scale as $optionIndex => $option) {
                AnswerOption::create([
                    'question_id' => $question->id,
                    'text' => $option['text'],
                    'weight' => $option['weight'],
                    'order' => $optionIndex + 1,
                ]);
            }
        }
    }
}