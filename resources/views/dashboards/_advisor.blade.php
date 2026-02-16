<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Panel de Orientador</h1>
            <p class="text-gray-500">Gestiona tus usuarios y da seguimiento a sus evaluaciones</p>
        </div>
        <button class="bg-teal-600 hover:bg-teal-700 text-white font-medium py-2 px-4 rounded-lg inline-flex items-center transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
            Asignar Nuevo Test
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex flex-row items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-500">Mis Usuarios</h3>
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <div>
                <div class="text-3xl font-bold text-gray-900">127</div>
                <p class="text-xs text-gray-500 mt-1">Activos</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex flex-row items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-500">Tests Asignados</h3>
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
            </div>
            <div>
                <div class="text-3xl font-bold text-gray-900">186</div>
                <p class="text-xs text-amber-600 mt-1 font-medium">25 pendientes</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex flex-row items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-500">Tasa Completado</h3>
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            </div>
            <div>
                <div class="text-3xl font-bold text-gray-900">86.5%</div>
                <p class="text-xs text-emerald-600 mt-1 font-medium">+4.2% este mes</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex flex-row items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-500">Revisión Pendiente</h3>
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div>
                <div class="text-3xl font-bold text-gray-900">12</div>
                <p class="text-xs text-gray-500 mt-1">Requieren atención</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Avance por Grupo</h3>
            <div id="advisor-chart-groups" class="w-full h-80"></div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Tests Completados Esta Semana</h3>
            <div id="advisor-chart-weekly" class="w-full h-80"></div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900">Usuarios que Requieren Atención</h3>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-red-500"></div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Carlos Méndez</p>
                            <p class="text-xs text-gray-500 mt-0.5">Indicadores de ansiedad elevados</p>
                        </div>
                    </div>
                    <button class="text-sm border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 px-3 py-1 rounded-md transition-colors">Ver Detalle</button>
                </div>

                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Ana Sofía Ramírez</p>
                            <p class="text-xs text-gray-500 mt-0.5">Test de autoestima pendiente por 5 días</p>
                        </div>
                    </div>
                    <button class="text-sm border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 px-3 py-1 rounded-md transition-colors">Ver Detalle</button>
                </div>

                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Roberto García</p>
                            <p class="text-xs text-gray-500 mt-0.5">Recomendación de seguimiento programada</p>
                        </div>
                    </div>
                    <button class="text-sm border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 px-3 py-1 rounded-md transition-colors">Ver Detalle</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Chart 1: Avance por Grupo (Stacked Bar)
        const optionsGroups = {
            series: [{
                name: 'Completados',
                data: [28, 22, 31, 19]
            }, {
                name: 'Pendientes',
                data: [4, 8, 2, 11]
            }],
            chart: {
                type: 'bar',
                height: 300,
                stacked: false,
                toolbar: { show: false },
                fontFamily: 'inherit'
            },
            colors: ['#10B981', '#F59E0B'], // Emerald & Amber
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: false,
                    columnWidth: '55%'
                }
            },
            dataLabels: { enabled: false },
            xaxis: {
                categories: ['Grupo A', 'Grupo B', 'Grupo C', 'Grupo D'],
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            grid: { strokeDashArray: 4 }
        };
        new ApexCharts(document.querySelector("#advisor-chart-groups"), optionsGroups).render();

        // Chart 2: Semanal (Line)
        const optionsWeekly = {
            series: [{
                name: 'Tests',
                data: [12, 19, 15, 22, 18]
            }],
            chart: {
                type: 'line',
                height: 300,
                toolbar: { show: false },
                fontFamily: 'inherit'
            },
            colors: ['#0F766E'], // Teal Primary
            stroke: { curve: 'smooth', width: 3 },
            xaxis: {
                categories: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie'],
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            grid: { strokeDashArray: 4 }
        };
        new ApexCharts(document.querySelector("#advisor-chart-weekly"), optionsWeekly).render();
    });
</script>