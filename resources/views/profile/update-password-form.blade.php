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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Requirements Panel --}}
        <div class="lg:col-span-1">
            <div class="bg-gray-50 border border-gray-100 rounded-2xl p-6 shadow-sm">
                <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Requisitos de seguridad</h4>
                <ul class="space-y-3">
                    <template x-for="(check, key) in [
                        { label: 'Mínimo 12 caracteres', met: checks.length },
                        { label: 'Letra mayúscula', met: checks.uppercase },
                        { label: 'Letra minúscula', met: checks.lowercase },
                        { label: 'Un número (0-9)', met: checks.number },
                        { label: 'Carácter especial', met: checks.special }
                    ]" :key="key">
                        <li class="flex items-center gap-3 text-xs font-medium transition-colors duration-200" :class="check.met ? 'text-teal-700' : 'text-gray-400'">
                            <div class="size-5 rounded-full flex items-center justify-center transition-all duration-200" :class="check.met ? 'bg-teal-100 text-teal-600' : 'bg-gray-100 text-gray-300'">
                                <svg x-show="check.met" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                <div x-show="!check.met" class="size-1.5 rounded-full bg-current"></div>
                            </div>
                            <span x-text="check.label"></span>
                        </li>
                    </template>
                </ul>
            </div>
        </div>

        {{-- Password Form --}}
        <div class="lg:col-span-2">
            <form wire:submit="updatePassword">
                <div class="space-y-6">
                    <div>
                        <x-label for="current_password" value="Contraseña Actual" class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2" />
                        <x-input id="current_password" type="password" class="block w-full border-gray-100 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all duration-200" wire:model="state.current_password" autocomplete="current-password" />
                        <x-input-error for="current_password" class="mt-2 text-xs" />
                    </div>

                    <div>
                        <x-label for="password" value="Nueva Contraseña" class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2" />
                        <x-input id="password" type="password" class="block w-full border-gray-100 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all duration-200" wire:model="state.password" x-model="password" autocomplete="new-password" />
                        <x-input-error for="password" class="mt-2 text-xs" />
                    </div>

                    <div>
                        <x-label for="password_confirmation" value="Confirmar Nueva Contraseña" class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2" />
                        <x-input id="password_confirmation" type="password" class="block w-full border-gray-100 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all duration-200" wire:model="state.password_confirmation" autocomplete="new-password" />
                        <x-input-error for="password_confirmation" class="mt-2 text-xs" />
                    </div>
                </div>

                <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-50">
                    <x-action-message class="me-3 text-xs font-bold text-teal-600" on="saved">
                        ¡Contraseña actualizada!
                    </x-action-message>

                    <button class="px-8 py-2.5 bg-teal-600 text-white rounded-xl text-sm font-bold hover:bg-teal-700 shadow-lg shadow-teal-500/20 transition-all active:scale-95">
                        Actualizar Contraseña
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>