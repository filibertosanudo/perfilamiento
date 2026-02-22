<x-guest-layout>
    <div class="min-h-screen bg-gradient-to-br from-teal-50 via-white to-teal-50 flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            
            {{-- Header --}}
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-teal-600 text-white rounded-xl mb-3 shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-1 leading-tight">
                    Crear tu contraseña
                </h1>
                <p class="text-gray-600 text-sm font-medium">
                    Bienvenido, {{ $user->first_name }}
                </p>
            </div>

            {{-- Tarjeta con padding --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6">
                
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
                    <div class="mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Configuración de acceso</h2>
                        <p class="text-xs text-gray-500 mt-1">
                            Establece una contraseña segura para tu cuenta
                        </p>
                    </div>

                    <form wire:submit="submit" class="space-y-3">
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

                            {{-- Inputs de Contraseña --}}
                            <div>
                                <label for="password" class="block mb-1 text-xs font-medium text-gray-700">
                                    Contraseña <span class="text-red-500">*</span>
                                </label>
                                <input
                                    wire:model="password"
                                    x-model="password"
                                    type="password"
                                    id="password"
                                    class="block w-full border-gray-300 focus:border-teal-500 focus:ring-teal-500 rounded-md shadow-sm py-2 text-sm"
                                    placeholder="Mínimo 12 caracteres"
                                >
                                @error('password')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block mb-1 text-xs font-medium text-gray-700">
                                    Confirmar contraseña <span class="text-red-500">*</span>
                                </label>
                                <input
                                    wire:model="password_confirmation"
                                    type="password"
                                    id="password_confirmation"
                                    class="block w-full border-gray-300 focus:border-teal-500 focus:ring-teal-500 rounded-md shadow-sm py-2 text-sm"
                                    placeholder="Repite tu contraseña"
                                >
                            </div>

                            {{-- Indicadores de Contraseña --}}
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
                                Crear contraseña e iniciar sesión
                            </button>
                        </div>
                    </form>
                @endif
            </div>
            
            {{-- Footer --}}
            <div class="text-center mt-6 text-xs text-gray-500">
                <p class="font-medium">Sistema confidencial y seguro</p>
                <p class="mt-0.5 opacity-75">&copy; {{ date('Y') }} - Todos los derechos reservados</p>
            </div>
        </div>
    </div>
</x-guest-layout>