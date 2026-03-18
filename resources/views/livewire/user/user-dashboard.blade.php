<div class="space-y-6">
    
    {{-- Header --}}
    <div x-data="{ 
        getGreeting() {
            const hour = new Date().getHours();
            if (hour < 12) return 'Buenos días';
            if (hour < 19) return 'Buenas tardes';
            return 'Buenas noches';
        }
    }">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
            <span x-text="getGreeting()"></span>, {{ auth()->user()->first_name }}
        </h1>
        <p class="text-gray-500">{{ $motivationalQuote }}</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- Tests Pendientes --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex flex-row items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-500">Tests Pendientes</h3>
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <div class="text-3xl font-bold text-gray-900">{{ $stats['pending_count'] }}</div>
                @if($nextDueDate && $nextDueDate['assignment']->due_date)
                    <p class="text-xs text-amber-600 mt-1 font-medium">
                        Próximo vence {{ $nextDueDate['assignment']->time_remaining }}
                    </p>
                @else
                    <p class="text-xs text-gray-400 mt-1">Sin fechas próximas</p>
                @endif
            </div>
        </div>

        {{-- En Progreso --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex flex-row items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-500">En Progreso</h3>
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
            </div>
            <div>
                <div class="text-3xl font-bold text-gray-900">{{ $stats['in_progress_count'] }}</div>
                @if($inProgressTests->isNotEmpty())
                    @php
                        $avgProgress = round($inProgressTests->avg('progress'));
                    @endphp
                    <p class="text-xs text-gray-500 mt-1">{{ $avgProgress }}% completado en promedio</p>
                @else
                    <p class="text-xs text-gray-400 mt-1">Ninguno iniciado</p>
                @endif
            </div>
        </div>

        {{-- Completados --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex flex-row items-center justify-between pb-2">
                <h3 class="text-sm font-medium text-gray-500">Completados</h3>
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <div class="text-3xl font-bold text-gray-900">{{ $stats['completed_count'] }}</div>
                @if($stats['completed_this_month'] > 0)
                    <p class="text-xs text-emerald-600 mt-1 font-medium">
                        {{ $stats['completed_this_month'] }} este mes
                    </p>
                @else
                    <p class="text-xs text-gray-400 mt-1">Ninguno este mes</p>
                @endif
            </div>
        </div>
    </div>
    
    {{-- Header de Resultados --}}
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-gray-900">Mis Resultados</h2>
        <div class="flex gap-2">
            {{-- PDF de Historial --}}
            <a href="{{ route('pdf.user-history') }}" target="_blank"
            class="inline-flex items-center gap-2 border border-gray-300 hover:border-teal-600 hover:text-teal-600 px-4 py-2 rounded-lg text-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Historial
            </a>

            {{-- PDF Integral (solo si tiene 3+ tests) --}}
            @if($completedTestsCount >= 3)
                <a href="{{ route('pdf.user-integral') }}" target="_blank"
                class="inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Reporte Integral
                </a>
            @endif
        </div>
    </div>

    {{-- Recordatorio --}}
    @if($stats['pending_count'] > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 border-l-4 border-l-blue-500 p-6">
            <div class="flex flex-row items-start gap-4">
                <svg class="w-6 h-6 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Recordatorio Importante</h3>
                    <p class="text-sm text-gray-600">
                        Tienes {{ $stats['pending_count'] }} {{ $stats['pending_count'] === 1 ? 'evaluación pendiente' : 'evaluaciones pendientes' }}. 
                        Recuerda que estas evaluaciones nos ayudan a brindarte mejor apoyo.
                        Los resultados son confidenciales y solo tu orientador tiene acceso a ellos.
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Tests Asignados --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900">Tests Asignados</h3>
        </div>
        
        @if($pendingTests->isEmpty() && $inProgressTests->isEmpty())
            <div class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No tienes tests pendientes</h3>
                <p class="text-sm text-gray-500">Tu orientador te asignará tests cuando sea necesario.</p>
            </div>
        @else
            <div class="p-6 space-y-4">
                
                {{-- Tests en Progreso --}}
                @foreach($inProgressTests as $item)
                    <div class="p-4 border border-blue-200 rounded-lg bg-blue-50 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h4 class="text-base font-semibold text-gray-900 mb-1">{{ $item['test']->name }}</h4>
                                <div class="flex items-center gap-4 text-xs text-gray-600">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Vence: {{ $item['assignment']->due_date->format('d/m/Y') }}
                                    </span>
                                    <span class="text-blue-600 font-medium">En progreso</span>
                                </div>
                            </div>
                            <a href="{{ route('tests.take', $item['assignment']->id) }}" 
                                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-md transition-colors">
                                Continuar
                            </a>
                        </div>
                        <div class="space-y-1">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $item['progress'] }}%"></div>
                            </div>
                            <p class="text-xs text-gray-600 text-right">{{ round($item['progress']) }}% completado</p>
                        </div>
                    </div>
                @endforeach

                {{-- Tests Pendientes --}}
                @foreach($pendingTests as $item)
                    @php
                        $urgencyColor = $item['assignment']->urgency_color;
                        $badgeClasses = [
                            'red' => 'text-red-600',
                            'amber' => 'text-amber-600',
                            'gray' => 'text-gray-600',
                        ];
                    @endphp
                    <div class="p-4 border border-gray-200 rounded-lg hover:shadow-md transition-shadow bg-white">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h4 class="text-base font-semibold text-gray-900 mb-1">{{ $item['test']->name }}</h4>
                                <div class="flex items-center gap-4 text-xs">
                                    <span class="flex items-center gap-1 text-gray-500">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Vence: {{ $item['assignment']->due_date->format('d/m/Y') }}
                                    </span>
                                    <span class="font-medium {{ $badgeClasses[$urgencyColor] }}">
                                        {{ $item['assignment']->time_remaining }}
                                    </span>
                                </div>
                            </div>
                            <a href="{{ route('tests.take', $item['assignment']->id) }}" 
                                class="bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium py-2 px-4 rounded-md transition-colors">
                                Comenzar
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Tests Completados Recientes --}}
    @if($completedTests->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900">Tests Completados Recientes</h3>
                <a href="{{ route('results.index') }}" class="text-sm text-gray-600 hover:text-gray-900 border border-gray-300 px-3 py-1 rounded-md transition-colors bg-white">
                    Ver Todos
                </a>
            </div>
            <div class="p-6 space-y-3">
                @foreach($completedTests as $response)
                    @php
                        $categoryColors = [
                            'mínima' => 'emerald',
                            'leve' => 'blue',
                            'moderada' => 'amber',
                            'severa' => 'red',
                            'normal' => 'emerald',
                            'bajo' => 'amber',
                            'alta' => 'emerald',
                            'baja' => 'amber',
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
                    <div class="flex items-center justify-between p-4 bg-{{ $color }}-50 rounded-lg border border-{{ $color }}-100">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-{{ $color }}-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $response->assignment->test->name }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Completado el {{ $response->finished_at->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right flex items-center">
                            <span class="text-sm font-medium text-{{ $color }}-700 mr-3">
                                {{ ucfirst($response->result_category ?? 'Normal') }}
                            </span>
                            <button wire:click="showResultDetails({{ $response->id }})" class="text-sm text-gray-600 hover:text-gray-900 hover:underline">
                                Ver Resultados
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

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

                {{-- Modal Footer --}}
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/40 rounded-b-2xl shrink-0 flex justify-between items-center">
                    <a href="{{ route('results.show', $selectedResult->id) }}" class="text-sm text-teal-600 hover:text-teal-700 font-medium hover:underline">
                        Ver análisis completo
                    </a>
                    <button wire:click="closeModal" class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 rounded-lg font-medium transition-colors">
                        Cerrar
                    </button>
                </div>

            </div>
        </div>
    @endif
</div>