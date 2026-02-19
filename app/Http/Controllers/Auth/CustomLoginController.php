<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use App\Models\User;
use App\Helpers\SecurityLog;

class CustomLoginController extends Controller
{
    /**
     * Maneja el intento de login con todas las validaciones de seguridad
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Verificar rate limiting global
        $this->ensureIsNotRateLimited($request);

        $user = User::where('email', $request->email)->first();

        // Si el usuario no existe, usar un delay falso para evitar user enumeration
        if (!$user) {
            $this->incrementRateLimiter($request);
            sleep(rand(1, 3));
            
            SecurityLog::loginFailed($request->email);
            
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        // Verificar si la cuenta está bloqueada
        if ($user->isLocked()) {
            $minutesLeft = now()->diffInMinutes($user->locked_until);
            
            SecurityLog::accountLocked($user);
            
            throw ValidationException::withMessages([
                'email' => ["Cuenta bloqueada. Intente nuevamente en {$minutesLeft} minutos."],
            ]);
        }

        // Verificar si la cuenta está activa
        if (!$user->active) {
            throw ValidationException::withMessages([
                'email' => ['Esta cuenta está inactiva. Verifica tu correo para activarla.'],
            ]);
        }

        // Intentar autenticación
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            $user->resetFailedAttempts();
            RateLimiter::clear($this->throttleKey($request));

            SecurityLog::loginSuccess($user);

            return redirect()->intended(route('dashboard'));
        }

        // Login falló - incrementar intentos
        $user->incrementFailedAttempts();
        $this->incrementRateLimiter($request);

        // Retardo progresivo según intentos fallidos
        $delay = min($user->failed_login_attempts, 5);
        sleep($delay);

        SecurityLog::loginFailed($request->email, $user->failed_login_attempts);

        throw ValidationException::withMessages([
            'email' => ['Las credenciales proporcionadas son incorrectas.'],
        ]);
    }

    /**
     * Rate limiting: máximo 5 intentos en 15 minutos
     */
    protected function ensureIsNotRateLimited(Request $request): void
        {
            if (RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
                $seconds = RateLimiter::availableIn($this->throttleKey($request));
                
                if ($seconds < 60) {
                    $textoSegundo = $seconds == 1 ? 'segundo' : 'segundos';
                    $mensaje = "Demasiados intentos. Intente nuevamente en {$seconds} {$textoSegundo}.";
                } else {
                    $minutes = ceil($seconds / 60);
                    $textoMinuto = $minutes == 1 ? 'minuto' : 'minutos';
                    $mensaje = "Demasiados intentos. Intente nuevamente en {$minutes} {$textoMinuto}.";
                }
                
                throw ValidationException::withMessages([
                    'email' => [$mensaje],
                ]);
            }
        }

    /**
     * Incrementar el rate limiter
     */
    protected function incrementRateLimiter(Request $request): void
    {
        RateLimiter::hit($this->throttleKey($request), 900); // 15 minutos
    }

    /**
     * Generar clave única para rate limiting
     */
    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(
            Str::lower($request->input('email')) . '|' . $request->ip()
        );
    }

    /**
     * Logout con invalidación de sesión
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}