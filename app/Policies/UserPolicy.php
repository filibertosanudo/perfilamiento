<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Ver listado de usuarios
     * - Admin ve todos
     * - Orientador ve solo usuarios asignados a sus grupos
     */
    public function viewAny(User $user): bool
    {
        // Admin puede ver todos
        if ($user->role_id === 1) {
            return true;
        }

        // Orientador puede ver su panel con filtro de sus usuarios
        if ($user->role_id === 2) {
            return true;
        }

        return false;
    }

    /**
     * Ver un usuario específico
     */
    public function view(User $user, User $model): bool
    {
        // Admin puede ver a todos
        if ($user->role_id === 1) {
            return true;
        }

        // Orientador puede ver a usuarios de sus grupos
        if ($user->role_id === 2) {
            return $model->groups()
                ->where('creator_id', $user->id)
                ->exists();
        }

        // Usuario solo puede ver su propio perfil
        return $user->id === $model->id;
    }

    /**
     * Crear usuario
     * - Admin puede crear cualquier usuario
     * - Orientador puede crear usuarios en su institución
     */
    public function create(User $user): bool
    {
        return in_array($user->role_id, [1, 2]); // Admin u Orientador
    }

    /**
     * Actualizar usuario
     */
    public function update(User $user, User $model): bool
    {
        // Admin puede editar a todos
        if ($user->role_id === 1) {
            return true;
        }

        // Orientador puede editar a usuarios de sus grupos
        if ($user->role_id === 2) {
            return $model->groups()
                ->where('creator_id', $user->id)
                ->exists();
        }

        // Usuario solo puede editar su propio perfil
        return $user->id === $model->id;
    }

    /**
     * Eliminar/desactivar usuario
     */
    public function delete(User $user, User $model): bool
    {
        // Admin puede desactivar a todos
        if ($user->role_id === 1) {
            return $user->id !== $model->id;
        }

        // Orientador puede desactivar a usuarios de sus grupos
        if ($user->role_id === 2) {
            return $model->groups()
                ->where('creator_id', $user->id)
                ->exists();
        }

        return false;
    }
}