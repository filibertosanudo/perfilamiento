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
                                <button wire:click="showResultDetails({{ $response->id }})"
                                    class="text-sm text-teal-600 hover:text-teal-700 font-medium hover:underline">
                                    Ver Detalles
                                </button>
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
            {{ $results->links('vendor.pagination.tailwind') }}
        </div>
    </div>

    {{-- Detail Modal --}}
    @if($showDetailModal && $selectedResult)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl flex flex-col max-h-[90vh]" x-data @keydown.escape.window="$wire.closeModal()">
                
                {{-- Modal Header --}}
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
                    $category = strtolower($selectedResult->result_category ?? 'normal');
                    $badgeColor = 'gray';
                    foreach ($categoryColors as $key => $color) {
                        if (str_contains($category, $key)) {
                            $badgeColor = $color;
                            break;
                        }
                    }
                @endphp
                <div class="bg-gradient-to-r from-teal-600 to-teal-700 px-6 py-4 rounded-t-2xl text-white flex justify-between items-start shrink-0">
                    <div>
                        <h2 class="text-xl font-bold mb-1">{{ $selectedResult->assignment->test->name }}</h2>
                        <div class="flex items-center gap-3 text-teal-100 text-sm">
                            <span>Puntaje: {{ $selectedResult->numeric_result }} / {{ $selectedResult->assignment->test->max_score }}</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-{{ $badgeColor }}-100 text-{{ $badgeColor }}-700">
                                {{ ucfirst($selectedResult->result_category) }}
                            </span>
                        </div>
                    </div>
                    <button wire:click="closeModal" class="p-2 rounded-lg hover:bg-white/20 text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Modal Body (Scrollable) --}}
                <div class="p-6 overflow-y-auto space-y-4 flex-1">
                    @foreach($selectedDetails as $index => $detail)
                        <div class="pb-4 border-b border-gray-100 last:border-0 last:pb-0">
                            <div class="flex items-start gap-3">
                                <div class="w-7 h-7 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900 mb-2">
                                        {{ $detail->question->text }}
                                    </h4>
                                    <div class="bg-teal-50 border border-teal-200 rounded-lg p-3 flex justify-between items-center">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-teal-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-sm font-medium text-teal-900">
                                                {{ $detail->answerOption->text }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/40 rounded-b-2xl shrink-0 flex justify-between items-center">
                    <a href="{{ route('results.show', $selectedResult->id) }}" class="text-sm text-teal-600 hover:text-teal-700 font-medium hover:underline">
                        Ver análisis completo
                    </a>
                    <a href="{{ route('pdf.test-result', $selectedResult->id) }}" target="_blank" download class="text-sm text-indigo-600 hover:text-indigo-700 font-medium hover:underline">
                        Descargar PDF
                    </a>
                    <button wire:click="closeModal" class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 rounded-lg font-medium transition-colors">
                        Cerrar
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>