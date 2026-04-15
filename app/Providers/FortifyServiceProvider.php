<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;
use App\Models\User;
use App\Helpers\SecurityLog;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        // ============================================
        // Autenticación personalizada (migrada desde CustomLoginController)
        // Maneja: bloqueo de cuentas, logging de seguridad, delays progresivos
        // ============================================
        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();

            // Si el usuario no existe, delay para evitar user enumeration
            if (! $user) {
                sleep(rand(1, 3));
                SecurityLog::loginFailed($request->email);
                return null;
            }

            // Verificar si la cuenta está bloqueada
            if ($user->isLocked()) {
                $minutesLeft = now()->diffInMinutes($user->locked_until);
                SecurityLog::accountLocked($user);
                
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'email' => ["Cuenta bloqueada. Intente nuevamente en {$minutesLeft} minutos."],
                ]);
            }

            // Verificar si la cuenta está activa
            if (! $user->active) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'email' => ['Esta cuenta está inactiva. Verifica tu correo para activarla.'],
                ]);
            }

            // Intentar autenticación
            if (Hash::check($request->password, $user->password)) {
                $user->resetFailedAttempts();
                SecurityLog::loginSuccess($user);
                return $user;
            }

            // Login falló — incrementar intentos y delay progresivo
            $user->incrementFailedAttempts();
            $delay = min($user->failed_login_attempts, 5);
            sleep($delay);

            SecurityLog::loginFailed($request->email, $user->failed_login_attempts);

            return null;
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
