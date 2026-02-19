<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SecurePassword implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Mínimo 12 caracteres
        if (strlen($value) < 12) {
            $fail('La contraseña debe tener al menos 12 caracteres.');
            return;
        }

        // Al menos 1 mayúscula
        if (!preg_match('/[A-Z]/', $value)) {
            $fail('La contraseña debe contener al menos una letra mayúscula.');
            return;
        }

        // Al menos 1 minúscula
        if (!preg_match('/[a-z]/', $value)) {
            $fail('La contraseña debe contener al menos una letra minúscula.');
            return;
        }

        // Al menos 1 número
        if (!preg_match('/[0-9]/', $value)) {
            $fail('La contraseña debe contener al menos un número.');
            return;
        }

        // Al menos 1 carácter especial
        if (!preg_match('/[\W_]/', $value)) {
            $fail('La contraseña debe contener al menos un carácter especial (!@#$%^&*...).');
            return;
        }
    }
}