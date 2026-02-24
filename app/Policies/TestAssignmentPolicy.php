<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TestAssignment;

class TestAssignmentPolicy
{
    /**
     * Ver listado de asignaciones
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role_id, [1, 2]); // Admin u Orientador
    }

    /**
     * Ver una asignación específica
     */
    public function view(User $user, TestAssignment $assignment): bool
    {
        // Admin puede ver todas
        if ($user->role_id === 1) {
            return true;
        }

        // Orientador solo puede ver las que él asignó
        if ($user->role_id === 2) {
            return $assignment->assigned_by === $user->id;
        }

        return false;
    }

    /**
     * Crear asignación
     */
    public function create(User $user): bool
    {
        return in_array($user->role_id, [1, 2]);
    }

    /**
     * Eliminar/cancelar asignación
     */
    public function delete(User $user, TestAssignment $assignment): bool
    {
        // Admin puede eliminar todas
        if ($user->role_id === 1) {
            return true;
        }

        // Orientador solo puede eliminar las que él asignó
        if ($user->role_id === 2) {
            return $assignment->assigned_by === $user->id;
        }

        return false;
    }

    /**
     * Permitir al usuario responder el test
     */
    public function take(User $user, TestAssignment $assignment): bool
    {
        // Verificar que la asignación está activa
        if (!$assignment->active) {
            return false;
        }

        // Verificar que no esté vencida
        if ($assignment->is_expired) {
            return false;
        }

        // Verificar que el usuario es el destinatario
        $affectedUsers = $assignment->affected_users->pluck('id');
        return $affectedUsers->contains($user->id);
    }
}