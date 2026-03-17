<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- PWA Meta Tags -->
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="Perfilame">
        <meta name="theme-color" content="#4f46e5">
        <link rel="apple-touch-icon" href="/icons/icon-192x192.png">

        <!-- Styles -->
        @livewireStyles

        <!-- Librerías -->
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900">
        
        <div class="flex h-screen overflow-hidden">
            @include('components.sidebar')

            <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
                
                <x-header :title="$header ?? 'Panel de Control'" />

                <main class="p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        @stack('modals')
        @livewireScripts

        @stack('scripts')

        <!-- Offline Detection -->
        <div id="offline-banner" class="hidden fixed bottom-4 right-4 bg-red-600 text-white px-4 py-3 rounded-lg shadow-2xl z-50 flex items-center space-x-3 animate-bounce">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3u" />
            </svg>
            <div>
                <p class="font-bold">Sin conexión</p>
                <p class="text-xs opacity-90">Trabajando en modo local</p>
            </div>
        </div>

        <script>
            window.addEventListener('online', () => {
                document.getElementById('offline-banner').classList.add('hidden');
            });
            window.addEventListener('offline', () => {
                document.getElementById('offline-banner').classList.remove('hidden');
            });
            if (!navigator.onLine) {
                document.getElementById('offline-banner').classList.remove('hidden');
            }
        </script>
    </body>
</html>
