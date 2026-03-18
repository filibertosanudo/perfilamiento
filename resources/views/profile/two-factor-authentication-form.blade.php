<div class="space-y-6">
    {{-- Título de estado actual --}}
    <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
        <div class="size-2 rounded-full {{ $this->enabled ? ($showingConfirmation ? 'bg-amber-500 animate-pulse' : 'bg-teal-500') : 'bg-gray-300' }}"></div>
        @if ($this->enabled)
            @if ($showingConfirmation)
                Finaliza la habilitación de la autenticación de dos factores
            @else
                La autenticación de dos factores está habilitada
            @endif
        @else
            La autenticación de dos factores no está habilitada
        @endif
    </h3>

    <div class="text-sm text-gray-600 leading-relaxed">
        @if ($this->enabled)
            <p class="mb-4">
                La autenticación de dos factores ahora está habilitada. Escanea el siguiente código QR usando la aplicación de autenticación de tu teléfono o ingresa la clave de configuración.
            </p>

            @if ($showingQrCode)
                <div class="bg-gray-50 border border-gray-100 rounded-2xl p-5 flex flex-col gap-6">
                    <div class="flex flex-col items-center sm:items-start gap-6 w-full">
                        {{-- QR Code --}}
                        <div class="p-4 bg-white shadow-sm border border-gray-100 rounded-2xl ring-4 ring-teal-50/30 flex-shrink-0">
                            <div class="w-48 h-48 sm:w-56 sm:h-56">
                                {!! $this->user->twoFactorQrCodeSvg() !!}
                            </div>
                        </div>
                        
                        {{-- Secret Key --}}
                        <div class="space-y-3 w-full min-w-0 text-center sm:text-left">
                            <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Clave de configuración</p>
                            <div class="flex flex-col gap-2">
                                <code class="block w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm font-mono text-teal-700 shadow-sm break-all">
                                    {{ decrypt($this->user->two_factor_secret) }}
                                </code>
                                <p class="text-[11px] text-gray-500 font-medium leading-tight">
                                    Ingresa esto manualmente si no puedes escanear el código.
                                </p>
                            </div>
                        </div>
                    </div>

                    @if ($showingConfirmation)
                        <div class="w-full pt-5 border-t border-gray-100">
                            <x-label for="code" value="Introduce el código generado" class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2" />
                            <x-input id="code" type="text" name="code" 
                                class="block w-full border-gray-100 rounded-xl px-4 py-3 bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all duration-200 shadow-sm" 
                                inputmode="numeric" autofocus autocomplete="one-time-code"
                                placeholder="000000"
                                wire:model="code"
                                wire:keydown.enter="confirmTwoFactorAuthentication" />
                            <x-input-error for="code" class="mt-2 text-xs" />
                        </div>
                    @endif
                </div>
            @endif

            @if ($showingRecoveryCodes)
                <div class="mt-6 p-6 bg-teal-50 border border-teal-100 rounded-2xl">
                    <div class="flex items-center gap-3 mb-4 text-teal-900">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        <p class="font-bold text-sm uppercase tracking-wider">Códigos de Recuperación</p>
                    </div>
                    <p class="text-xs text-teal-700/80 mb-4 leading-relaxed font-medium">
                        Guarda estos códigos en un lugar seguro. Son la única forma de recuperar el acceso si pierdes tu dispositivo.
                    </p>
                    <div class="grid grid-cols-2 gap-3 font-mono text-sm">
                        @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
                            <div class="bg-white px-3 py-1.5 rounded-lg border border-teal-100 text-center text-teal-800 shadow-sm tracking-widest font-bold">{{ $code }}</div>
                        @endforeach
                    </div>
                </div>
            @endif
        @else
            <p class="p-4 bg-gray-50 border border-gray-100 rounded-2xl italic text-gray-500">
                Cuando habilitas la autenticación de dos factores, se te pedirá un token seguro durante el inicio de sesión. Puedes obtener este token desde la aplicación Google Authenticator o similar en tu teléfono.
            </p>
        @endif
    </div>

    {{-- Botones de Acción --}}
    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-50">
        @if (! $this->enabled)
            <x-confirms-password wire:then="enableTwoFactorAuthentication">
                <button type="button" wire:loading.attr="disabled"
                        class="px-8 py-2.5 text-xs text-white bg-teal-600 hover:bg-teal-700 rounded-xl font-bold transition-all duration-200 shadow-lg shadow-teal-500/20 active:scale-95 disabled:opacity-50 uppercase tracking-widest">
                    Habilitar 2FA
                </button>
            </x-confirms-password>
        @else
            @if ($showingConfirmation)
                <x-confirms-password wire:then="disableTwoFactorAuthentication">
                    <button type="button" wire:loading.attr="disabled"
                            class="px-6 py-2.5 text-[10px] font-black text-gray-500 hover:text-red-600 transition-all uppercase tracking-[0.2em]">
                        Cancelar
                    </button>
                </x-confirms-password>

                <x-confirms-password wire:then="confirmTwoFactorAuthentication">
                    <button type="button" wire:loading.attr="disabled"
                            class="px-8 py-2.5 text-xs text-white bg-teal-600 hover:bg-teal-700 rounded-xl font-bold transition-all duration-200 shadow-lg shadow-teal-500/20 active:scale-95 uppercase tracking-widest">
                        Confirmar
                    </button>
                </x-confirms-password>
            @else
                @if ($showingRecoveryCodes)
                    <x-confirms-password wire:then="regenerateRecoveryCodes">
                        <button type="button" class="px-6 py-2.5 text-[10px] font-black text-gray-600 hover:text-gray-900 border border-gray-200 rounded-xl transition-all uppercase tracking-[0.2em]">
                            Regenerar Códigos
                        </button>
                    </x-confirms-password>
                @else
                    <x-confirms-password wire:then="showRecoveryCodes">
                        <button type="button" class="px-6 py-2.5 text-[10px] font-black text-gray-600 hover:text-gray-900 border border-gray-200 rounded-xl transition-all uppercase tracking-[0.2em]">
                            Mostrar Códigos
                        </button>
                    </x-confirms-password>
                @endif

                <x-confirms-password wire:then="disableTwoFactorAuthentication">
                    <button type="button" wire:loading.attr="disabled"
                            class="px-8 py-2.5 text-xs text-red-600 bg-red-50 hover:bg-red-100 rounded-xl font-bold transition-all duration-200 active:scale-95 uppercase tracking-widest">
                        Deshabilitar
                    </button>
                </x-confirms-password>
            @endif
        @endif
    </div>
</div>