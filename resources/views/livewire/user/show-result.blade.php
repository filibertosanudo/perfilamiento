<div class="space-y-6">

    {{-- Header con Navegación --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('results.index') }}" 
            class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m12 19-7-7 7-7"/>
                <path d="M19 12H5"/>
            </svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Resultados del Test</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $response->assignment->test->name }}</p>
        </div>
    </div>

    {{-- Información General --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-teal-600 to-teal-700 px-8 py-6 text-white">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-2xl font-bold mb-2">{{ $response->assignment->test->name }}</h2>
                    <p class="text-teal-100 text-sm">
                        Completado el {{ $response->finished_at->format('d/m/Y') }} a las {{ $response->finished_at->format('H:i') }}
                    </p>
                </div>
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
                    $badgeColor = 'gray';
                    foreach ($categoryColors as $key => $color) {
                        if (str_contains($category, $key)) {
                            $badgeColor = $color;
                            break;
                        }
                    }
                @endphp
                <div class="text-right">
                    <p class="text-teal-100 text-sm mb-1">Categoría</p>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-{{ $badgeColor }}-100 text-{{ $badgeColor }}-700">
                        {{ ucfirst($response->result_category) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="text-center p-6 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-500 mb-2">Puntaje Total</p>
                    <p class="text-4xl font-bold text-gray-900">{{ $response->numeric_result }}</p>
                    <p class="text-xs text-gray-500 mt-1">de {{ $response->assignment->test->max_score }} puntos</p>
                </div>
                <div class="text-center p-6 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-500 mb-2">Preguntas Respondidas</p>
                    <p class="text-4xl font-bold text-gray-900">{{ $scoreBySection['questions'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">de {{ $response->assignment->test->total_questions }} preguntas</p>
                </div>
                <div class="text-center p-6 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-500 mb-2">Tiempo Utilizado</p>
                    <p class="text-4xl font-bold text-gray-900">
                        {{ $response->started_at->diffInMinutes($response->finished_at) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">minutos</p>
                </div>
            </div>

            {{-- Recomendación --}}
            @if($recommendation)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                    <h3 class="text-sm font-semibold text-blue-900 mb-2 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2v20M2 12h20"/>
                        </svg>
                        Recomendación
                    </h3>
                    <p class="text-sm text-blue-800">{{ $recommendation }}</p>
                </div>
            @endif

            {{-- Información del Orientador --}}
            @if($response->assignment->assignedBy)
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Asignado por</h3>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm font-bold">
                            {{ strtoupper(substr($response->assignment->assignedBy->first_name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $response->assignment->assignedBy->first_name }} {{ $response->assignment->assignedBy->last_name }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $response->assignment->assignedBy->email }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Detalle de Respuestas --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900">Detalle de Respuestas</h3>
        </div>
        <div class="p-6 space-y-6">
            @foreach($details as $index => $detail)
                <div class="pb-6 border-b border-gray-100 last:border-0 last:pb-0">
                    <div class="flex items-start gap-3 mb-3">
                        <div class="w-8 h-8 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center text-sm font-bold shrink-0">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1">
                            <h4 class="text-base font-medium text-gray-900 mb-2">
                                {{ $detail->question->text }}
                            </h4>
                            <div class="bg-teal-50 border border-teal-200 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-teal-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-sm font-medium text-teal-900">
                                            {{ $detail->answerOption->text }}
                                        </span>
                                    </div>
                                    <span class="text-sm font-semibold text-teal-700">
                                        {{ $detail->answerOption->weight ?? 0 }} pts
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Opciones de Re-intento --}}
    @if($canRetake)
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-6">
            <div class="flex items-start gap-4">
                <svg class="w-6 h-6 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-amber-900 mb-1">Disponible para Re-intento</h3>
                    <p class="text-sm text-amber-800 mb-3">
                        Han pasado {{ $response->finished_at->diffInDays(now()) }} días desde tu último intento. 
                        Puedes volver a realizar este test si tu orientador te lo asigna nuevamente.
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
            <div class="flex items-start gap-4">
                <svg class="w-6 h-6 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-1">Tiempo de Espera para Re-intento</h3>
                    <p class="text-sm text-gray-600">
                        Debes esperar {{ $response->assignment->test->minimum_retest_time }} días entre intentos. 
                        Podrás realizar este test nuevamente después del 
                        {{ $response->finished_at->addDays($response->assignment->test->minimum_retest_time)->format('d/m/Y') }}.
                    </p>
                </div>
            </div>
        </div>
    @endif

</div>