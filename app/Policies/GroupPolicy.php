<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Group;

class GroupPolicy
{
    /**
     * Ver listado de grupos
     * - Admin ve todos
     * - Orientador ve solo sus grupos
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role_id, [1, 2]); // Admin u Orientador
    }

    /**
     * Ver un grupo específico
     */
    public function view(User $user, Group $group): bool
    {
        // Admin puede ver todos
        if ($user->role_id === 1) {
            return true;
        }

        // Orientador solo puede ver sus propios grupos
        if ($user->role_id === 2) {
            return $group->creator_id === $user->id;
        }

        return false;
    }

    /**
     * Crear grupo
     * - Admin puede crear en cualquier institución
     * - Orientador puede crear en su institución
     */
    public function create(User $user): bool
    {
        return in_array($user->role_id, [1, 2]);
    }

    /**
     * Actualizar grupo
     */
    public function update(User $user, Group $group): bool
    {
        // Admin puede editar todos
        if ($user->role_id === 1) {
            return true;
        }

        // Orientador solo puede editar sus propios grupos
        if ($user->role_id === 2) {
            return $group->creator_id === $user->id;
        }

        return false;
    }

    /**
     * Eliminar/desactivar grupo
     */
    public function delete(User $user, Group $group): bool
    {
        // Admin puede desactivar todos
        if ($user->role_id === 1) {
            return true;
        }

        // Orientador solo puede desactivar sus propios grupos
        if ($user->role_id === 2) {
            return $group->creator_id === $user->id;
        }

        return false;
    }

    /**
     * Gestionar miembros (agregar/quitar usuarios)
     */
    public function manageMembers(User $user, Group $group): bool
    {
        return $this->update($user, $group);
    }
}