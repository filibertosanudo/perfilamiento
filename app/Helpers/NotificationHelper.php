<?php

namespace App\Helpers;

use App\Models\Notification;
use App\Models\User;

class NotificationHelper
{
    /**
     * Crear notificación de test asignado
     */
    public static function testAssigned(User $user, $testName, $testId, $dueDate = null)
    {
        $message = "Se te ha asignado el test: {$testName}";
        if ($dueDate) {
            $message .= ". Fecha límite: " . $dueDate->format('d/m/Y');
        }

        return Notification::create([
            'user_id' => $user->id,
            'type' => 'test_assigned',
            'title' => 'Nuevo test asignado',
            'message' => $message,
            'data' => [
                'test_id' => $testId,
                'due_date' => $dueDate?->toDateString(),
            ],
        ]);
    }

    /**
     * Crear notificación de test completado (para orientador)
     */
    public static function testCompleted(User $advisor, $userName, $testName, $responseId)
    {
        return Notification::create([
            'user_id' => $advisor->id,
            'type' => 'test_completed',
            'title' => 'Test completado',
            'message' => "{$userName} ha completado el test: {$testName}",
            'data' => [
                'response_id' => $responseId,
            ],
        ]);
    }

    /**
     * Crear notificación de resultado severo (para orientador y admin)
     */
    public static function resultSevere(User $recipient, $userName, $testName, $category, $responseId)
    {
        return Notification::create([
            'user_id' => $recipient->id,
            'type' => 'result_severe',
            'title' => '⚠️ Resultado que requiere atención',
            'message' => "{$userName} obtuvo un resultado '{$category}' en {$testName}. Se recomienda seguimiento inmediato.",
            'data' => [
                'response_id' => $responseId,
                'category' => $category,
            ],
        ]);
    }

    /**
     * Crear recordatorio de test próximo a vencer
     */
    public static function testReminder(User $user, $testName, $testId, $daysLeft)
    {
        $message = $daysLeft === 1 
            ? "El test '{$testName}' vence mañana. ¡No olvides completarlo!" 
            : "El test '{$testName}' vence en {$daysLeft} días.";

        return Notification::create([
            'user_id' => $user->id,
            'type' => 'reminder',
            'title' => 'Recordatorio de test',
            'message' => $message,
            'data' => [
                'test_id' => $testId,
                'days_left' => $daysLeft,
            ],
        ]);
    }

    /**
     * Notificar a múltiples usuarios
     */
    public static function notifyMultiple(array $userIds, string $type, string $title, string $message, array $data = [])
    {
        foreach ($userIds as $userId) {
            Notification::create([
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $data,
            ]);
        }
    }
}