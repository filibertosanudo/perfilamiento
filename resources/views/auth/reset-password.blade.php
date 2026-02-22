<x-guest-layout>
    <div class="min-h-screen bg-gradient-to-br from-teal-50 via-white to-teal-50 flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            
            {{-- Header --}}
            <div class="text-center mb-5">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-teal-600 text-white rounded-xl mb-3 shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-1 leading-tight">
                    SIESI
                </h1>
                <p class="text-gray-600 text-xs font-medium">
                    Sistema Integral de Evaluación y Seguimiento Institucional
                </p>
            </div>

            {{-- Tarjeta principal --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6">
                
                <div class="mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Restablecer contraseña</h2>
                    <p class="text-xs text-gray-500 mt-1">
                        Ingresa tu nueva contraseña cumpliendo los requisitos de seguridad.
                    </p>
                </div>

                <x-validation-errors class="mb-3" />

                {{-- Reducimos la separación vertical --}}
                <form method="POST" action="{{ route('password.update') }}" class="space-y-3">
                    @csrf

                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div>
                        <x-label for="email" value="Correo Electrónico" class="mb-1 text-xs text-gray-700 font-medium" />
                        <x-input id="email" 
                                 class="block w-full border-gray-300 focus:border-teal-500 focus:ring-teal-500 rounded-md shadow-sm py-2 text-sm" 
                                 type="email" 
                                 name="email" 
                                 :value="old('email', $request->email)" 
                                 required 
                                 autofocus 
                                 autocomplete="username" 
                                 placeholder="usuario@institucion.edu" />
                    </div>

                    <div x-data="{
                        password: '',
                        get checks() {
                            return {
                                length: this.password.length >= 12,
                                uppercase: /[A-Z]/.test(this.password),
                                lowercase: /[a-z]/.test(this.password),
                                number: /[0-9]/.test(this.password),
                                special: /[\W_]/.test(this.password)
                            }
                        }
                    }" class="space-y-3">

                        <div>
                            <x-label for="password" value="Nueva Contraseña" class="mb-1 text-xs text-gray-700 font-medium" />
                            <x-input id="password" 
                                     x-model="password"
                                     class="block w-full border-gray-300 focus:border-teal-500 focus:ring-teal-500 rounded-md shadow-sm py-2 text-sm" 
                                     type="password" 
                                     name="password" 
                                     required 
                                     autocomplete="new-password" 
                                     placeholder="Mínimo 12 caracteres" />
                        </div>

                        <div>
                            <x-label for="password_confirmation" value="Confirmar Contraseña" class="mb-1 text-xs text-gray-700 font-medium" />
                            <x-input id="password_confirmation" 
                                     class="block w-full border-gray-300 focus:border-teal-500 focus:ring-teal-500 rounded-md shadow-sm py-2 text-sm" 
                                     type="password" 
                                     name="password_confirmation" 
                                     required 
                                     autocomplete="new-password" 
                                     placeholder="Repite tu contraseña" />
                        </div>

                        {{-- Indicadores de requisitos --}}
                        <div class="bg-gray-50 border border-gray-100 rounded-lg p-3 text-xs text-gray-600 space-y-1.5 mt-2">
                            <p class="font-bold text-gray-800 mb-1.5">Requisitos de seguridad:</p>
                            <ul class="space-y-1">
                                <li class="flex items-center gap-1.5 transition-colors" :class="checks.length ? 'text-gray-800' : 'text-gray-400'">
                                    <span class="font-bold w-4 text-center" :class="checks.length ? 'text-teal-600' : 'text-red-500'" x-text="checks.length ? '✓' : '✗'">✗</span>
                                    <span>Mínimo <strong>12 caracteres</strong></span>
                                </li>
                                <li class="flex items-center gap-1.5 transition-colors" :class="checks.uppercase ? 'text-gray-800' : 'text-gray-400'">
                                    <span class="font-bold w-4 text-center" :class="checks.uppercase ? 'text-teal-600' : 'text-red-500'" x-text="checks.uppercase ? '✓' : '✗'">✗</span>
                                    <span>Al menos <strong>1 mayúscula</strong> (A-Z)</span>
                                </li>
                                <li class="flex items-center gap-1.5 transition-colors" :class="checks.lowercase ? 'text-gray-800' : 'text-gray-400'">
                                    <span class="font-bold w-4 text-center" :class="checks.lowercase ? 'text-teal-600' : 'text-red-500'" x-text="checks.lowercase ? '✓' : '✗'">✗</span>
                                    <span>Al menos <strong>1 minúscula</strong> (a-z)</span>
                                </li>
                                <li class="flex items-center gap-1.5 transition-colors" :class="checks.number ? 'text-gray-800' : 'text-gray-400'">
                                    <span class="font-bold w-4 text-center" :class="checks.number ? 'text-teal-600' : 'text-red-500'" x-text="checks.number ? '✓' : '✗'">✗</span>
                                    <span>Al menos <strong>1 número</strong> (0-9)</span>
                                </li>
                                <li class="flex items-center gap-1.5 transition-colors" :class="checks.special ? 'text-gray-800' : 'text-gray-400'">
                                    <span class="font-bold w-4 text-center" :class="checks.special ? 'text-teal-600' : 'text-red-500'" x-text="checks.special ? '✓' : '✗'">✗</span>
                                    <span>Al menos <strong>1 carácter especial</strong></span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="w-full flex justify-center items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white font-bold py-2.5 px-4 rounded-lg transition duration-150 ease-in-out shadow-md hover:shadow-lg text-sm">
                            Restablecer contraseña
                        </button>
                    </div>
                </form>

                {{-- Pie de tarjeta --}}
                <div class="mt-5 pt-4 border-t border-gray-100">
                    <p class="text-center text-xs text-gray-400">
                        Si tienes problemas, contacta al administrador.
                    </p>
                </div>
            </div>
            
            {{-- Footer externo --}}
            <div class="text-center mt-5 text-xs text-gray-500">
                <p class="font-medium">Sistema confidencial y seguro</p>
                <p class="mt-0.5 opacity-75">&copy; {{ date('Y') }} - Todos los derechos reservados</p>
            </div>
        </div>
    </div>
</x-guest-layout>