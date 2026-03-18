<div class="space-y-6">

    {{-- Flash Message --}}
    @if(session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3 rounded-xl shadow-sm">
            <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm font-medium">{{ session('message') }}</p>
        </div>
    @endif

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Gestión de Áreas</h1>
            <p class="mt-1 text-sm text-gray-500">Administra todas las áreas/facultades del sistema</p>
        </div>
        <button wire:click="create"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-semibold text-sm rounded-xl shadow transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva Área
        </button>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-teal-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500">Total</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500">Activas</p>
                <p class="text-2xl font-bold text-emerald-700">{{ $stats['active'] }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500">Inactivas</p>
                <p class="text-2xl font-bold text-gray-600">{{ $stats['inactive'] }}</p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <div class="flex flex-wrap gap-3 items-center">
            {{-- Search --}}
            <div class="relative flex-1 min-w-56">
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text"
                    placeholder="Buscar por nombre o ciudad..."
                    class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg bg-gray-50 text-sm placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors"/>
            </div>
            {{-- Type filter --}}
            <select wire:model.live="filterType"
                class="w-full sm:w-48 border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors pr-8">
                <option value="">Todos los tipos</option>
                @foreach($types as $val => $label)
                    <option value="{{ $val }}">{{ $label }}</option>
                @endforeach
            </select>
            {{-- Show inactive --}}
            <label class="flex items-center gap-2 cursor-pointer group">
                <div class="relative">
                    <input type="checkbox" 
                        wire:model.live="showInactive"
                        class="sr-only peer">
                    <div class="w-10 h-6 bg-gray-200 rounded-full peer peer-checked:bg-teal-600 transition-colors"></div>
                    <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-4"></div>
                </div>
                <span class="text-xs font-medium text-gray-600 group-hover:text-gray-900">
                    Mostrar inactivas
                </span>
            </label>
        </div>
    </div>

    {{-- Main Content: List + Detail --}}
    <div class="flex gap-6 items-start">

        {{-- Area Grid/List --}}
        <div class="{{ $detail ? 'flex-1 min-w-0' : 'w-full' }}">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                {{-- Table Header --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50/60">
                                <th wire:click="sortBy('name')"
                                    class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center gap-1">
                                        Área
                                        @if($sortField === 'name')
                                            <svg class="w-3.5 h-3.5 {{ $sortDirection === 'asc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m18 15-6-6-6 6"/>
                                            </svg>
                                        @endif
                                    </div>
                                </th>
                                @if(!$detail)
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Ciudad</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Orientadores</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Usuarios</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Grupos</th>
                                @endif
                                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($areas as $area)
                                @php
                                    $typeColors = [
                                        'universidad' => 'indigo', 'preparatoria' => 'blue', 'secundaria' => 'cyan',
                                        'primaria' => 'teal', 'empresa' => 'violet', 'gobierno' => 'amber',
                                        'ong' => 'emerald', 'otro' => 'gray',
                                    ];
                                    $tc = $typeColors[$area->type ?? 'otro'] ?? 'gray';
                                @endphp
                                <tr class="hover:bg-gray-50/70 transition-colors {{ $detailId === $area->id ? 'bg-teal-50/60 border-l-2 border-l-teal-500' : '' }}">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-{{ $tc }}-100 flex items-center justify-center text-{{ $tc }}-600 font-bold text-sm shrink-0">
                                                {{ strtoupper(substr($area->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">{{ $area->name }}</p>
                                                @if($area->type)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-{{ $tc }}-100 text-{{ $tc }}-700">
                                                        {{ $types[$area->type] ?? ucfirst($area->type) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    @if(!$detail)
                                    <td class="px-5 py-4 text-sm text-gray-600">{{ $area->city ?? '—' }}</td>
                                    <td class="px-5 py-4 text-center">
                                        <span class="text-sm font-semibold text-indigo-700">{{ $area->advisors_count }}</span>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <span class="text-sm font-semibold text-blue-700">{{ $area->users_count }}</span>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <span class="text-sm font-semibold text-teal-700">{{ $area->groups_count }}</span>
                                    </td>
                                    @endif
                                    <td class="px-5 py-4 text-center">
                                        @if($area->active)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Activa</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">Inactiva</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-end gap-0.5 opacity-60 group-hover:opacity-100 transition-opacity">
                                            <button wire:click="showDetail({{ $area->id }})"
                                                class="p-1.5 hover:bg-gray-100 rounded-lg transition-colors {{ $detailId === $area->id ? 'bg-teal-100 text-teal-700' : '' }}" title="Ver detalles">
                                                <svg class="text-gray-500" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>
                                            </button>
                                            
                                            <button wire:click="edit({{ $area->id }})"
                                                class="p-1.5 hover:bg-gray-100 rounded-lg transition-colors" title="Editar">
                                                <svg class="text-gray-500" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/>
                                                </svg>
                                            </button>

                                            @if($area->active)
                                                <button
                                                    wire:click="toggleActive({{ $area->id }})"
                                                    wire:confirm="¿Seguro que deseas desactivar el área {{ $area->name }}?"
                                                    class="p-1.5 hover:bg-red-50 rounded-lg transition-colors" title="Desactivar">
                                                    <svg class="text-red-500" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                                                    </svg>
                                                </button>
                                            @else
                                                <button
                                                    wire:click="toggleActive({{ $area->id }})"
                                                    wire:confirm="¿Reactivar el área {{ $area->name }}?"
                                                    class="p-1.5 hover:bg-green-50 rounded-lg transition-colors" title="Reactivar">
                                                    <svg class="text-green-600" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/>
                                                        <path d="M21 3v5h-5"/>
                                                        <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/>
                                                        <path d="M8 16H3v5"/>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center gap-3 text-gray-400">
                                            <svg class="w-12 h-12 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                                            </svg>
                                            <p class="text-sm font-medium text-gray-500">No hay áreas</p>
                                            <p class="text-xs">{{ $search || $filterType ? 'Ajusta los filtros de búsqueda' : 'Crea la primera área' }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{-- Pagination --}}
                <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/40">
                    {{ $areas->links() }}
                </div>
            </div>
        </div>

        {{-- Detail Panel --}}
        @if($detail)
            <div class="w-96 shrink-0 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden sticky top-4">
                {{-- Detail Header --}}
                @php
                    $typeColors = ['universidad'=>'indigo','preparatoria'=>'blue','secundaria'=>'cyan','primaria'=>'teal','empresa'=>'violet','gobierno'=>'amber','ong'=>'emerald','otro'=>'gray'];
                    $tc = $typeColors[$detail->type ?? 'otro'] ?? 'gray';
                @endphp
                <div class="bg-gradient-to-br from-teal-600 to-teal-700 p-5 text-white">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center text-white font-bold text-lg">
                                {{ strtoupper(substr($detail->name, 0, 2)) }}
                            </div>
                            <div>
                                <h3 class="font-bold text-base leading-tight">{{ $detail->name }}</h3>
                                @if($detail->type)
                                    <span class="text-xs text-teal-200">{{ $types[$detail->type] ?? ucfirst($detail->type) }}</span>
                                @endif
                            </div>
                        </div>
                        <button wire:click="closeDetail" class="p-1 rounded-lg hover:bg-white/20 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-5 space-y-5 max-h-[75vh] overflow-y-auto">
                    {{-- Info --}}
                    <div class="space-y-2 text-sm">
                        @if($detail->city)
                            <div class="flex items-center gap-2 text-gray-600">
                                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $detail->city }}{{ $detail->address ? ' · ' . $detail->address : '' }}
                            </div>
                        @endif
                        @if($detail->phone)
                            <div class="flex items-center gap-2 text-gray-600">
                                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                {{ $detail->phone }}
                            </div>
                        @endif
                    </div>

                    {{-- Stats Grid --}}
                    <div class="grid grid-cols-2 gap-3">
                        @foreach([
                            ['Usuarios', $detailStats['total_users'], 'blue'],
                            ['Tests', $detailStats['total_tests'], 'teal'],
                            ['Este mes', $detailStats['this_month'], 'emerald'],
                            ['Grupos', $detailStats['total_groups'], 'indigo'],
                        ] as [$label, $val, $color])
                            <div class="bg-{{ $color }}-50 border border-{{ $color }}-100 rounded-xl p-3 text-center">
                                <p class="text-xl font-bold text-{{ $color }}-700">{{ $val }}</p>
                                <p class="text-xs text-{{ $color }}-600 mt-0.5">{{ $label }}</p>
                            </div>
                        @endforeach
                    </div>

                    @if($detailStats['concerning'] > 0)
                        <div class="bg-red-50 border border-red-200 rounded-xl p-3 flex items-center gap-3">
                            <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <p class="text-sm text-red-700"><strong>{{ $detailStats['concerning'] }}</strong> resultado(s) requieren atención</p>
                        </div>
                    @endif

                    {{-- Categories --}}
                    @if($detailStats['categories']->isNotEmpty())
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Distribución de Resultados</h4>
                            <div class="space-y-2">
                                @php $total = $detailStats['categories']->sum('cnt'); @endphp
                                @foreach($detailStats['categories']->take(5) as $cat)
                                    @php
                                        $pct = $total > 0 ? round(($cat->cnt / $total) * 100) : 0;
                                        $catColors = ['mínima'=>'emerald','leve'=>'blue','moderada'=>'amber','severa'=>'red','normal'=>'emerald','baja'=>'amber','alta'=>'emerald'];
                                        $catKey = strtolower($cat->result_category ?? '');
                                        $catColor = 'gray';
                                        foreach($catColors as $k => $cv) { if(str_contains($catKey, $k)) { $catColor = $cv; break; } }
                                    @endphp
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-gray-600 w-24 truncate">{{ ucfirst($cat->result_category) }}</span>
                                        <div class="flex-1 bg-gray-100 rounded-full h-2 overflow-hidden">
                                            <div class="bg-{{ $catColor }}-500 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                                        </div>
                                        <span class="text-xs font-semibold text-gray-700 w-8 text-right">{{ $cat->cnt }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Advisors --}}
                    @if($detailStats['advisors']->isNotEmpty())
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Orientadores</h4>
                            <div class="space-y-2">
                                @foreach($detailStats['advisors'] as $advisor)
                                    <div class="flex items-center gap-2 p-2 bg-gray-50 rounded-lg">
                                        <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold shrink-0">
                                            {{ strtoupper(substr($advisor->first_name, 0, 1)) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-semibold text-gray-900 truncate">{{ $advisor->first_name }} {{ $advisor->last_name }}</p>
                                            <p class="text-xs text-gray-500">{{ $advisor->managedGroups->count() }} grupos · {{ $advisor->assignedTests->count() }} asignaciones</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Recent Users --}}
                    @if($detailStats['active_users']->isNotEmpty())
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Usuarios Recientes</h4>
                            <div class="space-y-1.5">
                                @foreach($detailStats['active_users'] as $u)
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold shrink-0">
                                            {{ strtoupper(substr($u->first_name, 0, 1)) }}
                                        </div>
                                        <p class="text-xs text-gray-700 truncate">{{ $u->first_name }} {{ $u->last_name }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Edit Button --}}
                    <button wire:click="edit({{ $detail->id }})"
                        class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-xl transition-colors">
                        Editar Área
                    </button>
                </div>
            </div>
        @endif

    </div>

    {{-- Modal CRUD --}}
    @if($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg" x-data @keydown.escape.window="$wire.closeModal()">
                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h2 class="text-lg font-bold text-gray-900">
                        {{ $areaId ? 'Editar Área' : 'Nueva Área' }}
                    </h2>
                    <button wire:click="closeModal" class="p-2 rounded-lg hover:bg-gray-100 text-gray-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="px-6 py-5 space-y-4">
                    {{-- Name --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="name" type="text" placeholder="Facultad de Ingeniería"
                            class="block w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors @error('name') border-red-400 @enderror"/>
                        @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Type + City --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                                Tipo
                            </label>
                            <select wire:model="type"
                                class="block w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors">
                                <option value="">Sin especificar</option>
                                @foreach($types as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                                Ciudad
                            </label>
                            <input wire:model="city" type="text" placeholder="Hermosillo"
                                class="block w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors"/>
                        </div>
                    </div>

                    {{-- Address --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                            Dirección
                        </label>
                        <input wire:model="address" type="text" placeholder="Blvd. Universidad 3000"
                            class="block w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors"/>
                    </div>

                    {{-- Phone + Status --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                                Teléfono
                            </label>
                            <input wire:model="phone" type="text" placeholder="(555) 000-0000"
                                class="block w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors @error('phone') border-red-400 @enderror"/>
                            @error('phone') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                                Estado
                            </label>
                            <select wire:model="active"
                                class="block w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors">
                                <option value="1">Activa</option>
                                <option value="0">Inactiva</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50/40 rounded-b-2xl">
                    <button wire:click="closeModal"
                        class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 rounded-lg font-medium transition-colors">
                        Cancelar
                    </button>
                    <button wire:click="store" wire:loading.attr="disabled"
                        class="px-5 py-2 text-sm text-white bg-teal-600 hover:bg-teal-700 disabled:opacity-50 rounded-lg font-semibold transition-colors shadow-sm">
                        <span wire:loading.remove wire:target="store">{{ $areaId ? 'Guardar cambios' : 'Crear área' }}</span>
                        <span wire:loading wire:target="store">Guardando...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
