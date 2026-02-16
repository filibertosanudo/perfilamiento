@props(['title'])

<header class="bg-white border-b border-gray-200 px-8 py-4 sticky top-0 z-40">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl text-gray-900">{{ $title }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">
                {{ Auth::user()->institucion ?? 'Institución Universitaria' }}
            </p>
        </div>

        <div class="flex items-center gap-4">
            <div class="hidden md:flex items-center gap-2 bg-gray-50 rounded-lg px-3 py-2 border border-gray-200">
                <svg class="w-[18px] h-[18px] text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.3-4.3"></path>
                </svg>
                <input 
                    type="text" 
                    placeholder="Buscar..." 
                    class="bg-transparent border-none outline-none text-sm w-64 p-0 focus:ring-0 text-gray-900 placeholder:text-gray-400"
                />
            </div>

            <button class="relative p-2 hover:bg-gray-100 rounded-lg transition-colors text-gray-600">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path>
                </svg>
                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>

            <div class="flex items-center gap-3 pl-3 border-l border-gray-200 relative" x-data="{ open: false }">
                <div class="text-right hidden sm:block cursor-pointer" @click="open = !open">
                    <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500 capitalize">
                        @if(Auth::user()->role_id == 1) Administrador
                        @elseif(Auth::user()->role_id == 2) Orientador
                        @else Usuario
                        @endif
                    </p>
                </div>
                
                <button @click="open = !open" class="w-10 h-10 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center font-medium focus:outline-none transition-opacity hover:opacity-90 overflow-hidden">
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos() && Auth::user()->profile_photo_path)
                         <img class="h-full w-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                    @else
                        {{ substr(Auth::user()->name, 0, 2) }}
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
                    class="absolute right-0 top-12 z-50 mt-2 w-48 rounded-md shadow-lg origin-top-right bg-white ring-1 ring-black ring-opacity-5 py-1 focus:outline-none" 
                    style="display: none;">
                    
                    <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mi Perfil</a>
                    <div class="border-t border-gray-100"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Cerrar Sesión</button>
                    </form>
               </div>
            </div>
        </div>
    </div>
</header>