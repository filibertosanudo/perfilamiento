<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role_id', 3)->limit(5)->get();

        foreach ($users as $user) {
            // Test asignado
            Notification::create([
                'user_id' => $user->id,
                'type' => 'test_assigned',
                'title' => 'Nuevo test asignado',
                'message' => 'Se te ha asignado el test: Escala de Ansiedad Generalizada (GAD-7). Fecha límite: ' . now()->addDays(7)->format('d/m/Y'),
                'data' => ['test_id' => 1, 'due_date' => now()->addDays(7)->toDateString()],
                'read' => false,
            ]);

            // Recordatorio
            Notification::create([
                'user_id' => $user->id,
                'type' => 'reminder',
                'title' => 'Recordatorio de test',
                'message' => 'El test "Cuestionario de Salud del Paciente (PHQ-9)" vence en 2 días.',
                'data' => ['test_id' => 2, 'days_left' => 2],
                'read' => false,
            ]);
        }

        // Notificaciones para orientadores
        $advisors = User::where('role_id', 2)->limit(3)->get();

        foreach ($advisors as $advisor) {
            Notification::create([
                'user_id' => $advisor->id,
                'type' => 'test_completed',
                'title' => 'Test completado',
                'message' => 'Juan Pérez ha completado el test: GAD-7',
                'data' => ['response_id' => 1],
                'read' => false,
            ]);

            Notification::create([
                'user_id' => $advisor->id,
                'type' => 'result_severe',
                'title' => 'Resultado que requiere atención',
                'message' => 'María González obtuvo un resultado "Ansiedad severa" en GAD-7. Se recomienda seguimiento inmediato.',
                'data' => ['response_id' => 2, 'category' => 'severa'],
                'read' => false,
            ]);
        }

        $this->command->info('Notificaciones de prueba creadas.');
    }
}