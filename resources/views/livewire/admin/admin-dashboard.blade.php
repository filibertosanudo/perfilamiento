<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Panel de Administración</h1>
        <p class="text-gray-500">Vista global del sistema de perfilamiento</p>
    </div>

    {{-- Stats Cards Principales --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        {{-- Total Usuarios --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-teal-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
            <h3 class="text-sm font-medium text-gray-500 mb-1">Total Usuarios</h3>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-bold text-gray-900">{{ $stats['total_users'] }}</span>
            </div>
            <p class="text-xs text-gray-500 mt-2">{{ $stats['total_advisors'] }} orientadores</p>
        </div>

        {{-- Total Grupos --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
            <h3 class="text-sm font-medium text-gray-500 mb-1">Grupos Activos</h3>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-bold text-gray-900">{{ $stats['total_groups'] }}</span>
            </div>
            <p class="text-xs text-gray-500 mt-2">{{ $stats['total_institutions'] }} instituciones</p>
        </div>

        {{-- Tests Completados --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-emerald-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <h3 class="text-sm font-medium text-gray-500 mb-1">Tests Completados</h3>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-bold text-gray-900">{{ $stats['total_completed'] }}</span>
            </div>
            <p class="text-xs text-emerald-600 mt-2 font-medium">+{{ $stats['completed_this_week'] }} esta semana</p>
        </div>

        {{-- Tasa de Completación --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-amber-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
            <h3 class="text-sm font-medium text-gray-500 mb-1">Tasa de Completación</h3>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-bold text-gray-900">{{ $stats['completion_rate'] }}%</span>
            </div>
            <p class="text-xs text-gray-500 mt-2">{{ $stats['total_pending'] }} pendientes</p>
        </div>
    </div>

    {{-- Gráfica de Tests por Mes + Distribución de Categorías --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Tests Completados por Mes --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Tests Completados (Últimos 6 Meses)</h3>
            <div class="space-y-3">
                @foreach($monthlyCompletions['labels'] as $index => $month)
                    @php
                        $count = $monthlyCompletions['data'][$index];
                        $maxCount = max($monthlyCompletions['data']);
                        $percentage = $maxCount > 0 ? ($count / $maxCount) * 100 : 0;
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1">
                            <span class="font-medium text-gray-700">{{ $month }}</span>
                            <span class="font-bold text-gray-900">{{ $count }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-teal-600 h-2 rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Distribución por Categoría --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Distribución de Resultados</h3>
            <div class="space-y-3">
                @php
                    $totalResults = $categoryDistribution->sum('count');
                    $categoryColors = [
                        'mínima' => 'emerald',
                        'leve' => 'blue',
                        'moderada' => 'amber',
                        'severa' => 'red',
                        'normal' => 'emerald',
                        'baja' => 'amber',
                        'alta' => 'emerald',
                    ];
                @endphp
                @forelse($categoryDistribution as $item)
                    @php
                        $category = strtolower($item->result_category ?? 'normal');
                        $color = 'gray';
                        foreach ($categoryColors as $key => $badgeColor) {
                            if (str_contains($category, $key)) {
                                $color = $badgeColor;
                                break;
                            }
                        }
                        $percentage = $totalResults > 0 ? ($item->count / $totalResults) * 100 : 0;
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1">
                            <span class="font-medium text-gray-700">{{ ucfirst($item->result_category) }}</span>
                            <span class="font-bold text-gray-900">{{ $item->count }} ({{ round($percentage, 1) }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-{{ $color }}-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">No hay resultados aún</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Instituciones + Tests Más Utilizados --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Instituciones Más Activas --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900">Instituciones Más Activas</h3>
            </div>
            <div class="p-6">
                @forelse($topInstitutions as $institution)
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">{{ $institution->name }}</p>
                            <p class="text-xs text-gray-500">{{ $institution->users_count }} usuarios</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-teal-600">{{ $institution->completed_tests_count }}</p>
                            <p class="text-xs text-gray-500">tests</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">No hay datos disponibles</p>
                @endforelse
            </div>
        </div>

        {{-- Tests Más Utilizados --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900">Tests Más Utilizados</h3>
            </div>
            <div class="p-6">
                @forelse($topTests as $test)
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">{{ $test->name }}</p>
                            <p class="text-xs text-gray-500">{{ $test->assignments_count }} asignaciones</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-blue-600">{{ $test->completed_count }}</p>
                            <p class="text-xs text-gray-500">completados</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">No hay datos disponibles</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Orientadores Más Activos --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900">Orientadores Más Activos</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/60">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Orientador</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Institución</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Grupos</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Usuarios</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Tests Asignados</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($topAdvisors as $advisor)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold">
                                        {{ strtoupper(substr($advisor->first_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $advisor->first_name }} {{ $advisor->last_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $advisor->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $advisor->institution->name }}</td>
                            <td class="px-6 py-4 text-center text-sm font-medium text-gray-900">{{ $advisor->groups_count }}</td>
                            <td class="px-6 py-4 text-center text-sm font-medium text-gray-900">{{ $advisor->total_users ?? 0 }}</td>
                            <td class="px-6 py-4 text-center text-sm font-bold text-teal-600">{{ $advisor->assigned_tests_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">No hay orientadores activos</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Alertas + Recientes --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Resultados Preocupantes --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-red-50">
                <h3 class="text-lg font-bold text-red-900 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    Usuarios que Requieren Atención
                </h3>
                <p class="text-xs text-red-700 mt-1">{{ $stats['users_needing_attention'] }} usuarios con resultados preocupantes</p>
            </div>
            <div class="p-6 max-h-96 overflow-y-auto">
                @forelse($concerningResults as $result)
                    @php
                        $category = strtolower($result->result_category ?? '');
                        $color = str_contains($category, 'severa') ? 'red' : 'amber';
                    @endphp
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $result->user->first_name }} {{ $result->user->last_name }}</p>
                            <p class="text-xs text-gray-500">{{ $result->assignment->test->name }}</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-700 mt-1">
                                {{ ucfirst($result->result_category) }}
                            </span>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500">{{ $result->finished_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">No hay resultados preocupantes recientes</p>
                @endforelse
            </div>
        </div>

        {{-- Tests Completados Recientemente --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900">Actividad Reciente</h3>
            </div>
            <div class="p-6 max-h-96 overflow-y-auto">
                @forelse($recentCompletions as $completion)
                    @php
                        $categoryColors = [
                            'mínima' => 'emerald',
                            'leve' => 'blue',
                            'moderada' => 'amber',
                            'severa' => 'red',
                            'normal' => 'emerald',
                            'baja' => 'amber',
                            'alta' => 'emerald',
                        ];
                        $category = strtolower($completion->result_category ?? 'normal');
                        $color = 'gray';
                        foreach ($categoryColors as $key => $badgeColor) {
                            if (str_contains($category, $key)) {
                                $color = $badgeColor;
                                break;
                            }
                        }
                    @endphp
                    <div class="flex items-center gap-3 py-3 border-b border-gray-100 last:border-0">
                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold shrink-0">
                            {{ strtoupper(substr($completion->user->first_name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">
                                {{ $completion->user->first_name }} {{ $completion->user->last_name }}
                            </p>
                            <p class="text-xs text-gray-500 truncate">{{ $completion->assignment->test->name }}</p>
                        </div>
                        <div class="shrink-0">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-700">
                                {{ ucfirst($completion->result_category) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">No hay actividad reciente</p>
                @endforelse
            </div>
        </div>
    </div>

</div>