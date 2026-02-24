<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Mis Resultados</h1>
            <p class="mt-1 text-sm text-gray-500">
                Historial completo de tus evaluaciones completadas
            </p>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <p class="text-sm text-gray-500 mb-1">Total Completados</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <p class="text-sm text-gray-500 mb-1">Este Mes</p>
            <p class="text-2xl font-bold text-teal-600">{{ $stats['this_month'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <p class="text-sm text-gray-500 mb-1">Este Año</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['this_year'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <p class="text-sm text-gray-500 mb-1">Puntaje Promedio</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['avg_score'] }}</p>
        </div>
    </div>

    {{-- Filtros y Búsqueda --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col sm:flex-row gap-4">
            {{-- Búsqueda --}}
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                        </svg>
                    </div>
                    <input
                        wire:model.live.debounce.300ms="search"
                        type="text"
                        placeholder="Buscar por nombre del test..."
                        class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg bg-gray-50 text-sm placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors"
                    >
                </div>
            </div>

            {{-- Filtro por Categoría --}}
            @if($categories->isNotEmpty())
                <div class="sm:w-64">
                    <select wire:model.live="filterCategory"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors">
                        <option value="">Todas las categorías</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}">{{ ucfirst($category) }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
    </div>

    {{-- Tabla de Resultados --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/60">
                        <th wire:click="sortBy('finished_at')" 
                            class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors group">
                            <div class="flex items-center gap-1">
                                Fecha
                                @if($sortField === 'finished_at')
                                    <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="m18 15-6-6-6 6"/>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Test
                        </th>
                        <th wire:click="sortBy('numeric_result')" 
                            class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors group">
                            <div class="flex items-center gap-1">
                                Puntaje
                                @if($sortField === 'numeric_result')
                                    <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="m18 15-6-6-6 6"/>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Resultado
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Asignado por
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($results as $response)
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
                            foreach ($categoryColors as $key => $color) {
                                if (str_contains($category, $key)) {
                                    $badgeColor = $color;
                                    break;
                                }
                            }
                            $badgeColor = $badgeColor ?? 'gray';
                        @endphp
                        <tr class="hover:bg-gray-50/70 transition-colors group">
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $response->finished_at->format('d/m/Y') }}
                                <p class="text-xs text-gray-500">{{ $response->finished_at->format('H:i') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-gray-900">{{ $response->assignment->test->name }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-gray-900">{{ $response->numeric_result }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-{{ $badgeColor }}-100 text-{{ $badgeColor }}-700">
                                    {{ ucfirst($response->result_category) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($response->assignment->assignedBy)
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold shrink-0">
                                            {{ strtoupper(substr($response->assignment->assignedBy->first_name, 0, 1)) }}
                                        </div>
                                        <span class="text-sm text-gray-700">
                                            {{ $response->assignment->assignedBy->first_name }}
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('results.show', $response->id) }}"
                                    class="text-sm text-teal-600 hover:text-teal-700 font-medium hover:underline">
                                    Ver Detalles
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-3 text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="1">
                                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-500">No hay resultados</p>
                                        @if($search)
                                            <p class="text-xs mt-1">Intenta ajustar los filtros de búsqueda</p>
                                        @else
                                            <p class="text-xs mt-1">Completa un test para ver tus resultados aquí</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="px-6 py-3 border-t border-gray-100 bg-gray-50/40">
            {{ $results->links() }}
        </div>
    </div>

</div>