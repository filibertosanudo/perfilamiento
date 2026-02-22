<x-guest-layout>
    <div class="min-h-screen bg-gradient-to-br from-teal-50 via-white to-teal-50 flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-teal-600 text-white rounded-2xl mb-4 shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2 leading-tight">
                    SIESI
                </h1>
                <p class="text-gray-600 text-sm font-medium">
                    Sistema Integral de Evaluación y Seguimiento Institucional
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-8">
                
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">¿Olvidaste tu contraseña?</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        No hay problema. Ingresa tu correo y te enviaremos un enlace para restablecer tu contraseña.
                    </p>
                </div>

                @session('status')
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <path d="m9 11 3 3L22 4"/>
                        </svg>
                        {{ $value }}
                    </div>
                @endsession

                <x-validation-errors class="mb-4" />

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf

                    <div>
                        <x-label for="email" value="Correo Electrónico" class="mb-1 text-gray-700" />
                        <x-input id="email" 
                                 class="block w-full border-gray-300 focus:border-teal-500 focus:ring-teal-500 rounded-md shadow-sm py-2.5" 
                                 type="email" 
                                 name="email" 
                                 :value="old('email')" 
                                 required 
                                 autofocus 
                                 autocomplete="username" 
                                 placeholder="usuario@institucion.edu" />
                    </div>

                    <button type="submit" class="w-full flex justify-center items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 px-4 rounded-lg transition duration-150 ease-in-out shadow-md hover:shadow-lg">
                        Enviar enlace de restablecimiento
                    </button>

                    <div class="text-center pt-4">
                        <a href="{{ route('login') }}" class="text-sm text-teal-600 hover:text-teal-800 font-medium hover:underline">
                            ← Volver al inicio de sesión
                        </a>
                    </div>
                </form>

                <div class="mt-8 pt-6 border-t border-gray-100">
                    <p class="text-center text-xs text-gray-400">
                        Si tienes problemas, contacta al administrador.
                    </p>
                </div>
            </div>
            
            <div class="text-center mt-8 text-sm text-gray-500">
                <p class="font-medium">Sistema confidencial y seguro</p>
                <p class="mt-1 opacity-75">&copy; {{ date('Y') }} - Todos los derechos reservados</p>
            </div>
        </div>
    </div>
</x-guest-layout>