<div class="space-y-6">

    {{-- Flash Message --}}
    @if (session()->has('message'))
        <div class="bg-teal-50 border border-teal-100 text-teal-700 px-4 py-3 rounded-xl text-sm font-bold flex items-center gap-3 shadow-sm animate-fade-in">
            <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center shadow-sm">
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            {{ session('message') }}
        </div>
    @endif

    {{-- Sessions List --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="divide-y divide-gray-50">
            @forelse($sessions as $session)
                <div class="p-5 flex items-start gap-5 hover:bg-gray-50/50 transition-all duration-200">
                    {{-- Device Icon --}}
                    <div class="shrink-0">
                        @php
                            $isDesktop = str_contains($session['user_agent'], 'Windows') || 
                                         str_contains($session['user_agent'], 'Mac') || 
                                         str_contains($session['user_agent'], 'Linux');
                        @endphp
                        <div class="w-14 h-14 rounded-2xl {{ $session['is_current'] ? 'bg-teal-50 text-teal-600 ring-4 ring-teal-50/50' : 'bg-gray-50 text-gray-400' }} flex items-center justify-center shadow-sm border border-white">
                            @if($isDesktop)
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            @else
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                            @endif
                        </div>
                    </div>

                    {{-- Session Info --}}
                    <div class="flex-1 min-w-0 pt-1">
                        <div class="flex flex-wrap items-center gap-3 mb-2">
                            <p class="text-xs font-bold text-gray-900 uppercase tracking-wider truncate max-w-[200px] sm:max-w-md">
                                {{ $session['user_agent'] }}
                            </p>
                            
                            @if($session['is_current'])
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-teal-600 text-white shadow-sm shadow-teal-500/20">
                                    Este Dispositivo
                                </span>
                            @endif
                        </div>
                        <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-[11px] font-medium text-gray-500">
                            <span class="flex items-center gap-2 px-2 py-1 rounded-lg bg-gray-50 border border-gray-100">
                                <svg class="w-3.5 h-3.5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 18 0 0118 0z"></path></svg>
                                {{ $session['ip_address'] }}
                            </span>
                            <span class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ $session['last_active'] }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-gray-400">
                    <p class="text-sm font-medium italic">No hay registros de sesiones adicionales.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Logout Other Devices Button --}}
    @if(count($sessions) > 1)
        <div class="flex justify-end pt-2">
            <button 
                wire:click="confirmLogoutOtherDevices"
                class="inline-flex items-center gap-2.5 px-6 py-2.5 bg-red-50 text-red-600 text-xs font-bold uppercase tracking-widest rounded-xl hover:bg-red-100 transition-all duration-200 active:scale-95 border border-red-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Cerrar otras sesiones
            </button>
        </div>
    @endif

    {{-- Pass Confirmation Modal --}}
    @if($showConfirmPassword)
        <div class="fixed inset-0 z-[60] overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4">
                
                {{-- Backdrop --}}
                <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-md transition-opacity" wire:click="cancelLogout"></div>

                {{-- Modal --}}
                <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all animate-modal-pop">
                    
                    {{-- Header --}}
                    <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/30">
                        <h3 class="text-xl font-black text-gray-900 tracking-tight">
                            Confirmar Seguridad
                        </h3>
                        <p class="text-xs font-medium text-gray-500 mt-1 uppercase tracking-wider">
                            Protege tu cuenta
                        </p>
                    </div>

                    {{-- Body --}}
                    <div class="px-8 py-8">
                        <div class="mb-6">
                            <label for="password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                Contraseña Actual
                            </label>
                            <input 
                                wire:model="password"
                                type="password" 
                                id="password"
                                class="block w-full border border-gray-100 rounded-2xl px-5 py-3 text-sm bg-gray-50 focus:bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all duration-200 shadow-sm"
                                placeholder="••••••••••••"
                                autofocus
                            >
                            @error('password')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="p-4 bg-yellow-50 rounded-2xl border border-yellow-100 flex gap-4">
                            <div class="w-10 h-10 rounded-xl bg-yellow-100 flex items-center justify-center shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-yellow-600"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                            </div>
                            <p class="text-[11px] text-yellow-900 leading-relaxed font-semibold italic">
                                Se cerrará la sesión en todos los demás dispositivos. Perderás el acceso inmediato en ellos.
                            </p>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="px-8 py-6 border-t border-gray-50 bg-gray-50/50 flex flex-col sm:flex-row gap-3">
                        <button 
                            wire:click="cancelLogout"
                            class="flex-1 px-6 py-3 text-xs font-bold text-gray-500 hover:bg-gray-100 rounded-2xl transition-all uppercase tracking-widest order-2 sm:order-1">
                            Volver
                        </button>
                        <button 
                            wire:click="logoutOtherDevices"
                            class="flex-1 px-8 py-3 text-xs font-bold text-white bg-red-600 rounded-2xl hover:bg-red-700 transition-all shadow-lg shadow-red-500/20 active:scale-95 order-1 sm:order-2 uppercase tracking-widest">
                            Confirmar Cierre
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>