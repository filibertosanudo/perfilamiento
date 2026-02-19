{{-- resources/views/livewire/auth/accept-invitation.blade.php --}}
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        
        {{-- Logo / Header --}}
        <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-900">
                Crear tu contraseña
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Bienvenido, {{ $user->first_name }}
            </p>
        </div>

        @if($already_accepted)
            {{-- Ya aceptó la invitación --}}
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">
                            Ya has configurado tu contraseña
                        </h3>
                        <p class="mt-2 text-sm text-yellow-700">
                            Esta invitación ya fue utilizada. Si olvidaste tu contraseña, puedes
                            <a href="{{ route('password.request') }}" class="underline font-medium">
                                solicitar un restablecimiento aquí
                            </a>.
                        </p>
                    </div>
                </div>
            </div>
            <a href="{{ route('login') }}" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-teal-600 hover:bg-teal-700">
                Ir al inicio de sesión
            </a>

        @elseif($expired)
            {{-- Invitación expirada --}}
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            Esta invitación ha expirado
                        </h3>
                        <p class="mt-2 text-sm text-red-700">
                            El enlace de invitación ha caducado. Por favor, contacta al administrador para que te envíe una nueva invitación.
                        </p>
                    </div>
                </div>
            </div>

        @else
            {{-- Formulario para crear contraseña --}}
            <form wire:submit="submit" class="mt-8 space-y-6">
                <div class="space-y-4">
                <div x-data="{
                    password: @entangle('password'),
                    checks: {
                        length: false,
                        uppercase: false,
                        lowercase: false,
                        number: false,
                        special: false
                    },
                    validatePassword() {
                        this.checks.length = this.password.length >= 12;
                        this.checks.uppercase = /[A-Z]/.test(this.password);
                        this.checks.lowercase = /[a-z]/.test(this.password);
                        this.checks.number = /[0-9]/.test(this.password);
                        this.checks.special = /[\W_]/.test(this.password);
                    }
                }" x-init="$watch('password', () => validatePassword())">

                    {{-- Inputs de contraseña --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Contraseña <span class="text-red-500">*</span>
                        </label>
                        <input
                            wire:model="password"
                            type="password"
                            id="password"
                            class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 shadow-sm focus:ring-teal-500 focus:border-teal-500"
                            placeholder="Mínimo 12 caracteres"
                        >
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                            Confirmar contraseña <span class="text-red-500">*</span>
                        </label>
                        <input
                            wire:model="password_confirmation"
                            type="password"
                            id="password_confirmation"
                            class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 shadow-sm focus:ring-teal-500 focus:border-teal-500"
                            placeholder="Repite tu contraseña"
                        >
                    </div>

                    {{-- Indicadores de requisitos --}}
                    <div class="bg-gray-50 rounded-lg p-4 text-xs text-gray-700 space-y-2">
                        <p class="font-semibold text-sm mb-2">Requisitos de seguridad:</p>
                        <ul class="space-y-1.5">
                            <li class="flex items-start gap-2 transition-colors" :class="checks.length ? 'text-gray-700' : 'text-gray-400'">
                                <span class="mt-0.5 font-bold" :class="checks.length ? 'text-teal-600' : 'text-red-500'">
                                    <span x-show="checks.length">✓</span>
                                    <span x-show="!checks.length">✗</span>
                                </span>
                                <span>Mínimo <strong>12 caracteres</strong></span>
                            </li>
                            <li class="flex items-start gap-2 transition-colors" :class="checks.uppercase ? 'text-gray-700' : 'text-gray-400'">
                                <span class="mt-0.5 font-bold" :class="checks.uppercase ? 'text-teal-600' : 'text-red-500'">
                                    <span x-show="checks.uppercase">✓</span>
                                    <span x-show="!checks.uppercase">✗</span>
                                </span>
                                <span>Al menos <strong>1 letra mayúscula</strong> (A-Z)</span>
                            </li>
                            <li class="flex items-start gap-2 transition-colors" :class="checks.lowercase ? 'text-gray-700' : 'text-gray-400'">
                                <span class="mt-0.5 font-bold" :class="checks.lowercase ? 'text-teal-600' : 'text-red-500'">
                                    <span x-show="checks.lowercase">✓</span>
                                    <span x-show="!checks.lowercase">✗</span>
                                </span>
                                <span>Al menos <strong>1 letra minúscula</strong> (a-z)</span>
                            </li>
                            <li class="flex items-start gap-2 transition-colors" :class="checks.number ? 'text-gray-700' : 'text-gray-400'">
                                <span class="mt-0.5 font-bold" :class="checks.number ? 'text-teal-600' : 'text-red-500'">
                                    <span x-show="checks.number">✓</span>
                                    <span x-show="!checks.number">✗</span>
                                </span>
                                <span>Al menos <strong>1 número</strong> (0-9)</span>
                            </li>
                            <li class="flex items-start gap-2 transition-colors" :class="checks.special ? 'text-gray-700' : 'text-gray-400'">
                                <span class="mt-0.5 font-bold" :class="checks.special ? 'text-teal-600' : 'text-red-500'">
                                    <span x-show="checks.special">✓</span>
                                    <span x-show="!checks.special">✗</span>
                                </span>
                                <span>Al menos <strong>1 carácter especial</strong> (!@#$%...)</span>
                            </li>
                        </ul>
                    </div>
                </div>
                </div>

                <button
                    type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-colors"
                >
                    Crear contraseña e iniciar sesión
                </button>
            </form>
        @endif

    </div>
</div>