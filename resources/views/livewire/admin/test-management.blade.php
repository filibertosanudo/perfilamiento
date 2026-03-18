<div>
    {{-- Flash Messages --}}
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="mb-6 p-4 bg-teal-50 border border-teal-200 rounded-2xl flex items-center gap-3">
            <svg class="w-5 h-5 text-teal-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-sm font-medium text-teal-800">{{ session('message') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="mb-6 p-4 bg-red-50 border border-red-200 rounded-2xl flex items-center gap-3">
            <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-sm font-medium text-red-800">{{ session('error') }}</span>
        </div>
    @endif

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Gestión de Tests</h2>
            <p class="text-sm text-gray-500 mt-1">Crea y administra los tests psicológicos del sistema</p>
        </div>
        <button wire:click="create"
                class="px-6 py-2.5 text-sm text-white bg-teal-600 hover:bg-teal-700 rounded-xl font-bold transition-all duration-200 shadow-lg shadow-teal-500/20 active:scale-95 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Crear Test
        </button>
    </div>

    {{-- FILTERS --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
        <div class="flex flex-col sm:flex-row items-center gap-4">
            <div class="relative flex-1 w-full">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por nombre o descripción..."
                       class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-100 rounded-xl bg-gray-50 focus:bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all" />
            </div>
            <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer whitespace-nowrap">
                <input wire:model.live="showInactive" type="checkbox" class="rounded border-gray-300 text-teal-600 focus:ring-teal-500" />
                Mostrar inactivos
            </label>
        </div>
    </div>

    {{-- TESTS TABLE --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/50">
                        <th wire:click="sortBy('id')" class="px-6 py-3.5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest cursor-pointer hover:text-gray-600 transition-colors">
                            ID
                            @if ($sortField === 'id')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th wire:click="sortBy('name')" class="px-6 py-3.5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest cursor-pointer hover:text-gray-600 transition-colors">
                            Nombre
                            @if ($sortField === 'name')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-6 py-3.5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Preguntas</th>
                        <th class="px-6 py-3.5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Puntaje Máx.</th>
                        <th class="px-6 py-3.5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Tiempo Est.</th>
                        <th class="px-6 py-3.5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Estado</th>
                        <th class="px-6 py-3.5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($tests as $test)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 text-gray-500 font-mono text-xs">#{{ $test->id }}</td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $test->name }}</p>
                                    @if($test->description)
                                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ Str::limit($test->description, 80) }}</p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-gray-100 text-xs font-bold text-gray-700">
                                    {{ $test->questions_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-bold text-teal-700">{{ $test->max_score }}</span>
                            </td>
                            <td class="px-6 py-4 text-center text-gray-600">
                                {{ $test->estimated_time ? $test->estimated_time . ' min' : '—' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button wire:click="toggleActive({{ $test->id }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold transition-all
                                        {{ $test->active ? 'bg-teal-50 text-teal-700 hover:bg-teal-100' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $test->active ? 'bg-teal-500' : 'bg-gray-400' }}"></span>
                                    {{ $test->active ? 'Activo' : 'Inactivo' }}
                                </button>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="viewTest({{ $test->id }})" class="p-2 text-gray-400 hover:text-teal-600 hover:bg-teal-50 rounded-lg transition-all" title="Ver">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                    <button wire:click="edit({{ $test->id }})" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button wire:click="deleteTest({{ $test->id }})" wire:confirm="¿Estás seguro de eliminar este test? Esta acción no se puede deshacer."
                                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Eliminar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="p-4 bg-gray-100 rounded-2xl">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    </div>
                                    <p class="text-sm text-gray-500">No se encontraron tests</p>
                                    <button wire:click="create" class="text-sm text-teal-600 hover:text-teal-700 font-bold">Crear el primer test →</button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tests->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $tests->links() }}
            </div>
        @endif
    </div>

    {{-- ========================================== --}}
    {{-- MODAL: CREATE/EDIT TEST (MULTI-STEP WIZARD) --}}
    {{-- ========================================== --}}

    @if($isOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto" x-data x-trap.inert.noscroll="true">
        {{-- Overlay --}}
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" wire:click="closeModal"></div>

        {{-- Modal Content --}}
        <div class="flex items-start justify-center min-h-screen px-4 pt-8 pb-20">
            <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-4xl border border-gray-100 overflow-hidden"
                 @click.away="$wire.closeModal()">

                {{-- Modal Header --}}
                <div class="bg-gradient-to-r from-teal-600 to-teal-700 px-8 py-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold">
                                @if($isViewMode) Ver Test
                                @elseif($testId) Editar Test
                                @else Crear Nuevo Test
                                @endif
                            </h3>
                            <p class="text-teal-100 text-sm mt-1">
                                @if($isViewMode) Visualización de detalles del test
                                @else Completa los 3 pasos para configurar el test
                                @endif
                            </p>
                        </div>
                        <button wire:click="closeModal" class="p-2 hover:bg-white/10 rounded-xl transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Step Indicators --}}
                    <div class="flex items-center gap-4 mt-6">
                        @foreach([['num' => 1, 'label' => 'Información'], ['num' => 2, 'label' => 'Preguntas'], ['num' => 3, 'label' => 'Puntuación']] as $step)
                            <button wire:click="goToStep({{ $step['num'] }})"
                                    class="flex items-center gap-2 text-sm transition-all
                                    {{ $currentStep === $step['num'] ? 'text-white font-bold' : ($currentStep > $step['num'] ? 'text-teal-200' : 'text-teal-300/60') }}">
                                <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-all
                                    {{ $currentStep === $step['num'] ? 'bg-white text-teal-700 border-white' : ($currentStep > $step['num'] ? 'bg-teal-500 border-teal-400 text-white' : 'border-teal-400/50') }}">
                                    @if($currentStep > $step['num'])
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    @else
                                        {{ $step['num'] }}
                                    @endif
                                </span>
                                <span class="hidden sm:inline">{{ $step['label'] }}</span>
                            </button>
                            @if(!$loop->last)
                                <div class="flex-1 h-px {{ $currentStep > $step['num'] ? 'bg-teal-300' : 'bg-teal-500/30' }}"></div>
                            @endif
                        @endforeach
                    </div>
                </div>

                {{-- Modal Body --}}
                <div class="px-8 py-6 max-h-[60vh] overflow-y-auto">

                    {{-- ======================== --}}
                    {{-- STEP 1: TEST INFORMATION --}}
                    {{-- ======================== --}}
                    @if($currentStep === 1)
                    <div class="space-y-6">
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Nombre del Test *</label>
                            <input wire:model="testName" type="text" @if($isViewMode) disabled @endif
                                   class="w-full border-gray-100 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all shadow-sm disabled:opacity-60"
                                   placeholder="Ej: Escala de Ansiedad Generalizada (GAD-7)" />
                            @error('testName') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Descripción</label>
                            <textarea wire:model="testDescription" rows="3" @if($isViewMode) disabled @endif
                                      class="w-full border-gray-100 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all shadow-sm disabled:opacity-60 resize-none"
                                      placeholder="Descripción del test..."></textarea>
                        </div>

                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Objetivo</label>
                            <textarea wire:model="testObjective" rows="2" @if($isViewMode) disabled @endif
                                      class="w-full border-gray-100 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all shadow-sm disabled:opacity-60 resize-none"
                                      placeholder="¿Qué busca evaluar este test?"></textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Tiempo estimado (min)</label>
                                <input wire:model="estimatedTime" type="number" min="1" @if($isViewMode) disabled @endif
                                       class="w-full border-gray-100 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all shadow-sm disabled:opacity-60"
                                       placeholder="5" />
                            </div>
                            <div>
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Reprueba mínima (días)</label>
                                <input wire:model="minimumRetestTime" type="number" min="1" @if($isViewMode) disabled @endif
                                       class="w-full border-gray-100 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all shadow-sm disabled:opacity-60"
                                       placeholder="180" />
                            </div>
                            <div>
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Estado</label>
                                <select wire:model="testActive" @if($isViewMode) disabled @endif
                                        class="w-full border-gray-100 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all shadow-sm disabled:opacity-60">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- ======================== --}}
                    {{-- STEP 2: QUESTIONS --}}
                    {{-- ======================== --}}
                    @if($currentStep === 2)
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-bold text-gray-900">Preguntas del Test</h4>
                                <p class="text-xs text-gray-500 mt-0.5">{{ count($questions) }} pregunta(s) configurada(s)</p>
                            </div>
                            @if(!$isViewMode)
                                <button wire:click="addQuestion"
                                        class="px-4 py-2 text-xs text-teal-700 bg-teal-50 hover:bg-teal-100 rounded-xl font-bold transition-all flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Agregar Pregunta
                                </button>
                            @endif
                        </div>

                        @foreach($questions as $qIndex => $question)
                            <div class="bg-gray-50 border border-gray-100 rounded-2xl p-5 space-y-4" wire:key="question-{{ $qIndex }}">
                                {{-- Question Header --}}
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3 flex-1">
                                        <span class="w-8 h-8 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center text-xs font-black flex-shrink-0">
                                            {{ $qIndex + 1 }}
                                        </span>
                                        <input wire:model="questions.{{ $qIndex }}.text" type="text" @if($isViewMode) disabled @endif
                                               class="flex-1 border-gray-100 rounded-xl px-4 py-2.5 bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all text-sm shadow-sm disabled:opacity-60"
                                               placeholder="Escribe la pregunta..." />
                                    </div>
                                    @if(!$isViewMode)
                                        <div class="flex items-center gap-1 flex-shrink-0">
                                            <button wire:click="moveQuestionUp({{ $qIndex }})" class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-200 rounded-lg transition-all {{ $qIndex === 0 ? 'opacity-30 cursor-not-allowed' : '' }}" @if($qIndex === 0) disabled @endif>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                            </button>
                                            <button wire:click="moveQuestionDown({{ $qIndex }})" class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-200 rounded-lg transition-all {{ $qIndex === count($questions) - 1 ? 'opacity-30 cursor-not-allowed' : '' }}" @if($qIndex === count($questions) - 1) disabled @endif>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                            <button wire:click="removeQuestion({{ $qIndex }})" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" {{ count($questions) <= 1 ? 'disabled' : '' }}>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    @endif
                                </div>

                                {{-- Answer Type Selector --}}
                                <div class="flex items-center gap-3">
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">Tipo de respuesta:</label>
                                    <select wire:change="changeAnswerType({{ $qIndex }}, $event.target.value)" @if($isViewMode) disabled @endif
                                            class="text-xs border-gray-100 rounded-lg px-3 py-1.5 bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all disabled:opacity-60">
                                        @foreach($likertPresets as $key => $preset)
                                            <option value="{{ $key }}" {{ $question['answer_type'] === $key ? 'selected' : '' }}>{{ $preset['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Answer Options --}}
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Opciones de Respuesta</label>
                                    @foreach($question['options'] as $oIndex => $option)
                                        <div class="flex items-center gap-2" wire:key="option-{{ $qIndex }}-{{ $oIndex }}">
                                            <span class="w-6 h-6 rounded-md bg-gray-200 text-gray-500 flex items-center justify-center text-[10px] font-black flex-shrink-0">
                                                {{ chr(65 + $oIndex) }}
                                            </span>
                                            <input wire:model="questions.{{ $qIndex }}.options.{{ $oIndex }}.text" type="text" @if($isViewMode) disabled @endif
                                                   class="flex-1 border-gray-100 rounded-lg px-3 py-2 bg-white text-xs focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all shadow-sm disabled:opacity-60"
                                                   placeholder="Texto de la opción" />
                                            <div class="flex items-center gap-1">
                                                <label class="text-[9px] font-bold text-gray-400 uppercase">Peso:</label>
                                                <input wire:model="questions.{{ $qIndex }}.options.{{ $oIndex }}.weight" type="number" step="0.5" @if($isViewMode) disabled @endif
                                                       class="w-16 border-gray-100 rounded-lg px-2 py-2 bg-white text-xs text-center focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all shadow-sm disabled:opacity-60" />
                                            </div>
                                            @if(!$isViewMode && count($question['options']) > 2)
                                                <button wire:click="removeOption({{ $qIndex }}, {{ $oIndex }})" class="p-1 text-gray-400 hover:text-red-500 transition-all">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            @endif
                                        </div>
                                    @endforeach
                                    @if(!$isViewMode)
                                        <button wire:click="addOption({{ $qIndex }})"
                                                class="text-xs text-teal-600 hover:text-teal-700 font-bold flex items-center gap-1 mt-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            Agregar opción
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- ======================== --}}
                    {{-- STEP 3: SCORING RANGES --}}
                    {{-- ======================== --}}
                    @if($currentStep === 3)
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-bold text-gray-900">Rangos de Puntuación</h4>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Puntaje máximo posible: <span class="font-bold text-teal-700">{{ $this->calculatedMaxScore }}</span>
                                </p>
                            </div>
                            @if(!$isViewMode)
                                <div class="flex items-center gap-2">
                                    <button wire:click="autoSuggestRanges"
                                            class="px-4 py-2 text-xs text-gray-600 hover:text-gray-900 border border-gray-200 rounded-xl font-bold transition-all flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        Auto-generar
                                    </button>
                                    <button wire:click="addRecommendation"
                                            class="px-4 py-2 text-xs text-teal-700 bg-teal-50 hover:bg-teal-100 rounded-xl font-bold transition-all flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Agregar Rango
                                    </button>
                                </div>
                            @endif
                        </div>

                        @if(empty($recommendations))
                            <div class="text-center py-8">
                                <div class="p-3 bg-gray-100 rounded-2xl inline-flex mb-3">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                </div>
                                <p class="text-sm text-gray-500">No hay rangos configurados.</p>
                                @if(!$isViewMode)
                                    <p class="text-xs text-gray-400 mt-1">Usa "Auto-generar" para crear rangos automáticos o agrégalos manualmente.</p>
                                @endif
                            </div>
                        @else
                            @foreach($recommendations as $rIndex => $rec)
                                <div class="bg-gray-50 border border-gray-100 rounded-2xl p-5 space-y-4" wire:key="rec-{{ $rIndex }}">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-gray-700">Rango {{ $rIndex + 1 }}</span>
                                        @if(!$isViewMode)
                                            <button wire:click="removeRecommendation({{ $rIndex }})" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        @endif
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                                        <div>
                                            <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Mín</label>
                                            <input wire:model="recommendations.{{ $rIndex }}.min_range" type="number" step="0.5" @if($isViewMode) disabled @endif
                                                   class="w-full border-gray-100 rounded-lg px-3 py-2 bg-white text-sm focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all shadow-sm disabled:opacity-60" />
                                        </div>
                                        <div>
                                            <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Máx</label>
                                            <input wire:model="recommendations.{{ $rIndex }}.max_range" type="number" step="0.5" @if($isViewMode) disabled @endif
                                                   class="w-full border-gray-100 rounded-lg px-3 py-2 bg-white text-sm focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all shadow-sm disabled:opacity-60" />
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Categoría</label>
                                            <input wire:model="recommendations.{{ $rIndex }}.result_category" type="text" @if($isViewMode) disabled @endif
                                                   class="w-full border-gray-100 rounded-lg px-3 py-2 bg-white text-sm focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all shadow-sm disabled:opacity-60"
                                                   placeholder="Ej: Mínima, Leve, Moderada..." />
                                        </div>
                                    </div>
                                    <div>
                                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Texto de recomendación</label>
                                        <textarea wire:model="recommendations.{{ $rIndex }}.recommendation_text" rows="2" @if($isViewMode) disabled @endif
                                                  class="w-full border-gray-100 rounded-lg px-3 py-2 bg-white text-xs focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all shadow-sm disabled:opacity-60 resize-none"
                                                  placeholder="Recomendación para este rango de puntaje..."></textarea>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    @endif
                </div>

                {{-- Modal Footer --}}
                <div class="px-8 py-5 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <div>
                        @if($currentStep > 1)
                            <button wire:click="previousStep"
                                    class="px-5 py-2.5 text-xs font-bold text-gray-600 hover:text-gray-900 border border-gray-200 rounded-xl transition-all flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                Anterior
                            </button>
                        @endif
                    </div>
                    <div class="flex items-center gap-3">
                        <button wire:click="closeModal"
                                class="px-5 py-2.5 text-[10px] font-black text-gray-500 hover:text-gray-900 transition-all uppercase tracking-widest">
                            {{ $isViewMode ? 'Cerrar' : 'Cancelar' }}
                        </button>
                        @if(!$isViewMode)
                            @if($currentStep < 3)
                                <button wire:click="nextStep"
                                        class="px-6 py-2.5 text-sm text-white bg-teal-600 hover:bg-teal-700 rounded-xl font-bold transition-all duration-200 shadow-lg shadow-teal-500/20 active:scale-95 flex items-center gap-1.5">
                                    Siguiente
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            @else
                                <button wire:click="save"
                                        class="px-8 py-2.5 text-sm text-white bg-teal-600 hover:bg-teal-700 rounded-xl font-bold transition-all duration-200 shadow-lg shadow-teal-500/20 active:scale-95 flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    {{ $testId ? 'Actualizar Test' : 'Crear Test' }}
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
