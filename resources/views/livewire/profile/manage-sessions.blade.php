<div class="space-y-6">

    {{-- Flash Message --}}
    @if (session()->has('message'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <path d="m9 11 3 3L22 4"/>
            </svg>
            {{ session('message') }}
        </div>
    @endif

    {{-- Lista de sesiones --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="divide-y divide-gray-100">
            @forelse($sessions as $session)
                <div class="p-4 flex items-start gap-4 hover:bg-gray-50 transition-colors">
                    {{-- Ícono de dispositivo --}}
                    <div class="shrink-0">
                        @if(str_contains($session['user_agent'], 'Windows') || str_contains($session['user_agent'], 'Mac') || str_contains($session['user_agent'], 'Linux'))
                            {{-- Desktop --}}
                            <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" 
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="text-gray-600">
                                    <rect x="2" y="3" width="20" height="14" rx="2"/>
                                    <line x1="8" y1="21" x2="16" y2="21"/>
                                    <line x1="12" y1="17" x2="12" y2="21"/>
                                </svg>
                            </div>
                        @else
                            {{-- Mobile --}}
                            <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" 
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="text-gray-600">
                                    <rect x="5" y="2" width="14" height="20" rx="2"/>
                                    <line x1="12" y1="18" x2="12.01" y2="18"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    {{-- Info de la sesión --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1 w-full">
                            <p class="text-xs font-semibold text-gray-900 truncate">
                                {{ $session['user_agent'] }}
                            </p>
                            
                            @if($session['is_current'])
                                <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-700 ring-1 ring-teal-200">
                                    Sesión actual
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-4 text-xs text-gray-500">
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" 
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="2" y1="12" x2="22" y2="12"/>
                                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                                </svg>
                                {{ $session['ip_address'] }}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" 
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12 6 12 12 16 14"/>
                                </svg>
                                {{ $session['last_active'] }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">
                    <p class="text-sm">No hay sesiones activas.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Botón de cerrar otras sesiones --}}
    @if(count($sessions) > 1)
        <div class="flex justify-end">
            <button 
                wire:click="confirmLogoutOtherDevices"
                class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" 
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
                Cerrar sesión en otros dispositivos
            </button>
        </div>
    @endif

    {{-- Modal de confirmación de contraseña --}}
    @if($showConfirmPassword)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4">
                
                {{-- Backdrop --}}
                <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" wire:click="cancelLogout"></div>

                {{-- Modal --}}
                <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
                    
                    {{-- Header --}}
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Confirmar contraseña
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Por seguridad, confirma tu contraseña para continuar.
                        </p>
                    </div>

                    {{-- Body --}}
                    <div class="px-6 py-5">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Contraseña actual
                            </label>
                            <input 
                                wire:model="password"
                                type="password" 
                                id="password"
                                class="block w-full border border-gray-300 rounded-lg px-3 py-2 shadow-sm focus:ring-teal-500 focus:border-teal-500"
                                placeholder="Ingresa tu contraseña"
                                autofocus
                            >
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            <div class="flex gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" 
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="text-yellow-600 shrink-0 mt-0.5">
                                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                                    <line x1="12" y1="9" x2="12" y2="13"/>
                                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                                </svg>
                                <p class="text-xs text-yellow-800">
                                    Esta acción cerrará tu sesión en todos los dispositivos excepto este. 
                                    Tendrás que volver a iniciar sesión en cada dispositivo.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-2">
                        <button 
                            wire:click="cancelLogout"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Cancelar
                        </button>
                        <button 
                            wire:click="logoutOtherDevices"
                            class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                            Cerrar otras sesiones
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>