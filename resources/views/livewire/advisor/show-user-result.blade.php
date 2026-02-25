<div class="space-y-6">

    {{-- Header con Navegación --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('advisor.results') }}" 
            class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m12 19-7-7 7-7"/>
                <path d="M19 12H5"/>
            </svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Resultado del Usuario</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $response->user->first_name }} {{ $response->user->last_name }} - {{ $response->assignment->test->name }}</p>
        </div>
    </div>

    {{-- Información del Usuario --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-start gap-6">
            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 text-white flex items-center justify-center text-2xl font-bold shrink-0">
                {{ strtoupper(substr($response->user->first_name, 0, 1)) }}{{ strtoupper(substr($response->user->last_name, 0, 1)) }}
            </div>
            <div class="flex-1">
                <h3 class="text-xl font-bold text-gray-900 mb-1">
                    {{ $response->user->first_name }} {{ $response->user->last_name }}
                </h3>
                <p class="text-sm text-gray-600 mb-3">{{ $response->user->email }}</p>
                <div class="flex flex-wrap gap-4 text-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span class="text-gray-700">{{ $response->user->institution->name }}</span>
                    </div>
                    @php
                        // Si es admin, mostrar todos los grupos del usuario
                        // Si es orientador, solo mostrar sus grupos
                        if (auth()->user()->role_id === 1) {
                            $userGroups = $response->user->groups;
                        } else {
                            $userGroups = $response->user->groups()->where('creator_id', auth()->id())->get();
                        }
                    @endphp
                    @if($userGroups->isNotEmpty())
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span class="text-gray-700">
                                {{ $userGroups->pluck('name')->join(', ') }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Resultado Principal --}}
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
            {{-- Métricas --}}
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
                    @php
                        $totalSeconds = $response->started_at->diffInSeconds($response->finished_at);
                        $minutes = floor($totalSeconds / 60);
                        $seconds = $totalSeconds % 60;
                    @endphp
                    <div class="text-4xl font-bold text-gray-900">
                        @if($totalSeconds < 60)
                            < 1m
                        @else
                            {{ $minutes }}m
                            @if($seconds > 0)
                                <span class="text-3xl text-gray-600"> {{ $seconds }}s</span>
                            @endif
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        @if($totalSeconds < 60)
                            Menos de un minuto
                        @elseif($seconds > 0)
                            {{ $minutes }} {{ $minutes == 1 ? 'minuto' : 'minutos' }} y {{ $seconds }} {{ $seconds == 1 ? 'segundo' : 'segundos' }}
                        @else
                            {{ $minutes }} {{ $minutes == 1 ? 'minuto' : 'minutos' }}
                        @endif
                    </p>
                </div>
            </div>

            {{-- Tendencia --}}
            @if($trend)
                <div class="mb-8 p-6 bg-gray-50 rounded-lg border-l-4 
                    {{ $trend['direction'] === 'up' ? 'border-red-500' : ($trend['direction'] === 'down' ? 'border-green-500' : 'border-gray-300') }}">
                    <h3 class="text-sm font-semibold text-gray-900 mb-2 flex items-center gap-2">
                        @if($trend['direction'] === 'up')
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                            <span class="text-red-600">Aumento en el puntaje</span>
                        @elseif($trend['direction'] === 'down')
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                            </svg>
                            <span class="text-green-600">Disminución en el puntaje</span>
                        @else
                            <span class="text-gray-600">Sin cambios</span>
                        @endif
                    </h3>
                    <p class="text-sm text-gray-700">
                        Comparado con el intento anterior ({{ $trend['previous_date']->format('d/m/Y') }}): 
                        <strong>{{ $trend['diff'] }} puntos</strong> {{ $trend['direction'] === 'up' ? 'más' : 'menos' }}
                        ({{ $trend['percent'] }}% {{ $trend['direction'] === 'up' ? 'aumento' : 'disminución' }})
                    </p>
                </div>
            @endif

            {{-- Recomendación para el Orientador --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                <h3 class="text-sm font-semibold text-blue-900 mb-2 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 16v-4"/>
                        <path d="M12 8h.01"/>
                    </svg>
                    Recomendación para Seguimiento
                </h3>
                <p class="text-sm text-blue-800">{{ $recommendation }}</p>
            </div>

            {{-- Asignado por --}}
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Test asignado por</h3>
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
        </div>
    </div>

    {{-- Historial del Usuario en este Test --}}
    @if($userHistory->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900">Historial en este Test</h3>
                <p class="text-xs text-gray-500 mt-1">Intentos anteriores del usuario</p>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @foreach($userHistory as $pastResponse)
                        @php
                            $category = strtolower($pastResponse->result_category ?? 'normal');
                            $color = 'gray';
                            foreach ($categoryColors as $key => $badgeColor) {
                                if (str_contains($category, $key)) {
                                    $color = $badgeColor;
                                    break;
                                }
                            }
                        @endphp
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-1">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $pastResponse->finished_at->format('d/m/Y H:i') }}
                                    </p>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-700">
                                        {{ ucfirst($pastResponse->result_category) }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500">
                                    Puntaje: <strong>{{ $pastResponse->numeric_result }}</strong>
                                </p>
                            </div>
                            <a href="{{ route('advisor.results.show', $pastResponse->id) }}" 
                                class="text-sm text-teal-600 hover:text-teal-700 font-medium hover:underline">
                                Ver
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

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

</div>