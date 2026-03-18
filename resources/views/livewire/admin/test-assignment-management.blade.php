<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                @if(auth()->user()->role_id === 1)
                    Asignación de Tests
                @else
                    Mis Asignaciones de Tests
                @endif
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                @if(auth()->user()->role_id === 1)
                    Administra las asignaciones de tests en la plataforma
                @else
                    Asigna tests a tus usuarios y grupos
                @endif
            </p>
        </div>
        <div class="shrink-0">
            <button
                wire:click="create"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-teal-600 rounded-lg font-semibold text-white text-sm hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-colors shadow-sm"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"/><path d="M12 5v14"/>
                </svg>
                Asignar Test
            </button>
        </div>
    </div>

    {{-- Flash Message --}}
    @if (session()->has('message'))
        <div data-flash
            class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <path d="m9 11 3 3L22 4"/>
            </svg>
            {{ session('message') }}
        </div>
    @endif

    {{-- TABLA DE ASIGNACIONES --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

        {{-- Cabecera --}}
        <div class="px-6 py-4 space-y-4">
            
            {{-- Fila 1: Título + Toggle --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <h3 class="text-base font-semibold text-gray-900">
                        Asignaciones
                        <span class="ml-1.5 text-gray-400 font-normal text-sm">({{ $assignments->total() }})</span>
                    </h3>
                    
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

            {{-- Fila 2: Buscador --}}
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
                    placeholder="Buscar por test, usuario, grupo o área..."
                    class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg bg-gray-50 text-sm placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors"
                >
            </div>
        </div>

        {{-- Tabla --}}
        <div class="overflow-x-auto scrollbar-none">
            <table class="min-w-full">
                <thead>
                    <tr class="border-y border-gray-100 bg-gray-50/60">
                        
                        {{-- Test --}}
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Test
                        </th>

                        {{-- Tipo --}}
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Tipo
                        </th>

                        {{-- Asignado a --}}
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Asignado a
                        </th>

                        {{-- Asignado por --}}
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Asignado por
                        </th>

                        {{-- Progreso --}}
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Progreso
                        </th>

                        {{-- Fecha límite --}}
                        <th wire:click="sortBy('due_date')" 
                            class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors group">
                            <div class="flex items-center gap-1">
                                Fecha límite
                                @if($sortField === 'due_date')
                                    <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }} transition-transform" 
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" 
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m18 15-6-6-6 6"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 opacity-0 group-hover:opacity-40 transition-opacity" 
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" 
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m7 15 5 5 5-5"/><path d="m7 9 5-5 5 5"/>
                                    </svg>
                                @endif
                            </div>
                        </th>

                        {{-- Estado --}}
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Estado
                        </th>

                        {{-- Acciones --}}
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($assignments as $assignment)
                    <tr class="hover:bg-gray-50/70 transition-colors group">

                        {{-- Test --}}
                        <td class="px-6 py-4">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ $assignment->test->name }}
                                </p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $assignment->test->total_questions }} preguntas · 
                                    ~{{ $assignment->test->estimated_time }} min
                                </p>
                            </div>
                        </td>

                        {{-- Tipo --}}
                        <td class="px-6 py-4">
                            @if($assignment->user_id)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                    Individual
                                </span>
                            @elseif($assignment->group_id)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-medium bg-purple-100 text-purple-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                    Grupal
                                </span>
                            @elseif($assignment->area_id)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-medium bg-amber-100 text-amber-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="2" y="7" width="20" height="14" rx="2"/>
                                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                                    </svg>
                                    Por Área
                                </span>
                            @endif
                        </td>

                        {{-- Asignado a --}}
                        <td class="px-6 py-4">
                            @if($assignment->user_id)
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold shrink-0">
                                        {{ strtoupper(substr($assignment->user->first_name, 0, 1)) }}
                                    </div>
                                    <span class="text-sm text-gray-700">
                                        {{ $assignment->user->first_name }} {{ $assignment->user->last_name }}
                                    </span>
                                </div>
                            @elseif($assignment->group_id)
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-purple-600">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $assignment->group->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $assignment->group->users->count() }} miembros</p>
                                    </div>
                                </div>
                            @elseif($assignment->area_id)
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-amber-600">
                                        <rect x="2" y="7" width="20" height="14" rx="2"/>
                                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $assignment->area->name }}</p>
                                        <p class="text-xs text-gray-500">Toda la área</p>
                                    </div>
                                </div>
                            @endif
                        </td>

                        {{-- Asignado por --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold shrink-0">
                                    {{ strtoupper(substr($assignment->assignedBy->first_name, 0, 1)) }}
                                </div>
                                <span class="text-sm text-gray-700">
                                    {{ $assignment->assignedBy->first_name }}
                                </span>
                            </div>
                        </td>

                        {{-- Progreso --}}
                        <td class="px-6 py-4">
                            @php
                                $totalUsers = $assignment->affected_users->count();
                                $completedUsers = $assignment->responses()->where('completed', true)->count();
                                $inProgressUsers = $assignment->responses()->where('completed', false)->where('started_at', '!=', null)->count();
                                $pendingUsers = $totalUsers - $completedUsers - $inProgressUsers;
                            @endphp
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 text-xs">
                                    <span class="font-medium text-gray-700">{{ $completedUsers }}/{{ $totalUsers }}</span>
                                    <span class="text-gray-500">completados</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-teal-600 h-1.5 rounded-full" style="width: {{ $totalUsers > 0 ? ($completedUsers / $totalUsers) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        </td>

                        {{-- Fecha límite --}}
                        <td class="px-6 py-4">
                            @if($assignment->due_date)
                                @php
                                    $urgencyColor = $assignment->urgency_color;
                                    $colorClasses = [
                                        'red' => 'text-red-600',
                                        'amber' => 'text-amber-600',
                                        'gray' => 'text-gray-700',
                                    ];
                                @endphp
                                <div class="text-sm">
                                    <p class="font-medium {{ $colorClasses[$urgencyColor] }}">
                                        {{ $assignment->due_date->format('d/m/Y') }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $assignment->time_remaining }}
                                    </p>
                                </div>
                            @else
                                <span class="text-sm text-gray-400">Sin fecha límite</span>
                            @endif
                        </td>

                        {{-- Estado --}}
                        <td class="px-6 py-4">
                            @if($assignment->active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-500 text-white">
                                    Activa
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-200 text-gray-600">
                                    Cancelada
                                </span>
                            @endif
                        </td>

                        {{-- Acciones --}}
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-0.5 opacity-60 group-hover:opacity-100 transition-opacity">
                                
                                {{-- Ver --}}
                                <button wire:click="viewAssignment({{ $assignment->id }})"
                                    class="p-1.5 hover:bg-gray-100 rounded-lg transition-colors" title="Ver detalles">
                                    <svg class="text-gray-500" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </button>

                                @if($assignment->active)
                                    {{-- Editar --}}
                                    <button wire:click="edit({{ $assignment->id }})"
                                        class="p-1.5 hover:bg-gray-100 rounded-lg transition-colors" title="Editar">
                                        <svg class="text-gray-500" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/>
                                        </svg>
                                    </button>

                                    {{-- Cancelar --}}
                                    <button
                                        wire:click="delete({{ $assignment->id }})"
                                        wire:confirm="¿Cancelar esta asignación? Los usuarios ya no podrán responder el test."
                                        class="p-1.5 hover:bg-red-50 rounded-lg transition-colors" title="Cancelar asignación">
                                        <svg class="text-red-500" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                                        </svg>
                                    </button>
                                @else
                                    {{-- Reactivar --}}
                                    <button
                                        wire:click="activate({{ $assignment->id }})"
                                        wire:confirm="¿Reactivar esta asignación? Los usuarios podrán volver a responder el test."
                                        class="p-1.5 hover:bg-green-50 rounded-lg transition-colors" title="Reactivar asignación">
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
                        <td colspan="8" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-gray-500">No hay asignaciones</p>
                                    @if($search)
                                        <p class="text-xs mt-1">Intenta ajustar los filtros de búsqueda</p>
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
            {{ $assignments->links() }}
        </div>
    </div>

    {{-- MODAL ASIGNAR TEST --}}
    @if($isOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen sm:items-center p-4">
            
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" wire:click="closeModal"></div>

            {{-- Panel --}}
            <div class="relative bg-white rounded-xl shadow-2xl w-full sm:max-w-lg overflow-hidden">
                
                {{-- Header --}}
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900">
                        @if($isViewMode)
                            Ver Asignación
                        @elseif($assignmentId)
                            Editar Asignación
                        @else
                            Asignar Test
                        @endif
                    </h3>
                    <button wire:click="closeModal"
                        class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="px-6 py-5 space-y-4">

                    {{-- Seleccionar Test --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                            Test <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.blur="test_id"
                            {{ $isViewMode ? 'disabled' : '' }}
                            class="block w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors disabled:bg-gray-100 disabled:cursor-not-allowed">
                            <option value="">Seleccionar test...</option>
                            @foreach($tests as $test)
                                <option value="{{ $test->id }}">
                                    {{ $test->name }} ({{ $test->total_questions }} preguntas)
                                </option>
                            @endforeach
                        </select>
                        @error('test_id')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Tipo de asignación --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">
                            Asignar a <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            <label class="relative flex items-center justify-center p-3 border-2 rounded-lg {{ $isViewMode ? 'cursor-not-allowed opacity-60' : 'cursor-pointer' }} transition-all {{ $assignment_type === 'individual' ? 'border-teal-600 bg-teal-50' : 'border-gray-200 hover:border-gray-300' }}">
                                <input type="radio" wire:model.live="assignment_type" value="individual" class="sr-only" {{ $isViewMode ? 'disabled' : '' }}>
                                <div class="text-center">
                                    <svg class="w-6 h-6 mx-auto {{ $assignment_type === 'individual' ? 'text-teal-600' : 'text-gray-400' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                    <span class="text-xs font-medium mt-1 {{ $assignment_type === 'individual' ? 'text-teal-700' : 'text-gray-600' }}">Individual</span>
                                </div>
                            </label>

                            <label class="relative flex items-center justify-center p-3 border-2 rounded-lg {{ $isViewMode ? 'cursor-not-allowed opacity-60' : 'cursor-pointer' }} transition-all {{ $assignment_type === 'group' ? 'border-teal-600 bg-teal-50' : 'border-gray-200 hover:border-gray-300' }}">
                                <input type="radio" wire:model.live="assignment_type" value="group" class="sr-only" {{ $isViewMode ? 'disabled' : '' }}>
                                <div class="text-center">
                                    <svg class="w-6 h-6 mx-auto {{ $assignment_type === 'group' ? 'text-teal-600' : 'text-gray-400' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                    <span class="text-xs font-medium mt-1 {{ $assignment_type === 'group' ? 'text-teal-700' : 'text-gray-600' }}">Grupo</span>
                                </div>
                            </label>

                            <label class="relative flex items-center justify-center p-3 border-2 rounded-lg {{ $isViewMode ? 'cursor-not-allowed opacity-60' : 'cursor-pointer' }} transition-all {{ $assignment_type === 'area' ? 'border-teal-600 bg-teal-50' : 'border-gray-200 hover:border-gray-300' }}">
                                <input type="radio" wire:model.live="assignment_type" value="area" class="sr-only" {{ $isViewMode ? 'disabled' : '' }}>
                                <div class="text-center">
                                    <svg class="w-6 h-6 mx-auto {{ $assignment_type === 'area' ? 'text-teal-600' : 'text-gray-400' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="2" y="7" width="20" height="14" rx="2"/>
                                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                                    </svg>
                                    <span class="text-xs font-medium mt-1 {{ $assignment_type === 'area' ? 'text-teal-700' : 'text-gray-600' }}">Área</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Selector según tipo --}}
                    @if($assignment_type === 'individual')
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                                Usuario <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.blur="user_id"
                                {{ $isViewMode ? 'disabled' : '' }}
                                class="block w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors disabled:bg-gray-100 disabled:cursor-not-allowed">
                                <option value="">Seleccionar usuario...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->first_name }} {{ $user->last_name }} - {{ $user->email }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    @elseif($assignment_type === 'group')
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                                Grupo <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.blur="group_id"
                                {{ $isViewMode ? 'disabled' : '' }}
                                class="block w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors disabled:bg-gray-100 disabled:cursor-not-allowed">
                                <option value="">Seleccionar grupo...</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}">
                                        {{ $group->name }} ({{ $group->users->count() }} miembros)
                                    </option>
                                @endforeach
                            </select>
                            @error('group_id')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    @elseif($assignment_type === 'area')
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                                Área <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="area_id"
                                {{ $isViewMode || auth()->user()->role_id === 2 ? 'disabled' : '' }}
                                class="block w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors disabled:bg-gray-100 disabled:cursor-not-allowed">
                                
                                @if(auth()->user()->role_id !== 2)
                                    <option value="">Seleccionar área...</option>
                                @endif
                                
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}">
                                        {{ $area->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('area_id')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif

                    {{-- Fecha límite --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                            Fecha límite <span class="text-red-500">*</span>
                        </label>
                        <input type="date" wire:model.blur="due_date"
                            {{ $isViewMode ? 'disabled' : '' }}
                            min="{{ now()->addDays(7)->format('Y-m-d') }}"
                            class="block w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors disabled:bg-gray-100 disabled:cursor-not-allowed">
                        @if(!$isViewMode)
                            <p class="text-xs text-gray-500 mt-1">Mínimo 7 días a partir de hoy</p>
                        @endif
                        @error('due_date')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/60 flex justify-end gap-2">
                    @if($isViewMode)
                        <button type="button" wire:click="closeModal"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none transition-colors">
                            Cerrar
                        </button>
                    @else
                        <button type="button" wire:click="closeModal"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none transition-colors">
                            Cancelar
                        </button>
                        <button type="button" wire:click="store"
                            class="px-4 py-2 text-sm font-semibold text-white bg-teal-600 rounded-lg hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 transition-colors">
                            {{ $assignmentId ? 'Guardar Cambios' : 'Asignar Test' }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Auto-hide flash messages --}}
    @script
    <script>
        setTimeout(() => {
            const flash = document.querySelector('[data-flash]');
            if (flash) {
                flash.style.transition = 'opacity 0.5s';
                flash.style.opacity = '0';
                setTimeout(() => flash.remove(), 500);
            }
        }, 5000);
    </script>
    @endscript
</div>