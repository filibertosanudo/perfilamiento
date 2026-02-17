<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Panel de Administración</h1>
        <p class="text-gray-500">Vista general del sistema y estadísticas globales</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Card Usuarios --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex flex-row items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-500">Total Usuarios</h3>
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <div>
                <div class="text-3xl font-bold text-gray-900">1,248</div>
                <p class="text-xs text-emerald-600 mt-1 font-medium">+12.5% vs mes anterior</p>
            </div>
        </div>

        {{-- Card Orientadores --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex flex-row items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-500">Orientadores</h3>
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
            <div>
                <div class="text-3xl font-bold text-gray-900">42</div>
                <p class="text-xs text-emerald-600 mt-1 font-medium">+3 este mes</p>
            </div>
        </div>

        {{-- Card Instituciones --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex flex-row items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-500">Instituciones</h3>
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <div>
                <div class="text-3xl font-bold text-gray-900">18</div>
                <p class="text-xs text-gray-500 mt-1">Activas</p>
            </div>
        </div>

        {{-- Card Tests Completados --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex flex-row items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-500">Tests Completados</h3>
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <div>
                <div class="text-3xl font-bold text-gray-900">3,680</div>
                <p class="text-xs text-emerald-600 mt-1 font-medium">+18% este mes</p>
            </div>
        </div>
    </div>

    {{-- Gráficas de Crecimiento y Estado --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Crecimiento de Usuarios y Tests</h3>
            <div id="chart-growth" class="w-full h-80"></div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Estado de Tests</h3>
            <div id="chart-status" class="w-full h-80 flex items-center justify-center"></div>
        </div>
    </div>

    {{-- Gráfico de Categorías y Alertas --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Tests por Categoría</h3>
            <div id="chart-categories" class="w-full h-80"></div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Alertas Recientes</h3>
            <div class="space-y-4">
                
                <div class="flex items-start gap-3 p-3 bg-amber-50 rounded-lg border border-amber-100">
                    <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <div>
                        <p class="text-sm font-medium text-gray-900">15 tests próximos a vencer</p>
                        <p class="text-xs text-gray-500 mt-1">Vencen en los próximos 3 días</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-3 bg-blue-50 rounded-lg border border-blue-100">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Nuevo orientador registrado</p>
                        <p class="text-xs text-gray-500 mt-1">María González - Universidad Tecnológica</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-3 bg-emerald-50 rounded-lg border border-emerald-100">
                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Aumento en participación</p>
                        <p class="text-xs text-gray-500 mt-1">+23% en completados esta semana</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@push('scripts')
    @vite('resources/js/admin/dashboard.js')
@endpush