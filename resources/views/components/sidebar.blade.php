<aside class="w-64 bg-white border-r border-gray-200 h-screen flex flex-col sticky top-0 hidden md:flex z-50">
    <div class="px-6 py-5 border-b border-gray-200">
        <h1 class="text-xl font-medium text-teal-600">
            Sistema de Perfilamiento
        </h1>
        <p class="text-xs text-gray-500 mt-1">Bienestar Integral</p>
    </div>

    <nav class="flex-1 px-3 py-4 overflow-y-auto">
        @php
            $role = Auth::user()->role_id; // 1:Admin, 2:Orientador, 3:Usuario
            $menuItems = [];

            // Icon Definitions (SVG Paths)
            $icons = [
                'dashboard' => 'M3 3h7v7H3V3zm11 0h7v7h-7V3zm0 11h7v7h-7v-7zM3 14h7v7H3v-7z',
                'users' => 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2m8-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm8 10v-2a4 4 0 0 0-3-3.87m-4-12a4 4 0 0 1 0 7.75',
                'groups' => 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 7a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm13 14v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75', // Users con variante
                'user-cog' => 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2m8-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm10.9 2.45l-1.35.45c-.1.35-.25.7-.45 1l-1.35-.15a1.5 1.5 0 0 0-1.75 1.1l-.15 1.35c-.35.2-.7.35-1.05.45l-.45-1.35a1.5 1.5 0 0 0-2.3 0l-.45 1.35c-.35-.1-.7-.25-1.05-.45l-.15-1.35a1.5 1.5 0 0 0-1.75-1.1l-1.35.15c-.2-.3-.35-.65-.45-1l1.35-.45a1.5 1.5 0 0 0 0-2.3l-1.35-.45c.1-.35.25-.7.45-1l1.35.15a1.5 1.5 0 0 0 1.75-1.1l.15-1.35c.35-.2.7-.35 1.05-.45l.45 1.35a1.5 1.5 0 0 0 2.3 0l.45-1.35c.35.1.7.25 1.05.45l.15 1.35a1.5 1.5 0 0 0 1.75 1.1l1.35-.15c.2.3.35.65.45 1l-1.35.45a1.5 1.5 0 0 0 0 2.3z',
                'chart' => 'M3 3v18h18M18 17V9M13 17V5M8 17v-3',
                'settings' => 'M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.09a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2zM12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6z',
                'clipboard' => 'M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2m4 0h-4m0 0V2h4v2m-2 9h4m-4 4h4m-4-8h4',
                'file-text' => 'M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2zM14 2v6h6m-4 5H8m8 4H8m2-8H8',
                'user' => 'M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2m8-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8z',
                'logout' => 'M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4m7 14 5-5-5-5m5 5H9',
            ];

            if ($role == 1) { // Admin
                $menuItems = [
                    ['id' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'dashboard'],
                    ['id' => 'usuarios', 'label' => 'Gestión de Usuarios', 'icon' => 'users', 'route' => 'admin.users'],
                    ['id' => 'grupos', 'label' => 'Gestión de Grupos', 'icon' => 'groups', 'route' => 'grupos.index'],
                    ['id' => 'instituciones', 'label' => 'Instituciones', 'icon' => 'user-cog', 'route' => 'admin.instituciones'],
                    ['id' => 'reportes', 'label' => 'Reportes Generales', 'icon' => 'chart', 'route' => 'admin.reportes'],
                    ['id' => 'configuracion', 'label' => 'Configuración', 'icon' => 'settings', 'route' => 'admin.configuracion'],
                ];
            } elseif ($role == 2) { // Orientador
                $menuItems = [
                    ['id' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'dashboard'],
                    ['id' => 'grupos', 'label' => 'Mis Grupos', 'icon' => 'groups', 'route' => 'grupos.index'],
                    ['id' => 'usuarios', 'label' => 'Mis Usuarios', 'icon' => 'users', 'route' => 'orientador.users'],
                    ['id' => 'asignar-tests', 'label' => 'Asignar Tests', 'icon' => 'clipboard', 'route' => 'orientador.asignar-tests'],
                    ['id' => 'resultados', 'label' => 'Resultados', 'icon' => 'file-text', 'route' => 'orientador.resultados'],
                    ['id' => 'estadisticas', 'label' => 'Estadísticas', 'icon' => 'chart', 'route' => 'orientador.estadisticas'],
                ];
            } else { // Usuario
                $menuItems = [
                    ['id' => 'dashboard', 'label' => 'Mi Dashboard', 'icon' => 'dashboard', 'route' => 'dashboard'],
                    ['id' => 'mis-tests', 'label' => 'Mis Tests', 'icon' => 'clipboard', 'route' => 'usuario.mis-tests'],
                    ['id' => 'mis-resultados', 'label' => 'Mis Resultados', 'icon' => 'file-text', 'route' => 'usuario.mis-resultados'],
                    ['id' => 'perfil', 'label' => 'Mi Perfil', 'icon' => 'user', 'route' => 'usuario.perfil'],
                ];
            }
        @endphp

        @foreach($menuItems as $item)
            @php
                $isActive = request()->routeIs($item['route']) || request()->is($item['id'] . '*');
                
                $routeName = $item['route'] ?? '#';
                $routeUrl = Route::has($routeName) ? route($routeName) : '#';
            @endphp

            <a href="{{ $routeUrl }}"
            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg mb-1 transition-all
            {{ $isActive 
                ? 'bg-teal-50 text-teal-700' 
                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' 
            }}">
                
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="{{ $icons[$item['icon']] }}"></path>
                    @if($item['icon'] == 'user-cog') <circle cx="18" cy="15" r="3"></circle> @endif
                </svg>

                <span class="text-sm font-medium">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="px-3 py-4 border-t border-gray-200">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-red-50 hover:text-red-600 transition-all">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="{{ $icons['logout'] }}"></path>
                </svg>
                <span class="text-sm font-medium">Cerrar Sesión</span>
            </button>
        </form>
    </div>
</aside>