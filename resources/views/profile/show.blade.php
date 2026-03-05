<x-app-layout>
    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Profile Header Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-teal-500 to-teal-600 h-24 sm:h-32"></div>
                <div class="px-4 sm:px-6 pb-6">
                    <div class="flex flex-col sm:flex-row items-start sm:items-end gap-4 -mt-12 sm:-mt-16">
                        <div class="w-24 h-24 sm:w-32 sm:h-32 rounded-full bg-white ring-4 ring-white shadow-lg overflow-hidden flex items-center justify-center flex-shrink-0">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos() && Auth::user()->profile_photo_path)
                                <img class="h-full w-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                            @else
                                <div class="w-full h-full bg-teal-100 text-teal-700 flex items-center justify-center text-3xl sm:text-4xl font-bold">
                                    {{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->last_name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 pb-0 sm:pb-2 w-full">
                            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">
                                {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                                @if(Auth::user()->second_last_name)
                                    {{ Auth::user()->second_last_name }}
                                @endif
                            </h1>
                            <p class="text-sm text-gray-500 mt-1">{{ Auth::user()->email }}</p>
                            <div class="flex flex-wrap items-center gap-2 sm:gap-4 mt-2">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium
                                    {{ Auth::user()->role_id == 1 ? 'bg-purple-100 text-purple-700' : 
                                       (Auth::user()->role_id == 2 ? 'bg-teal-100 text-teal-700' : 'bg-blue-100 text-blue-700') }}">
                                    <span class="w-1.5 h-1.5 rounded-full
                                        {{ Auth::user()->role_id == 1 ? 'bg-purple-500' : 
                                           (Auth::user()->role_id == 2 ? 'bg-teal-500' : 'bg-blue-500') }}">
                                    </span>
                                    @if(Auth::user()->role_id == 1)
                                        Administrador
                                    @elseif(Auth::user()->role_id == 2)
                                        Orientador
                                    @else
                                        Usuario
                                    @endif
                                </span>
                                @if(Auth::user()->institution)
                                    <span class="inline-flex items-center gap-1.5 text-xs text-gray-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        {{ Auth::user()->institution->name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Grid de Secciones --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- Columna Izquierda: Info Personal --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    {{-- Información del perfil --}}
                    @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                                <h3 class="text-base sm:text-lg font-semibold text-gray-900">Información Personal</h3>
                                <p class="text-xs sm:text-sm text-gray-500 mt-1">Actualiza tu información de perfil y dirección de correo electrónico</p>
                            </div>
                            <div class="p-4 sm:p-6">
                                @livewire('profile.update-profile-information-form')
                            </div>
                        </div>
                    @endif

                    {{-- Cambiar contraseña --}}
                    @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                                <h3 class="text-base sm:text-lg font-semibold text-gray-900">Actualizar Contraseña</h3>
                                <p class="text-xs sm:text-sm text-gray-500 mt-1">Asegúrate de usar una contraseña segura</p>
                            </div>
                            <div class="p-4 sm:p-6">
                                @livewire('profile.update-password-form')
                            </div>
                        </div>
                    @endif

                    {{-- Eliminar cuenta --}}
                    @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                        <div class="bg-white rounded-xl shadow-sm border border-red-200 overflow-hidden">
                            <div class="px-4 sm:px-6 py-4 border-b border-red-200 bg-red-50">
                                <h3 class="text-base sm:text-lg font-semibold text-red-900">Zona Peligrosa</h3>
                                <p class="text-xs sm:text-sm text-red-700 mt-1">Eliminar permanentemente tu cuenta</p>
                            </div>
                            <div class="p-4 sm:p-6">
                                @livewire('profile.delete-user-form')
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Columna Derecha: Seguridad y Sesiones --}}
                <div class="space-y-6">
                    
                    {{-- Autenticación de dos factores --}}
                    @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                                <h3 class="text-sm sm:text-base font-semibold text-gray-900">Autenticación de Dos Factores</h3>
                                <p class="text-xs text-gray-500 mt-1">Agrega seguridad adicional a tu cuenta</p>
                            </div>
                            <div class="p-4 sm:p-6">
                                <div class="max-w-xl">
                                    @livewire('profile.two-factor-authentication-form')
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Sesiones activas --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                            <h3 class="text-sm sm:text-base font-semibold text-gray-900">Sesiones Activas</h3>
                            <p class="text-xs text-gray-500 mt-1">Gestiona tus dispositivos conectados</p>
                        </div>
                        <div class="p-4 sm:p-6">
                            @livewire('profile.manage-sessions')
                        </div>
                    </div>

                    {{-- Info Card --}}
                    <div class="bg-blue-50 rounded-xl border border-blue-200 p-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h4 class="text-sm font-semibold text-blue-900">Seguridad de tu cuenta</h4>
                                <p class="text-xs text-blue-700 mt-1">
                                    Tu información está protegida. Revisa regularmente tus sesiones activas y mantén tu contraseña segura.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>