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
}