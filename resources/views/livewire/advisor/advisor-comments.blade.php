<div
    x-data="{ showConfirmDelete: @entangle('deletingId') }"
    class="space-y-4"
>
    {{-- Header del Panel --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <h3 class="text-lg font-bold text-gray-900">Comentarios del Orientador</h3>
            @if($followUpCount > 0)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z" clip-rule="evenodd"/>
                    </svg>
                    {{ $followUpCount }} en seguimiento
                </span>
            @endif
        </div>
        <button
            wire:click="$toggle('showForm')"
            class="inline-flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm"
        >
            @if($showForm)
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Cancelar
            @else
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo Comentario
            @endif
        </button>
    </div>

    {{-- Formulario nuevo comentario --}}
    @if($showForm)
        <div class="bg-teal-50 border border-teal-200 rounded-xl p-5 space-y-4">
            <h4 class="text-sm font-semibold text-teal-800">Agregar nota privada</h4>

            {{-- Tipo --}}
            <div class="flex gap-3">
                @foreach(['note' => ['Nota', 'blue'], 'follow_up' => ['Seguimiento', 'amber'], 'alert' => ['Alerta', 'red']] as $val => $meta)
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" wire:model="type" value="{{ $val }}" class="sr-only peer">
                        <div class="text-center px-3 py-2 rounded-lg border-2 text-xs font-semibold transition-colors
                            peer-checked:bg-{{ $meta[1] }}-100 peer-checked:border-{{ $meta[1] }}-400 peer-checked:text-{{ $meta[1] }}-700
                            border-gray-200 text-gray-500 hover:border-gray-300 bg-white">
                            {{ $meta[0] }}
                        </div>
                    </label>
                @endforeach
            </div>

            {{-- Textarea --}}
            <div>
                <textarea
                    wire:model="body"
                    rows="4"
                    placeholder="Escribe tu nota aquí. Esta nota es privada y NO será visible para el usuario."
                    class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm bg-white placeholder-gray-400 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 resize-none transition-colors"
                ></textarea>
                @error('body')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Opciones --}}
            <div class="flex items-center gap-6">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" wire:model="flagFollowUp"
                        class="w-4 h-4 rounded border-gray-300 text-amber-500 focus:ring-amber-400">
                    <span class="text-sm text-gray-700">Marcar para seguimiento</span>
                </label>
                <div class="flex items-center gap-1.5 text-xs text-gray-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Nota privada (solo visible para orientadores)
                </div>
            </div>

            <div class="flex justify-end">
                <button
                    wire:click="save"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-teal-600 hover:bg-teal-700 disabled:opacity-50 text-white text-sm font-medium rounded-lg transition-colors"
                >
                    <span wire:loading.remove wire:target="save">Guardar Comentario</span>
                    <span wire:loading wire:target="save">Guardando...</span>
                </button>
            </div>
        </div>
    @endif

    {{-- Filtros --}}
    <div class="flex flex-wrap items-center gap-2">
        <span class="text-xs text-gray-500 font-medium">Filtrar:</span>
        @foreach(['' => 'Todos', 'note' => 'Notas', 'follow_up' => 'Seguimiento', 'alert' => 'Alertas'] as $val => $label)
            <button wire:click="$set('filterType', '{{ $val }}')"
                class="px-3 py-1 text-xs font-medium rounded-full transition-colors
                    {{ $filterType === $val ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Lista de comentarios --}}
    <div class="space-y-3">
        @forelse($comments as $comment)
            @php
                $colors = ['note' => 'blue', 'follow_up' => 'amber', 'alert' => 'red'];
                $c = $colors[$comment->type] ?? 'gray';
            @endphp

            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow">
                {{-- si es editing este comentario --}}
                @if($editingId === $comment->id)
                    <div class="space-y-3">
                        <div class="flex gap-3">
                            @foreach(['note' => ['Nota', 'blue'], 'follow_up' => ['Seguimiento', 'amber'], 'alert' => ['Alerta', 'red']] as $val => $meta)
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" wire:model="editType" value="{{ $val }}" class="sr-only peer">
                                    <div class="text-center px-3 py-2 rounded-lg border-2 text-xs font-semibold transition-colors
                                        peer-checked:bg-{{ $meta[1] }}-100 peer-checked:border-{{ $meta[1] }}-400 peer-checked:text-{{ $meta[1] }}-700
                                        border-gray-200 text-gray-500 hover:border-gray-300 bg-white">
                                        {{ $meta[0] }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <textarea
                            wire:model="editBody"
                            rows="3"
                            class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 resize-none"
                        ></textarea>
                        @error('editBody')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" wire:model="editFlagFollowUp"
                                class="w-4 h-4 rounded border-gray-300 text-amber-500 focus:ring-amber-400">
                            <span class="text-sm text-gray-700">Marcar para seguimiento</span>
                        </label>
                        <div class="flex gap-2 justify-end">
                            <button wire:click="cancelEdit" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                Cancelar
                            </button>
                            <button wire:click="saveEdit" class="px-4 py-2 text-sm bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-lg transition-colors">
                                Guardar cambios
                            </button>
                        </div>
                    </div>
                @else
                    <div class="flex items-start gap-3">
                        {{-- Avatar orientador --}}
                        <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm font-bold shrink-0">
                            {{ strtoupper(substr($comment->advisor->first_name ?? 'O', 0, 1)) }}
                        </div>

                        <div class="flex-1 min-w-0">
                            {{-- Meta header --}}
                            <div class="flex items-center gap-2 flex-wrap mb-2">
                                <span class="text-sm font-semibold text-gray-900">
                                    {{ $comment->advisor->first_name }} {{ $comment->advisor->last_name }}
                                </span>
                                {{-- Badge tipo --}}
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-{{ $c }}-100 text-{{ $c }}-700">
                                    {{ $comment->typeLabel() }}
                                </span>
                                {{-- Badge seguimiento --}}
                                @if($comment->flag_follow_up)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z" clip-rule="evenodd"/>
                                        </svg>
                                        Seguimiento
                                    </span>
                                @endif
                                {{-- Badge test relacionado --}}
                                @if($comment->testResponse)
                                    <span class="text-xs text-gray-400" title="Test relacionado">
                                        · {{ $comment->testResponse->assignment->test->name ?? 'Test' }}
                                    </span>
                                @endif
                                {{-- Privado --}}
                                <span class="ml-auto flex items-center gap-0.5 text-xs text-gray-400">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    Privado
                                </span>
                            </div>

                            {{-- Cuerpo --}}
                            <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line break-words">{{ $comment->body }}</p>

                            {{-- Footer: tiempo + acciones --}}
                            <div class="flex items-center gap-4 mt-3">
                                <span class="text-xs text-gray-400" title="{{ $comment->created_at->format('d/m/Y H:i') }}">
                                    {{ $comment->created_at->diffForHumans() }}
                                    @if($comment->updated_at->ne($comment->created_at))
                                        <span class="ml-1 italic">(editado)</span>
                                    @endif
                                </span>

                                {{-- Solo el orientador que escribió puede editar/borrar --}}
                                @if($comment->advisor_id === auth()->id())
                                    <div class="flex items-center gap-3 ml-auto">
                                        <button wire:click="toggleFollowUp({{ $comment->id }})"
                                            class="text-xs {{ $comment->flag_follow_up ? 'text-amber-600 hover:text-amber-400' : 'text-gray-400 hover:text-amber-500' }} transition-colors"
                                            title="{{ $comment->flag_follow_up ? 'Quitar seguimiento' : 'Marcar para seguimiento' }}"
                                        >
                                            <svg class="w-4 h-4" fill="{{ $comment->flag_follow_up ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                            </svg>
                                        </button>
                                        <button wire:click="startEdit({{ $comment->id }})"
                                            class="text-xs text-gray-400 hover:text-teal-600 transition-colors"
                                            title="Editar"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button wire:click="confirmDelete({{ $comment->id }})"
                                            class="text-xs text-gray-400 hover:text-red-500 transition-colors"
                                            title="Eliminar"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-10 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <p class="text-sm font-medium">No hay comentarios aún</p>
                <p class="text-xs mt-1">Las notas del orientador son privadas y solo visibles en este panel</p>
            </div>
        @endforelse
    </div>

    {{-- Modal Confirmación de Borrado --}}
    @if($deletingId)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6" x-trap="true">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-base font-bold text-gray-900">Eliminar comentario</h4>
                        <p class="text-sm text-gray-500 mt-0.5">Esta acción no se puede deshacer.</p>
                    </div>
                </div>
                <div class="flex gap-3 justify-end mt-6">
                    <button wire:click="cancelDelete"
                        class="px-4 py-2 text-sm text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors">
                        Cancelar
                    </button>
                    <button wire:click="deleteComment"
                        class="px-4 py-2 text-sm text-white bg-red-600 hover:bg-red-700 rounded-lg font-medium transition-colors">
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
