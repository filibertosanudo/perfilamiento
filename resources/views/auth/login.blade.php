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
                    Plataforma de Perfilamiento Integral
                </h1>
                <p class="text-gray-600 text-sm font-medium">
                    Sistema de evaluación y seguimiento institucional
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-8">
                
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Iniciar Sesión</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Ingrese sus credenciales para acceder al sistema
                    </p>
                </div>

                <x-validation-errors class="mb-4" />

                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <x-label for="email" value="Correo Electrónico" class="mb-1 text-gray-700" />
                        <x-input id="email" class="block w-full border-gray-300 focus:border-teal-500 focus:ring-teal-500 rounded-md shadow-sm py-2.5" 
                                 type="email" 
                                 name="email" 
                                 :value="old('email')" 
                                 required autofocus autocomplete="username" 
                                 placeholder="usuario@institucion.edu" />
                    </div>

                    <div>
                        <x-label for="password" value="Contraseña" class="mb-1 text-gray-700" />
                        <x-input id="password" class="block w-full border-gray-300 focus:border-teal-500 focus:ring-teal-500 rounded-md shadow-sm py-2.5" 
                                 type="password" 
                                 name="password" 
                                 required autocomplete="current-password" 
                                 placeholder="••••••••" />
                    </div>

                    <div class="flex items-center justify-between mt-4">
                        <label for="remember_me" class="flex items-center cursor-pointer">
                            <x-checkbox id="remember_me" name="remember" class="text-teal-600 focus:ring-teal-500 border-gray-300" />
                            <span class="ms-2 text-sm text-gray-600">Recordarme</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm text-teal-600 hover:text-teal-800 font-medium hover:underline" href="{{ route('password.request') }}">
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif
                    </div>

                    <button type="submit" class="w-full flex justify-center items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 px-4 rounded-lg transition duration-150 ease-in-out shadow-md hover:shadow-lg">
                        Ingresar al Sistema
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-gray-100">
                    <p class="text-center text-xs text-gray-400">
                        Si tienes problemas para acceder, contacta al administrador.
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