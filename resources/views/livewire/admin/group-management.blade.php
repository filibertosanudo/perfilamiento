<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                @if(auth()->user()->role_id === 1)
                    Gestión de Grupos
                @else
                    Mis Grupos
                @endif
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                @if(auth()->user()->role_id === 1)
                    Administra todos los grupos de la plataforma
                @else
                    Gestiona tus grupos y sus miembros
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
                Crear Grupo
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

    {{-- TABLA DE GRUPOS --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

        {{-- Cabecera: contador + toggle + buscador --}}
        <div class="px-6 py-4 space-y-4">
            
            {{-- Fila 1: Título + Toggle Inactivos --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <h3 class="text-base font-semibold text-gray-900">
                        Grupos Registrados
                        <span class="ml-1.5 text-gray-400 font-normal text-sm">({{ $groups->total() }})</span>
                    </h3>
                    
                    {{-- Toggle para mostrar inactivos --}}
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <div class="relative">
                            <input type="checkbox" 
                                wire:model.live="showInactive"
                                class="sr-only peer">
                            <div class="w-10 h-6 bg-gray-200 rounded-full peer peer-checked:bg-teal-600 transition-colors"></div>
                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-4"></div>
                        </div>
                        <span class="text-xs font-medium text-gray-600 group-hover:text-gray-900">
                            Mostrar inactivos
                        </span>
                    </label>
                </div>
            </div>

            {{-- Fila 2: Buscador --}}
            <div class="flex flex-col sm:flex-row gap-3">
                {{-- Buscador --}}
                <div class="relative flex-1">
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
                        placeholder="Buscar por nombre, descripción o institución..."
                        class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg bg-gray-50 text-sm placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors"
                    >
                </div>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="overflow-x-auto scrollbar-none">
            <table class="min-w-full">
                <thead>
                    <tr class="border-y border-gray-100 bg-gray-50/60">
                        
                        {{-- Grupo - Ordenable --}}
                        <th wire:click="sortBy('name')" 
                            class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors group">
                            <div class="flex items-center gap-1">
                                Grupo
                                @if($sortField === 'name')
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

                        {{-- Institución --}}
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Institución
                        </th>

                        {{-- Orientador --}}
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Orientador
                        </th>

                        {{-- Miembros --}}
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Miembros
                        </th>

                        {{-- Fecha Creación - Ordenable --}}
                        <th wire:click="sortBy('created_at')" 
                            class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors group">
                            <div class="flex items-center gap-1">
                                Creado
                                @if($sortField === 'created_at')
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

                        {{-- Estado - Ordenable --}}
                        <th wire:click="sortBy('active')" 
                            class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors group">
                            <div class="flex items-center gap-1">
                                Estado
                                @if($sortField === 'active')
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

                        {{-- Acciones --}}
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($groups as $group)
                    <tr class="hover:bg-gray-50/70 transition-colors group">

                        {{-- Grupo --}}
                        <td class="px-6 py-4">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900 leading-tight">
                                    {{ $group->name }}
                                </p>
                                @if($group->description)
                                    <p class="text-xs text-gray-500 mt-1 line-clamp-1">
                                        {{ $group->description }}
                                    </p>
                                @endif
                            </div>
                        </td>

                        {{-- Institución --}}
                        <td class="px-6 py-4">
                            @if($group->institution)
                                <div class="flex items-center gap-1.5 text-sm text-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="text-gray-400 shrink-0">
                                        <rect x="2" y="7" width="20" height="14" rx="2"/>
                                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                                    </svg>
                                    {{ $group->institution->name }}
                                </div>
                            @endif
                        </td>

                        {{-- Orientador --}}
                        <td class="px-6 py-4">
                            @if($group->creator)
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold shrink-0">
                                        {{ strtoupper(substr($group->creator->first_name, 0, 1)) }}
                                    </div>
                                    <span class="text-sm text-gray-700">
                                        {{ $group->creator->first_name }} {{ $group->creator->last_name }}
                                    </span>
                                </div>
                            @endif
                        </td>

                        {{-- Miembros --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="flex -space-x-2">
                                    @foreach($group->users->take(3) as $user)
                                        <div class="w-7 h-7 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center text-xs font-bold ring-2 ring-white">
                                            {{ strtoupper(substr($user->first_name, 0, 1)) }}
                                        </div>
                                    @endforeach
                                </div>
                                <span class="text-sm text-gray-700 font-medium">
                                    {{ $group->users->count() }}
                                    @if($group->users->count() > 3)
                                        <span class="text-gray-400 font-normal">miembros</span>
                                    @else
                                        <span class="text-gray-400 font-normal">{{ $group->users->count() === 1 ? 'miembro' : 'miembros' }}</span>
                                    @endif
                                </span>
                            </div>
                        </td>

                        {{-- Fecha Creación --}}
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $group->created_at ? $group->created_at->format('d/m/Y') : '—' }}
                        </td>

                        {{-- Estado --}}
                        <td class="px-6 py-4">
                            @if($group->active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-500 text-white">
                                    Activo
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-200 text-gray-600">
                                    Inactivo
                                </span>
                            @endif
                        </td>

                        {{-- Acciones --}}
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-0.5 opacity-60 group-hover:opacity-100 transition-opacity">
                                
                                {{-- Ver --}}
                                <button wire:click="viewGroup({{ $group->id }})"
                                    class="p-1.5 hover:bg-gray-100 rounded-lg transition-colors" title="Ver detalles">
                                    <svg class="text-gray-500" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </button>

                                @if($group->active)
                                    {{-- Gestionar Miembros --}}
                                    <button wire:click="openMembersModal({{ $group->id }})"
                                        class="p-1.5 hover:bg-indigo-50 rounded-lg transition-colors" title="Gestionar miembros">
                                        <svg class="text-indigo-600" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                            <circle cx="9" cy="7" r="4"/>
                                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                        </svg>
                                    </button>

                                    {{-- Editar --}}
                                    <button wire:click="edit({{ $group->id }})"
                                        class="p-1.5 hover:bg-gray-100 rounded-lg transition-colors" title="Editar">
                                        <svg class="text-gray-500" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/>
                                        </svg>
                                    </button>

                                    {{-- Desactivar --}}
                                    <button
                                        wire:click="delete({{ $group->id }})"
                                        wire:confirm="¿Seguro que deseas desactivar el grupo '{{ $group->name }}'?"
                                        class="p-1.5 hover:bg-red-50 rounded-lg transition-colors" title="Desactivar">
                                        <svg class="text-red-500" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                                        </svg>
                                    </button>
                                @else
                                    {{-- Reactivar --}}
                                    <button
                                        wire:click="activate({{ $group->id }})"
                                        wire:confirm="¿Reactivar el grupo '{{ $group->name }}'?"
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
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-gray-500">No se encontraron grupos</p>
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
            {{ $groups->links() }}
        </div>
    </div>

    {{-- MODAL CREAR/EDITAR GRUPO --}}
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
                            Ver Grupo
                        @elseif($groupId)
                            Editar Grupo
                        @else
                            Crear Grupo
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

                    {{-- Nombre --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                            Nombre del Grupo <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model.blur="name" placeholder="Ej: Ingeniería 8A"
                            {{ $isViewMode ? 'disabled' : '' }}
                            class="block w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors disabled:bg-gray-100 disabled:cursor-not-allowed">
                        @error('name')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Descripción --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                            Descripción
                        </label>
                        <textarea wire:model.blur="description" rows="3" placeholder="Descripción del grupo..."
                            {{ $isViewMode ? 'disabled' : '' }}
                            class="block w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors disabled:bg-gray-100 disabled:cursor-not-allowed resize-none"></textarea>
                        @error('description')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Institución --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                            Institución <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.live="institution_id"
                            {{ $isViewMode || auth()->user()->role_id === 2 ? 'disabled' : '' }}
                            class="block w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors disabled:bg-gray-100 disabled:cursor-not-allowed">
                            <option value="">Seleccionar institución...</option>
                            @foreach($institutions as $institution)
                                <option value="{{ $institution->id }}">{{ $institution->name }}</option>
                            @endforeach
                        </select>
                        @error('institution_id')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Orientador (solo visible para Admin) --}}
                    @if(auth()->user()->role_id === 1)
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                                Orientador <span class="text-red-500">*</span>
                            </label>
                            
                            @if($institution_id && $advisors->isNotEmpty())
                                <select wire:model.blur="creator_id"
                                    {{ $isViewMode ? 'disabled' : '' }}
                                    class="block w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors disabled:bg-gray-100 disabled:cursor-not-allowed">
                                    <option value="">Seleccionar orientador...</option>
                                    @foreach($advisors as $advisor)
                                        <option value="{{ $advisor->id }}">
                                            {{ $advisor->first_name }} {{ $advisor->last_name }} - {{ $advisor->email }}
                                        </option>
                                    @endforeach
                                </select>
                            @elseif($institution_id && $advisors->isEmpty())
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-sm text-yellow-800">
                                    <div class="flex gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" 
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="shrink-0 text-yellow-600">
                                            <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                                            <path d="M12 9v4"/>
                                            <path d="M12 17h.01"/>
                                        </svg>
                                        <span>No hay orientadores disponibles en esta institución.</span>
                                    </div>
                                </div>
                            @else
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 text-sm text-gray-500">
                                    Selecciona una institución primero
                                </div>
                            @endif
                            
                            @error('creator_id')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    @elseif($isViewMode && $groupId)
                        {{-- Mostrar orientador en modo vista --}}
                        @php
                            $viewedGroup = \App\Models\Group::find($groupId);
                        @endphp
                        @if($viewedGroup && $viewedGroup->creator)
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                                    Orientador
                                </label>
                                <div class="flex items-center gap-2 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm font-bold shrink-0">
                                        {{ strtoupper(substr($viewedGroup->creator->first_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $viewedGroup->creator->first_name }} {{ $viewedGroup->creator->last_name }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $viewedGroup->creator->email }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

                    {{-- Estado --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                            Estado
                        </label>
                        <select wire:model.blur="active"
                            {{ $isViewMode ? 'disabled' : '' }}
                            class="block w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors disabled:bg-gray-100 disabled:cursor-not-allowed">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
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
                            {{ $groupId ? 'Guardar Cambios' : 'Crear Grupo' }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL GESTIONAR MIEMBROS --}}
    @if($membersModalOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen sm:items-center p-4">
            
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" wire:click="closeMembersModal"></div>

            {{-- Panel --}}
            <div class="relative bg-white rounded-xl shadow-2xl w-full sm:max-w-2xl overflow-hidden">
                
                {{-- Header --}}
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900">
                        Gestionar Miembros
                    </h3>
                    <button wire:click="closeMembersModal"
                        class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="px-6 py-5">
                    
                    {{-- Buscador --}}
                    <div class="mb-4">
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                                </svg>
                            </div>
                            <input
                                wire:model.live.debounce.300ms="memberSearch"
                                type="text"
                                placeholder="Buscar usuarios..."
                                class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg bg-gray-50 text-sm placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors"
                            >
                        </div>
                    </div>

                    {{-- Lista de usuarios --}}
                    <div class="max-h-96 overflow-y-auto space-y-2">
                        @forelse ($availableUsers as $user)
                            <label class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                <input
                                    type="checkbox"
                                    wire:click="toggleUser({{ $user->id }})"
                                    @checked(in_array($user->id, $selectedUsers))
                                    class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500"
                                >

                                <div class="w-8 h-8 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center text-sm font-bold shrink-0">
                                    {{ strtoupper(substr($user->first_name, 0, 1)) }}
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $user->first_name }} {{ $user->last_name }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $user->email }}
                                    </p>
                                </div>
                            </label>
                        @empty
                            <div class="text-center py-8 text-gray-400">
                                <p class="text-sm">No hay usuarios disponibles</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Contador de seleccionados --}}
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-sm text-gray-600">
                            <span class="font-semibold text-teal-600">{{ count($selectedUsers) }}</span> 
                            {{ count($selectedUsers) === 1 ? 'usuario seleccionado' : 'usuarios seleccionados' }}
                        </p>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/60 flex justify-end gap-2">
                    <button type="button" wire:click="closeMembersModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none transition-colors">
                        Cancelar
                    </button>
                    <button type="button" wire:click="saveMembers"
                        class="px-4 py-2 text-sm font-semibold text-white bg-teal-600 rounded-lg hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 transition-colors">
                        Guardar Cambios
                    </button>
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