@if(!$isStarted)
    {{-- Pantalla de Inicio --}}
    <div class="py-12">
        <div class="max-w-3xl mx-auto px-4">
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                
                {{-- Header --}}
                <div class="bg-gradient-to-r from-teal-600 to-teal-700 px-8 py-6 text-white">
                    <h1 class="text-2xl font-bold mb-2">{{ $assignment->test->name }}</h1>
                    <p class="text-teal-100 text-sm">
                        {{ $assignment->test->total_questions }} preguntas · 
                        Tiempo estimado: {{ $assignment->test->estimated_time }} minutos
                    </p>
                </div>

                {{-- Body --}}
                <div class="p-8 space-y-6">
                    
                    {{-- Objetivo --}}
                    @if($assignment->test->objective)
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Objetivo</h3>
                            <p class="text-gray-700 leading-relaxed">{{ $assignment->test->objective }}</p>
                        </div>
                    @endif

                    {{-- Descripción --}}
                    @if($assignment->test->description)
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Descripción</h3>
                            <p class="text-gray-700 leading-relaxed">{{ $assignment->test->description }}</p>
                        </div>
                    @endif

                    {{-- Instrucciones --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-blue-900 mb-2 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 16v-4"/>
                                <path d="M12 8h.01"/>
                            </svg>
                            Instrucciones Importantes
                        </h3>
                        <ul class="text-sm text-blue-800 space-y-1.5">
                            <li class="flex items-start gap-2">
                                <span class="text-blue-600 mt-0.5">•</span>
                                <span>Responde con honestidad, no hay respuestas correctas o incorrectas</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-blue-600 mt-0.5">•</span>
                                <span>Puedes guardar tu progreso y continuar más tarde</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-blue-600 mt-0.5">•</span>
                                <span>Tienes hasta el {{ $assignment->due_date->format('d/m/Y') }} para completarlo</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-blue-600 mt-0.5">•</span>
                                <span>Tus resultados son confidenciales</span>
                            </li>
                        </ul>
                    </div>

                    {{-- Si ya tiene progreso --}}
                    @if($response && !$response->completed)
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-amber-900 mb-2">Continuar donde lo dejaste</h3>
                            <p class="text-sm text-amber-800 mb-3">
                                Ya has respondido {{ count($answers) }} de {{ $questions->count() }} preguntas 
                                ({{ round($progress) }}% completado)
                            </p>
                            <div class="w-full bg-amber-200 rounded-full h-2">
                                <div class="bg-amber-600 h-2 rounded-full" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>
                    @endif

                </div>

                {{-- Footer --}}
                <div class="px-8 py-6 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                    <a href="{{ route('dashboard') }}" 
                        class="text-sm text-gray-600 hover:text-gray-900 font-medium flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m12 19-7-7 7-7"/>
                            <path d="M19 12H5"/>
                        </svg>
                        Volver al Dashboard
                    </a>
                    <button wire:click="startTest"
                        class="bg-teal-600 hover:bg-teal-700 text-white font-semibold px-6 py-3 rounded-lg transition-colors shadow-sm">
                        {{ $response && !$response->completed ? 'Continuar Test' : 'Comenzar Test' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

@elseif($isCompleted)
    {{-- Pantalla de Test Completado --}}
    <div class="py-12">
        <div class="max-w-3xl mx-auto px-4">
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                
                <div class="p-12 text-center">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>

                    <h2 class="text-3xl font-bold text-gray-900 mb-3">¡Test Completado!</h2>
                    <p class="text-gray-600 mb-2">Has completado exitosamente el test</p>
                    <p class="text-2xl font-semibold text-teal-600 mb-8">{{ $assignment->test->name }}</p>

                    <div class="bg-gray-50 rounded-lg p-6 mb-8 max-w-md mx-auto">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Resultado</p>
                                <p class="text-lg font-bold text-gray-900">{{ $response->result_category }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Puntaje</p>
                                <p class="text-lg font-bold text-gray-900">{{ $response->numeric_result }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8 text-left max-w-md mx-auto">
                        <p class="text-sm text-blue-800">
                            <strong>Nota:</strong> Tu orientador revisará estos resultados y podrá proporcionarte 
                            retroalimentación personalizada. Los resultados son confidenciales.
                        </p>
                    </div>

                    <a href="{{ route('dashboard') }}" 
                        class="inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white font-semibold px-6 py-3 rounded-lg transition-colors">
                        Volver al Dashboard
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14"/>
                            <path d="m12 5 7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

@else
    {{-- Pantalla de Responder Preguntas --}}
    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4">
            
            {{-- Barra de Progreso --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">
                        Pregunta {{ $currentQuestionIndex + 1 }} de {{ $questions->count() }}
                    </span>
                    <span class="text-sm text-gray-500">{{ round($progress) }}% completado</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-teal-600 h-2 rounded-full transition-all duration-300" 
                        style="width: {{ $progress }}%"></div>
                </div>
            </div>

            {{-- Pregunta Actual --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                
                <div class="p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-8">
                        {{ $currentQuestion->text }}
                    </h2>

                    <div class="space-y-3">
                        @foreach($currentQuestion->answerOptions as $option)
                            @php
                                $isSelected = isset($answers[$currentQuestion->id]) && 
                                             $answers[$currentQuestion->id] === $option->id;
                            @endphp
                            <button 
                                wire:click="answerQuestion({{ $option->id }})"
                                class="w-full text-left p-4 border-2 rounded-lg transition-all hover:border-teal-500 hover:bg-teal-50
                                    {{ $isSelected ? 'border-teal-600 bg-teal-50' : 'border-gray-200 bg-white' }}">
                                <div class="flex items-center gap-3">
                                    <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center shrink-0 transition-colors
                                        {{ $isSelected ? 'border-teal-600 bg-teal-600' : 'border-gray-300' }}">
                                        @if($isSelected)
                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <span class="text-base {{ $isSelected ? 'text-teal-900 font-medium' : 'text-gray-700' }}">
                                        {{ $option->text }}
                                    </span>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Navegación --}}
                <div class="px-8 py-6 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                    <div class="flex gap-2">
                        @if($currentQuestionIndex > 0)
                            <button wire:click="previousQuestion"
                                class="text-sm text-gray-600 hover:text-gray-900 font-medium flex items-center gap-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-white transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m15 18-6-6 6-6"/>
                                </svg>
                                Anterior
                            </button>
                        @endif

                        <button wire:click="saveAndExit"
                            class="text-sm text-gray-600 hover:text-gray-900 font-medium px-4 py-2 border border-gray-300 rounded-lg hover:bg-white transition-colors">
                            Guardar y Salir
                        </button>
                    </div>

                    @if(isset($answers[$currentQuestion->id]))
                        @if($currentQuestionIndex < $questions->count() - 1)
                            <button wire:click="nextQuestion"
                                class="bg-teal-600 hover:bg-teal-700 text-white font-medium px-6 py-2 rounded-lg transition-colors flex items-center gap-2">
                                Siguiente
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m9 18 6-6-6-6"/>
                                </svg>
                            </button>
                        @else
                            <button wire:click="finishTest"
                                wire:loading.attr="disabled"
                                class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-2 rounded-lg transition-colors flex items-center gap-2 shadow-sm">
                                <span wire:loading.remove wire:target="finishTest">Finalizar Test</span>
                                <span wire:loading wire:target="finishTest">Procesando...</span>
                                <svg wire:loading.remove wire:target="finishTest" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 12l5 5L20 7"/>
                                </svg>
                            </button>
                        @endif
                    @else
                        <span class="text-sm text-gray-400">Selecciona una opción para continuar</span>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endif