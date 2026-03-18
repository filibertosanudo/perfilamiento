@props(['title'])

<header class="bg-white border-b border-gray-200 px-8 py-4 sticky top-0 z-40">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $title }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">
                {{ Auth::user()->institution->name ?? 'Institución' }}
            </p>
        </div>

        <div class="flex items-center gap-4">
            {{-- Notifications --}}
            @livewire('notification-center')

            {{-- User Menu --}}
            <div class="flex items-center gap-3 pl-3 border-l border-gray-200 relative" x-data="{ open: false }">
                <div class="text-right hidden sm:block cursor-pointer" @click="open = !open">
                    <p class="text-sm font-medium text-gray-900">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                    <p class="text-xs text-gray-500">
                        @if(Auth::user()->role_id == 1)
                            <span class="inline-flex items-center gap-1">
                                <span class="w-1.5 h-1.5 bg-purple-500 rounded-full"></span>
                                Administrador
                            </span>
                        @elseif(Auth::user()->role_id == 2)
                            <span class="inline-flex items-center gap-1">
                                <span class="w-1.5 h-1.5 bg-teal-500 rounded-full"></span>
                                Orientador
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1">
                                <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                                Usuario
                            </span>
                        @endif
                    </p>
                </div>
                
                <button @click="open = !open" 
                    class="w-10 h-10 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center font-semibold text-sm focus:outline-none transition-opacity hover:opacity-90 overflow-hidden ring-2 ring-white">
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos() && Auth::user()->profile_photo_path)
                         <img class="h-full w-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                    @else
                        {{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->last_name, 0, 1)) }}
                    @endif
                </button>

                 <div x-show="open" 
                    @click.away="open = false"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute right-0 top-12 z-50 mt-2 w-56 rounded-lg shadow-lg origin-top-right bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none" 
                    style="display: none;">
                    
                    {{-- User Info --}}
                    <div class="px-4 py-3">
                        <p class="text-sm font-medium text-gray-900">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ Auth::user()->email }}</p>
                    </div>

                    {{-- Menu Items --}}
                    <div class="py-1">
                        <a href="{{ route('profile.show') }}" 
                            class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Mi Perfil
                        </a>
                    </div>

                    {{-- Logout --}}
                    <div class="py-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" 
                                class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Cerrar Sesión
                            </button>
                        </form>
                    </div>
               </div>
            </div>
        </div>
    </div>
</header>