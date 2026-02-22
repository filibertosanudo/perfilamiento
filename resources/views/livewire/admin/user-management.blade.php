<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Gestión de Usuarios</h1>
            <p class="mt-1 text-sm text-gray-500">Administra los usuarios registrados en la plataforma</p>
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
                Agregar Usuario
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

    {{-- TABLA DE USUARIOS --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

        {{-- Cabecera: contador + filtros + buscador --}}
        <div class="px-6 py-4 space-y-4">
            
            {{-- Fila 1: Título + Toggle Inactivos --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <h3 class="text-base font-semibold text-gray-900">
                        Usuarios Registrados
                        <span class="ml-1.5 text-gray-400 font-normal text-sm">({{ $users->total() }})</span>
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

            {{-- Fila 2: Filtros + Buscador --}}
            <div class="flex flex-col sm:flex-row gap-3">
                
                {{-- Filtro por tipo de usuario --}}
                <select
                    wire:model.live="filterRole"
                    @if(auth()->user()->role_id !== 1) disabled @endif
                    class="w-full sm:w-48 border border-gray-200 rounded-lg px-3 py-2 text-sm
                        bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500
                        transition-colors disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed">

                    @if(auth()->user()->role_id === 1)
                        {{-- Admin --}}
                        <option value="">Todos los tipos</option>
                        <option value="1">Solo Admins</option>
                        <option value="2">Solo Orientadores</option>
                        <option value="3">Solo Usuarios</option>
                    @else
                        {{-- Orientador --}}
                        <option value="3">Solo Usuarios</option>
                    @endif

                </select>

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
                        placeholder="Buscar por nombre, email o institución..."
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
                        
                        {{-- Usuario - Ordenable --}}
                        <th wire:click="sortBy('first_name')" 
                            class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors group">
                            <div class="flex items-center gap-1">
                                Usuario
                                @if($sortField === 'first_name')
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

                        {{-- Institución - No ordenable --}}
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Institución
                        </th>

                        {{-- Tipo - Ordenable --}}
                        <th wire:click="sortBy('role_id')" 
                            class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors group">
                            <div class="flex items-center gap-1">
                                Tipo
                                @if($sortField === 'role_id')
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

                        {{-- Orientador - No ordenable --}}
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Orientador(es)
                        </th>

                        {{-- Tests - No ordenable aún (TODO) --}}
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Tests Completados
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
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50/70 transition-colors group">

                        {{-- Usuario: avatar + nombre + email + teléfono --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center text-sm font-bold shrink-0 select-none">
                                    {{ strtoupper(substr($user->first_name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 leading-tight">
                                        {{ $user->first_name }} {{ $user->last_name }}
                                        @if($user->second_last_name)
                                            {{ $user->second_last_name }}
                                        @endif
                                    </p>
                                    <div class="flex flex-wrap items-center gap-x-3 mt-1">
                                        <span class="flex items-center gap-1 text-xs text-gray-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect width="20" height="16" x="2" y="4" rx="2"/>
                                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                                            </svg>
                                            {{ $user->email }}
                                        </span>
                                        @if($user->phone)
                                        <span class="flex items-center gap-1 text-xs text-gray-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13.5a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.61 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.6a16 16 0 0 0 6.29 6.29l.96-.96a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                                            </svg>
                                            {{ $user->phone }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Institución --}}
                        <td class="px-6 py-4">
                            @if($user->institution)
                                <div class="flex items-center gap-1.5 text-sm text-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="text-gray-400 shrink-0">
                                        <rect x="2" y="7" width="20" height="14" rx="2"/>
                                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                                    </svg>
                                    {{ $user->institution->name }}
                                </div>
                            @else
                                <span class="text-sm text-gray-400 italic">Sin asignar</span>
                            @endif
                        </td>

                        {{-- Tipo / Rol --}}
                        <td class="px-6 py-4">
                            @php
                                $roleMap = [
                                    1 => ['label' => 'Admin',      'class' => 'bg-purple-100 text-purple-700 ring-purple-200'],
                                    2 => ['label' => 'Orientador', 'class' => 'bg-indigo-100 text-indigo-700 ring-indigo-200'],
                                    3 => ['label' => 'Usuario',    'class' => 'bg-teal-100   text-teal-700   ring-teal-200'],
                                ];
                                $role = $roleMap[$user->role_id] ?? ['label' => 'Sin rol', 'class' => 'bg-gray-100 text-gray-600 ring-gray-200'];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ring-1 {{ $role['class'] }}">
                                {{ $role['label'] }}
                            </span>
                        </td>

                        {{-- Orientador(es) --}}
                        <td class="px-6 py-4">
                            @php
                                $noAplica = in_array($user->role_id, [1, 2]);
                            @endphp

                            @if($noAplica)
                                <span class="text-xs text-gray-400 italic">No aplica</span>
                            @else
                                @php
                                    // Agrupar grupos por orientador para evitar repetición
                                    $groupsByAdvisor = $user->groups->groupBy(function($group) {
                                        return $group->creator_id;
                                    })->filter(function($groups) {
                                        return $groups->first()->creator !== null;
                                    });
                                    
                                    $totalAdvisors = $groupsByAdvisor->count();
                                    $displayAdvisors = $groupsByAdvisor->take(2);
                                @endphp

                                @if($groupsByAdvisor->isEmpty())
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-red-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"/>
                                            <line x1="12" x2="12" y1="8" y2="12"/>
                                            <line x1="12" x2="12.01" y1="16" y2="16"/>
                                        </svg>
                                        Sin asignar
                                    </span>
                                @else
                                    <div class="flex flex-col gap-1.5">
                                        @foreach($displayAdvisors as $advisorGroups)
                                            @php
                                                $advisor = $advisorGroups->first()->creator;
                                                $groupCount = $advisorGroups->count();
                                            @endphp
                                            <div class="flex items-center gap-1.5">
                                                <div class="w-5 h-5 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold shrink-0">
                                                    {{ strtoupper(substr($advisor->first_name, 0, 1)) }}
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-xs text-gray-700 font-medium">
                                                        {{ $advisor->first_name }} {{ $advisor->last_name }}
                                                    </span>
                                                    @if($groupCount > 1)
                                                        <span class="text-xs text-gray-400">
                                                            {{ $groupCount }} grupos
                                                        </span>
                                                    @else
                                                        <span class="text-xs text-gray-400">
                                                            {{ $advisorGroups->first()->name }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                        
                                        @if($totalAdvisors > 2)
                                            <button 
                                                wire:click="viewUser({{ $user->id }})"
                                                class="text-xs text-teal-600 hover:text-teal-700 font-medium ml-6 text-left hover:underline"
                                            >
                                                Ver +{{ $totalAdvisors - 2 }} orientador(es) más
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            @endif
                        </td>

                        {{-- Tests Completados --}}
                        <td class="px-6 py-4 text-sm text-gray-700">
                            @isset($user->completed_tests_count)
                                {{ $user->completed_tests_count }}
                            @else
                                <span class="text-gray-400">—</span>
                            @endisset
                        </td>

                        {{-- Estado --}}
                        <td class="px-6 py-4">
                            @if($user->active)
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
                                <button wire:click="viewUser({{ $user->id }})"
                                    class="p-1.5 hover:bg-gray-100 rounded-lg transition-colors" title="Ver perfil">
                                    <svg class="text-gray-500" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </button>
                                
                                @if($user->active)
                                    <button wire:click="edit({{ $user->id }})"
                                        class="p-1.5 hover:bg-gray-100 rounded-lg transition-colors" title="Editar">
                                        <svg class="text-gray-500" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/>
                                        </svg>
                                    </button>
                                    <button
                                        wire:click="delete({{ $user->id }})"
                                        wire:confirm="¿Seguro que deseas desactivar a {{ $user->first_name }} {{ $user->last_name }}?"
                                        class="p-1.5 hover:bg-red-50 rounded-lg transition-colors" title="Desactivar">
                                        <svg class="text-red-500" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                                        </svg>
                                    </button>
                                @else
                                    <button
                                        wire:click="activate({{ $user->id }})"
                                        wire:confirm="¿Reactivar a {{ $user->first_name }} {{ $user->last_name }}?"
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

                                    {{-- Botón para reenviar invitación si ha expirado y no ha sido aceptada --}}
                                    @if(!$user->hasAcceptedInvitation() && $user->invitationExpired())
                                        <button
                                            wire:click="resendInvitation({{ $user->id }})"
                                            wire:confirm="Se enviará un nuevo correo de invitación a {{ $user->email }}. ¿Continuar?"
                                            class="p-1.5 hover:bg-blue-50 rounded-lg transition-colors" title="Reenviar invitación">
                                            <svg class="text-blue-500" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M22 2L15 22l-4-9-9-4 20-5z"/>
                                                <path d="M22 2L11 13"/>
                                            </svg>
                                        </button>
                                    @endif
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
                                    <p class="text-sm font-semibold text-gray-500">No se encontraron usuarios</p>
                                    @if($search || $filterRole)
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
            {{ $users->links('vendor.pagination.tailwind') }}
        </div>

    </div>

    {{-- MODAL --}}
    <div
        x-data="{ show: @entangle('isOpen') }"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        role="dialog"
        aria-modal="true"
    >
        <div class="flex items-end justify-center min-h-screen sm:items-center p-4">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" wire:click="closeModal"></div>
            <div class="relative bg-white rounded-xl shadow-2xl w-full sm:max-w-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900">
                        {{ $userId ? ($isViewMode ? 'Ver Usuario' : 'Editar Usuario') : 'Crear Usuario' }}
                    </h3>
                    <button wire:click="closeModal"
                        class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Body del modal --}}
                <div class="px-6 py-5 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                                Nombre <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model.blur="first_name" placeholder="Juan"
                                {{ $isViewMode ? 'disabled' : '' }}
                                class="block w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors disabled:bg-gray-100 disabled:cursor-not-allowed">
                            @error('first_name')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                                Apellido Paterno <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model.blur="last_name" placeholder="Pérez"
                                {{ $isViewMode ? 'disabled' : '' }}
                                class="block w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors disabled:bg-gray-100 disabled:cursor-not-allowed">
                            @error('last_name')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                            Apellido Materno
                        </label>
                        <input type="text" wire:model.blur="second_last_name" placeholder="García"
                            {{ $isViewMode ? 'disabled' : '' }}
                            class="block w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors disabled:bg-gray-100 disabled:cursor-not-allowed">
                        @error('second_last_name')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                            Correo Electrónico <span class="text-red-500">*</span>
                        </label>
                        <input type="email" wire:model.blur="email" placeholder="usuario@institucion.edu"
                            {{ $isViewMode ? 'disabled' : '' }}
                            class="block w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors disabled:bg-gray-100 disabled:cursor-not-allowed">
                        @error('email')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                            Teléfono
                        </label>
                        <input type="text" wire:model.blur="phone" placeholder="(555) 000-0000"
                            {{ $isViewMode ? 'disabled' : '' }}
                            class="block w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors disabled:bg-gray-100 disabled:cursor-not-allowed">
                        @error('phone')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                            Institución
                        </label>
                        <select wire:model.blur="institution_id"
                            {{ $isViewMode ? 'disabled' : '' }}
                            class="block w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors disabled:bg-gray-100 disabled:cursor-not-allowed">
                            <option value="">Sin institución</option>
                            @foreach($institutions as $institution)
                                <option value="{{ $institution->id }}">{{ $institution->name }}</option>
                            @endforeach
                        </select>
                        @error('institution_id')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Tipo de usuario + Estado --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                                Tipo de Usuario <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.blur="role_id"
                                {{ $isViewMode ? 'disabled' : '' }}
                                class="block w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors disabled:bg-gray-100 disabled:cursor-not-allowed">
                                <option value="">Seleccionar...</option>
                                
                                @if(auth()->user()->role_id === 1)
                                    {{-- Admin puede crear cualquier rol --}}
                                    <option value="1">Admin</option>
                                    <option value="2">Orientador</option>
                                    <option value="3">Usuario</option>
                                @else
                                    {{-- Orientador solo puede crear usuarios regulares --}}
                                    <option value="3">Usuario</option>
                                @endif
                            </select>
                            @error('role_id')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
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

                    {{-- Orientadores y Grupos --}}
                    @if($isViewMode && $userId)
                        @php
                            $viewedUser = \App\Models\User::with(['groups.creator'])->find($userId);
                            $userGroups = $viewedUser->groups ?? collect();
                            $showOrientadores = $viewedUser && $viewedUser->role_id === 3 && $userGroups->isNotEmpty();
                        @endphp

                        @if($showOrientadores)
                            <div class="pt-4 border-t border-gray-200">
                                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">
                                    Orientadores y Grupos Asignados
                                </label>
                                
                                <div class="space-y-3">
                                    @foreach($userGroups as $group)
                                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                            {{-- Grupo --}}
                                            <div class="flex items-start gap-2 mb-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" 
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="text-teal-600 shrink-0 mt-0.5">
                                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                                    <circle cx="9" cy="7" r="4"/>
                                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                                </svg>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-semibold text-gray-900">
                                                        {{ $group->name }}
                                                    </p>
                                                    @if($group->description)
                                                        <p class="text-xs text-gray-500 mt-0.5">
                                                            {{ $group->description }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Orientador --}}
                                            @if($group->creator)
                                                <div class="flex items-center gap-2 ml-6 pl-3 border-l-2 border-indigo-200">
                                                    <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold shrink-0">
                                                        {{ strtoupper(substr($group->creator->first_name, 0, 1)) }}
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-xs font-medium text-gray-700">
                                                            {{ $group->creator->first_name }} {{ $group->creator->last_name }}
                                                        </p>
                                                        <p class="text-xs text-gray-500">
                                                            Orientador
                                                        </p>
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- Fecha de ingreso al grupo --}}
                                            @php
                                                $joinedAt = $group->pivot->joined_at ?? null;
                                            @endphp
                                            @if($joinedAt)
                                                <div class="mt-2 text-xs text-gray-400 ml-6">
                                                    Ingresó: {{ \Carbon\Carbon::parse($joinedAt)->format('d/m/Y') }}
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @elseif($viewedUser && $viewedUser->role_id === 3)
                            {{-- Usuario sin grupos asignados --}}
                            <div class="pt-4 border-t border-gray-200">
                                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">
                                    Orientadores y Grupos Asignados
                                </label>
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                    <div class="flex gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" 
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="text-yellow-600 shrink-0 mt-0.5">
                                            <circle cx="12" cy="12" r="10"/>
                                            <line x1="12" x2="12" y1="8" y2="12"/>
                                            <line x1="12" x2="12.01" y1="16" y2="16"/>
                                        </svg>
                                        <p class="text-xs text-yellow-800">
                                            Este usuario no está asignado a ningún grupo aún.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>

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
                            {{ $userId ? 'Guardar Cambios' : 'Crear Usuario' }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>