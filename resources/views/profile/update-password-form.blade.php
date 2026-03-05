<div x-data="{
    password: '',
    checks: {
        length: false,
        uppercase: false,
        lowercase: false,
        number: false,
        special: false
    },
    validatePassword() {
        let pwd = this.password || '';
        this.checks.length = pwd.length >= 12;
        this.checks.uppercase = /[A-Z]/.test(pwd);
        this.checks.lowercase = /[a-z]/.test(pwd);
        this.checks.number = /[0-9]/.test(pwd);
        this.checks.special = /[\W_]/.test(pwd);
    }
}" x-init="$watch('password', () => validatePassword())">

    <div class="md:grid md:grid-cols-3 md:gap-6">
        
        {{-- Columna Izquierda: Panel de Requisitos --}}
        <div class="md:col-span-1 flex flex-col h-full">
            <div class="bg-white/50 rounded-lg p-5 text-sm text-gray-600 border border-gray-200 shadow-sm flex-grow">
                <p class="font-semibold text-base mb-4 text-gray-900">Requisitos de seguridad:</p>
                <ul class="space-y-3">
                    <li class="flex items-center gap-2 transition-colors" :class="checks.length ? 'text-gray-900' : 'text-gray-500'">
                        <span class="font-bold flex-shrink-0 text-lg" :class="checks.length ? 'text-teal-600' : 'text-gray-300'">
                            <span x-show="checks.length">✓</span><span x-show="!checks.length">○</span>
                        </span>
                        <span>Mínimo <strong>12 caracteres</strong></span>
                    </li>
                    <li class="flex items-center gap-2 transition-colors" :class="checks.uppercase ? 'text-gray-900' : 'text-gray-500'">
                        <span class="font-bold flex-shrink-0 text-lg" :class="checks.uppercase ? 'text-teal-600' : 'text-gray-300'">
                            <span x-show="checks.uppercase">✓</span><span x-show="!checks.uppercase">○</span>
                        </span>
                        <span>Al menos <strong>1 letra mayúscula</strong></span>
                    </li>
                    <li class="flex items-center gap-2 transition-colors" :class="checks.lowercase ? 'text-gray-900' : 'text-gray-500'">
                        <span class="font-bold flex-shrink-0 text-lg" :class="checks.lowercase ? 'text-teal-600' : 'text-gray-300'">
                            <span x-show="checks.lowercase">✓</span><span x-show="!checks.lowercase">○</span>
                        </span>
                        <span>Al menos <strong>1 letra minúscula</strong></span>
                    </li>
                    <li class="flex items-center gap-2 transition-colors" :class="checks.number ? 'text-gray-900' : 'text-gray-500'">
                        <span class="font-bold flex-shrink-0 text-lg" :class="checks.number ? 'text-teal-600' : 'text-gray-300'">
                            <span x-show="checks.number">✓</span><span x-show="!checks.number">○</span>
                        </span>
                        <span>Al menos <strong>1 número</strong> (0-9)</span>
                    </li>
                    <li class="flex items-center gap-2 transition-colors" :class="checks.special ? 'text-gray-900' : 'text-gray-500'">
                        <span class="font-bold flex-shrink-0 text-lg" :class="checks.special ? 'text-teal-600' : 'text-gray-300'">
                            <span x-show="checks.special">✓</span><span x-show="!checks.special">○</span>
                        </span>
                        <span>Al menos <strong>1 carácter especial</strong></span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Columna Derecha: Formulario de Contraseña --}}
        <div class="mt-5 md:mt-0 md:col-span-2">
            <form wire:submit="updatePassword">
                <div class="grid grid-cols-1 gap-6">
                    
                    <div>
                        <x-label for="current_password" value="{{ __('Current Password') }}" />
                        <x-input id="current_password" type="password" class="mt-1 block w-full" wire:model="state.current_password" autocomplete="current-password" />
                        <x-input-error for="current_password" class="mt-2" />
                    </div>

                    <div>
                        <x-label for="password" value="{{ __('New Password') }}" />
                        <x-input id="password" type="password" class="mt-1 block w-full" wire:model="state.password" x-model="password" autocomplete="new-password" />
                        <x-input-error for="password" class="mt-2" />
                    </div>

                    <div>
                        <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                        <x-input id="password_confirmation" type="password" class="mt-1 block w-full" wire:model="state.password_confirmation" autocomplete="new-password" />
                        <x-input-error for="password_confirmation" class="mt-2" />
                    </div>

                </div>

                <div class="flex items-center justify-end mt-6">
                    <x-action-message class="me-3" on="saved">
                        {{ __('Saved.') }}
                    </x-action-message>

                    <x-button>
                        {{ __('Save') }}
                    </x-button>
                </div>
            </form>
        </div>

    </div>
</div>