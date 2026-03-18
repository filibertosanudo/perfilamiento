<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Rules\SecurePassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;

class UpdateUserPassword implements UpdatesUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and update the user's password.
     *
     * @param  array<string, string>  $input
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'current_password' => ['required', 'string', 'current_password:web'],
            
            'password' => ['required', 'string', new SecurePassword(), 'confirmed'],
            
        ], [
            'current_password.current_password' => __('La contraseña proporcionada no coincide con tu contraseña actual.'),
            'password.confirmed' => __('La confirmación de la contraseña no coincide.'),
        ])->validateWithBag('updatePassword');

        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();
    }
}