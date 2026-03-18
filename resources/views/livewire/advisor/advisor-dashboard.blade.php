<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Panel de Orientador</h1>
        <p class="text-gray-500">Bienvenido {{ auth()->user()->first_name }}, aquí está el resumen de tus grupos y usuarios</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        {{-- Total Usuarios --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex flex-row items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-500">Mis Usuarios</h3>
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <div>
                <div class="text-3xl font-bold text-gray-900">{{ $stats['total_users'] }}</div>
                <p class="text-xs text-gray-500 mt-1">En {{ $stats['total_groups'] }} grupos</p>
            </div>
        </div>

        {{-- Tests Completados --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex flex-row items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-500">Tests Completados</h3>
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <div class="text-3xl font-bold text-gray-900">{{ $stats['tests_completed'] }}</div>
                <p class="text-xs text-emerald-600 mt-1 font-medium">{{ $stats['tests_completed_this_month'] }} este mes</p>
            </div>
        </div>

        {{-- Tests Pendientes --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex flex-row items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-500">Tests Pendientes</h3>
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <div class="text-3xl font-bold text-gray-900">{{ $stats['tests_pending'] }}</div>
                <p class="text-xs text-gray-500 mt-1">Sin completar</p>
            </div>
        </div>

        {{-- Tasa de Completación --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex flex-row items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-500">Tasa de Completación</h3>
                <svg class="w-5 h-5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <div>
                <div class="text-3xl font-bold text-gray-900">{{ $stats['avg_completion_rate'] }}%</div>
                <p class="text-xs text-gray-500 mt-1">Promedio general</p>
            </div>
        </div>
    </div>

    {{-- Mis Grupos --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-900">Mis Grupos</h3>
            <a href="{{ route('grupos.index') }}" class="text-sm text-teal-600 hover:text-teal-700 font-medium hover:underline">
                Gestionar Grupos
            </a>
        </div>
        
        @if($myGroups->isEmpty())
            <div class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No tienes grupos aún</h3>
                <p class="text-sm text-gray-500 mb-4">Crea tu primer grupo para empezar a gestionar usuarios</p>
                <a href="{{ route('grupos.index') }}" class="inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white font-medium px-4 py-2 rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14"/><path d="M12 5v14"/>
                    </svg>
                    Crear Grupo
                </a>
            </div>
        @else
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($myGroups as $group)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 mb-1">{{ $group->name }}</h4>
                                @if($group->description)
                                    <p class="text-xs text-gray-500 line-clamp-2">{{ $group->description }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                {{ $group->users_count }} miembros
                            </span>
                            <a href="{{ route('grupos.index') }}" class="text-teal-600 hover:text-teal-700 font-medium text-xs hover:underline">
                                Ver detalles →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Tests Completados Recientemente --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900">Tests Completados Recientemente</h3>
                <a href="{{ route('advisor.results') }}" class="text-sm text-teal-600 hover:text-teal-700 font-medium hover:underline">
                    Ver Todos
                </a>
            </div>
            
            @if($recentCompletions->isEmpty())
                <div class="p-8 text-center text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-sm">No hay tests completados aún</p>
                </div>
            @else
                <div class="p-6 space-y-3">
                    @foreach($recentCompletions as $response)
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
                            $category = strtolower($response->result_category ?? 'normal');
                            $color = 'gray';
                            foreach ($categoryColors as $key => $badgeColor) {
                                if (str_contains($category, $key)) {
                                    $color = $badgeColor;
                                    break;
                                }
                            }
                        @endphp
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold shrink-0">
                                    {{ strtoupper(substr($response->user->first_name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ $response->user->first_name }} {{ $response->user->last_name }}
                                    </p>
                                    <p class="text-xs text-gray-500 truncate">{{ $response->assignment->test->name }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-700">
                                    {{ ucfirst($response->result_category) }}
                                </span>
                                <a href="{{ route('advisor.results.show', $response->id) }}" class="text-xs text-teal-600 hover:text-teal-700 font-medium hover:underline">
                                    Ver
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Usuarios que Requieren Atención --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900">Usuarios que Requieren Atención</h3>
                <p class="text-xs text-gray-500 mt-1">Resultados que indican necesidad de seguimiento</p>
            </div>
            
            @if($usersNeedingAttention->isEmpty())
                <div class="p-8 text-center text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm">Todos los usuarios están bien</p>
                </div>
            @else
                <div class="p-6 space-y-3">
                    @foreach($usersNeedingAttention as $response)
                        @php
                            $categoryColors = [
                                'severa' => 'red',
                                'moderada' => 'amber',
                                'baja' => 'amber',
                            ];
                            $category = strtolower($response->result_category ?? '');
                            $color = 'amber';
                            foreach ($categoryColors as $key => $badgeColor) {
                                if (str_contains($category, $key)) {
                                    $color = $badgeColor;
                                    break;
                                }
                            }
                        @endphp
                        <div class="flex items-center justify-between p-3 border-l-4 border-{{ $color }}-500 bg-{{ $color }}-50 rounded-r-lg">
                            <div class="flex items-center gap-3 flex-1">
                                <svg class="w-5 h-5 text-{{ $color }}-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $response->user->first_name }} {{ $response->user->last_name }}
                                    </p>
                                    <p class="text-xs text-gray-600">
                                        {{ $response->assignment->test->name }} - 
                                        <span class="font-medium text-{{ $color }}-700">{{ $response->result_category }}</span>
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('advisor.results.show', $response->id) }}" class="text-xs text-{{ $color }}-700 hover:text-{{ $color }}-800 font-medium hover:underline shrink-0">
                                Revisar
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Tests Próximos a Vencer --}}
    @if($upcomingDeadlines->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900">Tests Próximos a Vencer</h3>
                <p class="text-xs text-gray-500 mt-1">En los próximos 7 días</p>
            </div>
            <div class="p-6 space-y-3">
                @foreach($upcomingDeadlines as $assignment)
                    @php
                        $daysUntilDue = now()->diffInDays($assignment->due_date, false);
                        $urgencyColor = $daysUntilDue <= 1 ? 'red' : ($daysUntilDue <= 3 ? 'amber' : 'gray');
                    @endphp
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:shadow-sm transition-shadow">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $assignment->test->name }}</p>
                            <div class="flex items-center gap-3 mt-1 text-xs text-gray-500">
                                @if($assignment->user_id)
                                    <span>{{ $assignment->user->first_name }} {{ $assignment->user->last_name }}</span>
                                @elseif($assignment->group_id)
                                    <span>Grupo: {{ $assignment->group->name }}</span>
                                @endif
                                <span class="text-{{ $urgencyColor }}-600 font-medium">
                                    Vence: {{ $assignment->due_date->format('d/m/Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>