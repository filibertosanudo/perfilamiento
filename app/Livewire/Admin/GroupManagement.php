<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Renderless;
use App\Models\Group;
use App\Models\User;
use App\Models\Area;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Helpers\SecurityLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class GroupManagement extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    // Búsqueda y filtros
    public string $search = '';
    public bool $showInactive = false;

    // Ordenamiento
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    // Estado del modal principal
    public bool $isOpen = false;
    public bool $isViewMode = false;

    // Campos del formulario de grupo
    public ?int $groupId = null;
    public string $name = '';
    public string $description = '';
    public ?int $area_id = null;
    public ?int $creator_id = null;
    public int $active = 1;

    // Modal de gestión de miembros
    public bool $membersModalOpen = false;
    public ?int $managingGroupId = null;
    public string $memberSearch = '';
    public array $selectedUsers = [];

    // Verificar permisos al montar
    public function mount()
    {
        $this->authorize('viewAny', Group::class);
    }

    // Reset paginación
    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingShowInactive(): void { $this->resetPage(); }

    // Actualizar lista de orientadores cuando cambia el área
    public function updatedAreaId(): void
    {
        // Si es admin y cambió el área, resetear el orientador seleccionado
        if (auth()->user()->role_id === 1) {
            $this->creator_id = null;
        }
    }

    // Ordenamiento
    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'desc';
        }
    }

    // Modal principal

    #[Renderless]
    public function openModal(): void
    {
        $this->isOpen = true;
        $this->dispatch('modal-opened');
    }

    public function closeModal(): void
    {
        $this->isOpen = false;
        $this->isViewMode = false;
        $this->resetInputFields();
        $this->dispatch('modal-closed');
    }

    // CRUD de Grupos

    public function create(): void
    {
        $this->authorize('create', Group::class);
        $this->resetInputFields();
        $this->isViewMode = false;

        // Pre-seleccionar área y orientador según el rol
        if (auth()->user()->role_id === 2) {
            $this->area_id = auth()->user()->area_id;
            $this->creator_id = auth()->id();
        }

        $this->openModal();
    }

    public function edit(int $id): void
    {
        $group = Group::findOrFail($id);
        $this->authorize('update', $group);

        $this->groupId = $group->id;
        $this->name = $group->name;
        $this->description = $group->description ?? '';
        $this->area_id = $group->area_id;
        $this->creator_id = $group->creator_id;
        $this->active = $group->active ? 1 : 0;
        $this->isViewMode = false;

        $this->openModal();
    }

    public function viewGroup(int $id): void
    {
        $group = Group::findOrFail($id);
        $this->authorize('view', $group);
        $this->edit($id);
        $this->isViewMode = true;
    }

    public function store(): void
    {
        if ($this->groupId) {
            $group = Group::findOrFail($this->groupId);
            $this->authorize('update', $group);
        } else {
            $this->authorize('create', Group::class);
        }

        $this->validate([
            'name' => 'required|string|min:3|max:150',
            'description' => 'nullable|string|max:500',
            'area_id' => 'required|exists:areas,id',
            'creator_id' => 'required|exists:users,id',
            'active' => 'required|boolean',
        ], [
            'name.required' => 'El nombre del grupo es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'area_id.required' => 'Debes seleccionar un área.',
            'area_id.exists' => 'El área seleccionada no es válida.',
            'creator_id.required' => 'Debes seleccionar un orientador.',
            'creator_id.exists' => 'El orientador seleccionado no es válido.',
        ]);

        $isNew = !$this->groupId;

        $group = Group::updateOrCreate(
            ['id' => $this->groupId],
            [
                'name' => trim($this->name),
                'description' => $this->description ? trim($this->description) : null,
                'area_id' => $this->area_id,
                'creator_id' => $this->creator_id,
                'active' => $this->active,
                'created_at' => $isNew ? now() : Group::find($this->groupId)->created_at,
            ]
        );

        if ($isNew) {
            \Log::info('Grupo creado', [
                'group_id' => $group->id,
                'group_name' => $group->name,
                'creator_id' => $group->creator_id,
                'created_by_admin' => auth()->id(),
                'created_by_email' => auth()->user()->email,
                'area_id' => $group->area_id,
            ]);
            $message = 'Grupo creado exitosamente.';
        } else {
            \Log::info('Grupo actualizado', [
                'group_id' => $group->id,
                'group_name' => $group->name,
                'creator_id' => $group->creator_id,
                'updated_by' => auth()->id(),
                'updated_by_email' => auth()->user()->email,
            ]);
            $message = 'Grupo actualizado correctamente.';
        }

        session()->flash('message', $message);
        $this->closeModal();
    }

    public function delete(int $id): void
    {
        $group = Group::findOrFail($id);
        $this->authorize('delete', $group);

        $group->update(['active' => false]);

        \Log::warning('Grupo desactivado', [
            'group_id' => $group->id,
            'group_name' => $group->name,
            'deactivated_by' => auth()->id(),
            'deactivated_by_email' => auth()->user()->email,
        ]);

        session()->flash('message', 'Grupo desactivado correctamente.');
    }

    public function activate(int $id): void
    {
        $group = Group::findOrFail($id);
        $this->authorize('delete', $group);

        $group->update(['active' => true]);

        \Log::info('Grupo reactivado', [
            'group_id' => $group->id,
            'group_name' => $group->name,
            'reactivated_by' => auth()->id(),
            'reactivated_by_email' => auth()->user()->email,
        ]);

        session()->flash('message', 'Grupo reactivado correctamente.');
    }

    // Gestión de Miembros

    public function openMembersModal(int $groupId): void
    {
        $group = Group::findOrFail($groupId);
        $this->authorize('manageMembers', $group);

        $this->managingGroupId = $groupId;
        $this->selectedUsers = $group->users->pluck('id')->toArray();
        $this->memberSearch = '';
        $this->membersModalOpen = true;
    }

    public function closeMembersModal(): void
    {
        $this->membersModalOpen = false;
        $this->managingGroupId = null;
        $this->selectedUsers = [];
        $this->memberSearch = '';
    }

    public function toggleUser(int $userId): void
    {
        if (in_array($userId, $this->selectedUsers)) {
            $this->selectedUsers = array_values(array_diff($this->selectedUsers, [$userId]));
        } else {
            $this->selectedUsers[] = $userId;
        }
    }

    public function saveMembers(): void
    {
        $group = Group::findOrFail($this->managingGroupId);
        $this->authorize('manageMembers', $group);

        // Obtener miembros actuales
        $currentMembers = $group->users->pluck('id')->toArray();

        // Calcular cambios
        $added = array_diff($this->selectedUsers, $currentMembers);
        $removed = array_diff($currentMembers, $this->selectedUsers);

        // Sincronizar con fecha de ingreso
        $syncData = [];
        foreach ($this->selectedUsers as $userId) {
            $syncData[$userId] = ['joined_at' => now()];
        }
        $group->users()->sync($syncData);

        if (!empty($added) || !empty($removed)) {
            \Log::info('Miembros del grupo actualizados', [
                'group_id' => $group->id,
                'group_name' => $group->name,
                'added' => $added,
                'removed' => $removed,
                'updated_by' => auth()->id(),
                'updated_by_email' => auth()->user()->email,
            ]);
        }

        session()->flash('message', 'Miembros actualizados correctamente.');
        $this->closeMembersModal();
    }

    // Render

    public function render()
    {
        $currentUser = auth()->user();
        $search = $this->search;

        $query = Group::with(['area', 'creator', 'users'])
            ->when(!$this->showInactive, fn ($q) => $q->where('active', true))
            ->when($currentUser->role_id === 2, fn ($q) => $q->where('creator_id', $currentUser->id))
            ->when($search, fn ($q) => $this->applySearch($q, $search));

        $groups = $query
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        $areas = $this->getAvailableAreas($currentUser);
        $availableUsers = $this->getAvailableUsers($currentUser);
        $advisors = $this->getAvailableAdvisors($currentUser);

        return view('livewire.admin.group-management', [
            'groups' => $groups,
            'totalGroups' => $this->getTotalGroups($currentUser),
            'areas' => $areas,
            'availableUsers' => $availableUsers,
            'advisors' => $advisors,
        ]);
    }

    // Métodos privados

    private function applySearch(Builder $query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhereHas('area', function ($q) use ($search) {
                  $q->where('name', 'like', "%{$search}%");
              });
        });
    }

    private function getAvailableAreas(User $currentUser): Collection
    {
        return $currentUser->role_id === 1
            ? Area::where('active', true)->orderBy('name')->get()
            : Area::where('id', $currentUser->area_id)
                ->where('active', true)
                ->get();
    }

    /**
     * Obtiene orientadores disponibles según el contexto
     */
    private function getAvailableAdvisors(User $currentUser): Collection
    {
        // Si no está en el modal, no cargar orientadores
        if (!$this->isOpen) {
            return collect();
        }

        // Si es orientador, no necesita selector
        if ($currentUser->role_id === 2) {
            return collect();
        }

        // Si es admin sin área seleccionada, no mostrar orientadores
        if (!$this->area_id) {
            return collect();
        }

        // Admin: Obtener orientadores del área seleccionada
        return User::where('role_id', 2)
            ->where('area_id', $this->area_id)
            ->where('active', true)
            ->orderBy('first_name')
            ->get();
    }

    private function getTotalGroups(User $currentUser): int
    {
        return $currentUser->role_id === 1
            ? Group::count()
            : Group::where('creator_id', $currentUser->id)->count();
    }

    private function getAvailableUsers(User $currentUser): Collection
    {
        if (!$this->membersModalOpen || !$this->managingGroupId) {
            return collect();
        }

        $group = Group::find($this->managingGroupId);
        if (!$group) {
            return collect();
        }

        // Usuarios del misma área, role_id = 3
        $query = User::where('area_id', $group->area_id)
            ->where('role_id', 3)
            ->where('active', true);

        // Aplicar búsqueda si existe
        if ($this->memberSearch) {
            $query->where(function ($q) {
                $search = $this->memberSearch;
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('first_name')->get();
    }

    private function resetInputFields(): void
    {
        $this->groupId = null;
        $this->name = '';
        $this->description = '';
        $this->area_id = null;
        $this->creator_id = null;
        $this->active = 1;
        $this->resetValidation();
    }
}