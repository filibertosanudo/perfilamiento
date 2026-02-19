<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class SecurityLog
{
    /**
     * Login exitoso
     */
    public static function loginSuccess($user): void
    {
        Log::info('Login exitoso', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role_id' => $user->role_id,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Login fallido
     */
    public static function loginFailed(string $email, int $attempts = 0): void
    {
        Log::warning('Intento de login fallido', [
            'email' => $email,
            'attempts' => $attempts,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Cuenta bloqueada por intentos fallidos
     */
    public static function accountLocked($user): void
    {
        Log::warning('Cuenta bloqueada por intentos fallidos', [
            'user_id' => $user->id,
            'email' => $user->email,
            'locked_until' => $user->locked_until?->toDateTimeString(),
            'ip' => request()->ip(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Intento de acceso no autorizado
     */
    public static function unauthorizedAccess($user, string $route, array $requiredRoles = []): void
    {
        Log::warning('Intento de acceso no autorizado', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role_id' => $user->role_id,
            'attempted_route' => $route,
            'required_roles' => $requiredRoles,
            'ip' => request()->ip(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Cambio de permisos o rol
     */
    public static function permissionChange($admin, $targetUser, string $action, array $changes = []): void
    {
        Log::info('Cambio de permisos', [
            'admin_id' => $admin->id,
            'admin_email' => $admin->email,
            'target_user_id' => $targetUser->id,
            'target_user_email' => $targetUser->email,
            'action' => $action,
            'changes' => $changes,
            'ip' => request()->ip(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Usuario desactivado
     */
    public static function userDeactivated($admin, $targetUser): void
    {
        Log::info('Usuario desactivado', [
            'admin_id' => $admin->id,
            'admin_email' => $admin->email,
            'target_user_id' => $targetUser->id,
            'target_user_email' => $targetUser->email,
            'ip' => request()->ip(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Usuario reactivado
     */
    public static function userReactivated($admin, $targetUser): void
    {
        Log::info('Usuario reactivado', [
            'admin_id' => $admin->id,
            'admin_email' => $admin->email,
            'target_user_id' => $targetUser->id,
            'target_user_email' => $targetUser->email,
            'ip' => request()->ip(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Sesiones cerradas en otros dispositivos
     */
    public static function logoutOtherDevices($user): void
    {
        Log::info('Usuario cerró sesiones en otros dispositivos', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => request()->ip(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Contraseña cambiada
     */
    public static function passwordChanged($user): void
    {
        Log::info('Contraseña cambiada', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => request()->ip(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Invitación enviada
     */
    public static function invitationSent($admin, $targetUser): void
    {
        Log::info('Invitación enviada', [
            'admin_id' => $admin->id,
            'admin_email' => $admin->email,
            'target_user_id' => $targetUser->id,
            'target_user_email' => $targetUser->email,
            'ip' => request()->ip(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Invitación aceptada (cuenta activada)
     */
    public static function invitationAccepted($user): void
    {
        Log::info('Usuario activó su cuenta', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => request()->ip(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }
}