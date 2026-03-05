<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TestAssignment;
use App\Helpers\NotificationHelper;
use Carbon\Carbon;

class SendTestReminders extends Command
{
    protected $signature = 'tests:send-reminders';
    protected $description = 'Enviar recordatorios de tests próximos a vencer';

    public function handle()
    {
        $this->info('Enviando recordatorios de tests...');

        // Tests que vencen en 3 días
        $assignments3Days = TestAssignment::where('active', true)
            ->whereNotNull('due_date')
            ->whereDate('due_date', Carbon::now()->addDays(3)->toDateString())
            ->whereDoesntHave('responses', function($q) {
                $q->where('completed', true);
            })
            ->with(['user', 'test'])
            ->get();

        foreach ($assignments3Days as $assignment) {
            if ($assignment->user) {
                NotificationHelper::testReminder(
                    $assignment->user,
                    $assignment->test->name,
                    $assignment->test_id,
                    3
                );
            }
        }

        // Tests que vencen mañana
        $assignments1Day = TestAssignment::where('active', true)
            ->whereNotNull('due_date')
            ->whereDate('due_date', Carbon::now()->addDay()->toDateString())
            ->whereDoesntHave('responses', function($q) {
                $q->where('completed', true);
            })
            ->with(['user', 'test'])
            ->get();

        foreach ($assignments1Day as $assignment) {
            if ($assignment->user) {
                NotificationHelper::testReminder(
                    $assignment->user,
                    $assignment->test->name,
                    $assignment->test_id,
                    1
                );
            }
        }

        $total = $assignments3Days->count() + $assignments1Day->count();
        $this->info("Se enviaron {$total} recordatorios.");

        return 0;
    }
}