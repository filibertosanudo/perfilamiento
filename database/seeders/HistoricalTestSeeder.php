<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Test;
use App\Models\TestAssignment;
use App\Models\TestResponse;
use App\Models\ResponseDetail;
use Carbon\Carbon;

class HistoricalTestSeeder extends Seeder
{
    public function run(): void
    {
        $ana = User::where('email', 'ana.garcia@siesi.test')->first();

        if (!$ana) {
            return;
        }

        $gad7 = Test::where('name', 'like', '%GAD-7%')->first();
        $phq9 = Test::where('name', 'like', '%PHQ-9%')->first();
        $rosenberg = Test::where('name', 'like', '%Rosenberg%')->first();

        if (!$gad7 || !$phq9 || !$rosenberg) {
            return;
        }

        $advisor = User::where('role_id', \DB::table('roles')->where('name', 'Advisor')->value('id'))
            ->where('area_id', $ana->area_id)
            ->first() ?? User::where('role_id', 1)->first();

        $this->simulateTestProgress($ana, $advisor, $gad7, 6, [14, 12, 10, 8, 5, 4]);
        $this->simulateTestProgress($ana, $advisor, $phq9, 6, [18, 15, 12, 10, 7, 5]);
        $this->simulateTestProgress($ana, $advisor, $rosenberg, 4, [15, 20, 26, 30], 4);
    }

    private function simulateTestProgress($user, $advisor, $test, $monthsAgoStart, array $scores, $monthInterval = 1): void
    {
        $questions = $test->questions()->with('answerOptions')->orderBy('order')->get();
        
        foreach ($scores as $index => $targetScore) {
            $monthsAgo = $monthsAgoStart - ($index * $monthInterval);
            $simulatedDate = Carbon::now()->subMonths($monthsAgo)->addDays(rand(1, 5));

            $assignment = TestAssignment::create([
                'user_id' => $user->id,
                'test_id' => $test->id,
                'assigned_by' => $advisor->id,
                'due_date' => $simulatedDate->copy()->addDays(7),
                'active' => true,
            ]);

            $category = $this->determineResultCategory($test->name, $targetScore);

            $response = TestResponse::create([
                'test_assignment_id' => $assignment->id,
                'user_id' => $user->id,
                'started_at' => $simulatedDate->copy()->subMinutes(15),
                'finished_at' => $simulatedDate,
                'completed' => true,
                'numeric_result' => $targetScore,
                'result_category' => $category,
            ]);

            foreach ($questions as $q) {
                ResponseDetail::create([
                    'test_response_id' => $response->id,
                    'question_id' => $q->id,
                    'answer_option_id' => $q->answerOptions->random()->id,
                    'answered_at' => $simulatedDate->copy()->subMinutes(rand(1, 14)),
                ]);
            }
        }
    }

    private function determineResultCategory(string $testName, float $score): string
    {
        if (str_contains($testName, 'GAD-7') || str_contains($testName, 'Ansiedad')) {
            if ($score <= 4) return 'Ansiedad mínima';
            if ($score <= 9) return 'Ansiedad leve';
            if ($score <= 14) return 'Ansiedad moderada';
            return 'Ansiedad severa';
        }

        if (str_contains($testName, 'PHQ-9') || str_contains($testName, 'Depresión')) {
            if ($score <= 4) return 'Depresión mínima';
            if ($score <= 9) return 'Depresión leve';
            if ($score <= 14) return 'Depresión moderada';
            if ($score <= 19) return 'Depresión moderadamente severa';
            return 'Depresión severa';
        }

        if (str_contains($testName, 'Rosenberg') || str_contains($testName, 'Autoestima')) {
            if ($score < 25) return 'Autoestima baja';
            if ($score <= 30) return 'Autoestima normal';
            return 'Autoestima alta';
        }

        return 'Resultado normal';
    }
}
