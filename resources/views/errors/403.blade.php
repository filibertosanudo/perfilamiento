<x-guest-layout>
    <div class="min-h-screen bg-gradient-to-br from-red-50 via-white to-red-50 flex items-center justify-center p-4">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-24 h-24 bg-red-100 text-red-600 rounded-full mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Acceso Denegado</h1>
            <p class="text-gray-600 mb-8">No tienes permisos para acceder a este recurso.</p>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors">
                Volver al Dashboard
            </a>
        </div>
    </div>
</x-guest-layout>