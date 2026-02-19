<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            abort(403, 'No autenticado');
        }

        $user = $request->user();
        
        // Mapeo de role_id a nombres
        $roleMap = [
            1 => 'admin',
            2 => 'advisor',
            3 => 'user',
        ];

        $userRole = $roleMap[$user->role_id] ?? null;

        if (!in_array($userRole, $roles)) {
            
            \Log::warning('Intento de acceso no autorizado', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $userRole,
                'attempted_route' => $request->path(),
                'required_roles' => $roles,
                'ip' => $request->ip(),
            ]);

            abort(403, 'No tienes permisos para acceder a este recurso');
        }

        return $next($request);
    }
}