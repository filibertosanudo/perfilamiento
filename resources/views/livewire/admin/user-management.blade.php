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

        {{-- Cabecera: contador + buscador --}}
        <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h3 class="text-base font-semibold text-gray-900">
                Usuarios Registrados
                <span class="ml-1.5 text-gray-400 font-normal text-sm">({{ $totalUsers }})</span>
            </h3>
            <div class="relative w-full sm:w-72">
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

        {{-- Tabla --}}
        <div class="overflow-x-auto scrollbar-none">
            <table class="min-w-full">
                <thead>
                    <tr class="border-y border-gray-100 bg-gray-50/60">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Usuario</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Institución</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Orientador(es)</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tests Completados</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
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
                                    $advisors = $user->groups->pluck('creator')->filter()->unique('id');
                                @endphp

                                @if($advisors->isEmpty())
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
                                    <div class="flex flex-col gap-1">
                                        @foreach($advisors as $advisor)
                                            <div class="flex items-center gap-1.5">
                                                <div class="w-5 h-5 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold shrink-0">
                                                    {{ strtoupper(substr($advisor->first_name, 0, 1)) }}
                                                </div>
                                                <span class="text-xs text-gray-700">
                                                    {{ $advisor->first_name }} {{ $advisor->last_name }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @endif
                        </td>

                        {{-- Tests Completados --}}
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{-- TODO: reemplazar con $user->completed_tests_count cuando exitsa la relación --}}
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
                                    wire:confirm="¿Seguro que deseas eliminar a {{ $user->first_name }} {{ $user->last_name }}? Esta acción no se puede deshacer."
                                    class="p-1.5 hover:bg-red-50 rounded-lg transition-colors" title="Eliminar">
                                    <svg class="text-red-500" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 6h18"/>
                                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                        <line x1="10" x2="10" y1="11" y2="17"/>
                                        <line x1="14" x2="14" y1="11" y2="17"/>
                                    </svg>
                                </button>
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
                                    @if($search)
                                        <p class="text-xs mt-1">Intenta con otro término de búsqueda</p>
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
            {{ $users->links() }}
        </div>
    </div>

    {{-- MODAL CREAR / EDITAR --}}
    <div
        x-data="{ show: @entangle('isOpen') }"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        role="dialog"
        aria-modal="true"
    >
        <div class="flex items-end justify-center min-h-screen sm:items-center p-4">

            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" wire:click="closeModal"></div>

            {{-- Panel --}}
            <div class="relative bg-white rounded-xl shadow-2xl w-full sm:max-w-lg overflow-hidden">

                {{-- Header --}}
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900">
                        {{ $userId ? 'Editar Usuario' : 'Crear Usuario' }}
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

                    {{-- Nombre + Apellido Paterno --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                                Nombre <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="first_name" placeholder="Juan"
                                class="block w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors">
                            @error('first_name')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                                Apellido Paterno <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="last_name" placeholder="Pérez"
                                class="block w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors">
                            @error('last_name')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Apellido Materno --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                            Apellido Materno
                        </label>
                        <input type="text" wire:model="second_last_name" placeholder="García"
                            class="block w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors">
                        @error('second_last_name')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Correo --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                            Correo Electrónico <span class="text-red-500">*</span>
                        </label>
                        <input type="email" wire:model="email" placeholder="usuario@institucion.edu"
                            class="block w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors">
                        @error('email')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Teléfono --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                            Teléfono
                        </label>
                        <input type="text" wire:model="phone" placeholder="(555) 000-0000"
                            class="block w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors">
                        @error('phone')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Institución --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                            Institución
                        </label>
                        <select wire:model="institution_id"
                            class="block w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors">
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
                            <select wire:model="role_id"
                                class="block w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors">
                                <option value="">Seleccionar...</option>
                                <option value="1">Admin</option>
                                <option value="2">Orientador</option>
                                <option value="3">Usuario</option>
                            </select>
                            @error('role_id')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                                Estado
                            </label>
                            <select wire:model="active"
                                class="block w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/60 flex justify-end gap-2">
                    <button type="button" wire:click="closeModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none transition-colors">
                        Cancelar
                    </button>
                    <button type="button" wire:click="store"
                        class="px-4 py-2 text-sm font-semibold text-white bg-teal-600 rounded-lg hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 transition-colors">
                        {{ $userId ? 'Guardar Cambios' : 'Crear Usuario' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>