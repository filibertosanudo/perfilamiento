<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Renderless;
use App\Models\Group;
use App\Models\User;
use App\Models\Institution;
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
    public ?int $institution_id = null;
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

        // Pre-seleccionar institución del orientador
        if (auth()->user()->role_id === 2) {
            $this->institution_id = auth()->user()->institution_id;
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
        $this->institution_id = $group->institution_id;
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
            'institution_id' => 'required|exists:institutions,id',
            'active' => 'required|boolean',
        ], [
            'name.required' => 'El nombre del grupo es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'institution_id.required' => 'Debes seleccionar una institución.',
            'institution_id.exists' => 'La institución seleccionada no es válida.',
        ]);

        $isNew = !$this->groupId;

        $group = Group::updateOrCreate(
            ['id' => $this->groupId],
            [
                'name' => trim($this->name),
                'description' => $this->description ? trim($this->description) : null,
                'institution_id' => $this->institution_id,
                'creator_id' => $isNew ? auth()->id() : Group::find($this->groupId)->creator_id,
                'active' => $this->active,
                'created_at' => $isNew ? now() : Group::find($this->groupId)->created_at,
            ]
        );

        if ($isNew) {
            \Log::info('Grupo creado', [
                'group_id' => $group->id,
                'group_name' => $group->name,
                'creator_id' => auth()->id(),
                'creator_email' => auth()->user()->email,
                'institution_id' => $group->institution_id,
            ]);
            $message = 'Grupo creado exitosamente.';
        } else {
            \Log::info('Grupo actualizado', [
                'group_id' => $group->id,
                'group_name' => $group->name,
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

        // Log de cambios
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

        $query = Group::with(['institution', 'creator', 'users'])
            ->when(!$this->showInactive, fn ($q) => $q->where('active', true))
            ->when($currentUser->role_id === 2, fn ($q) => $q->where('creator_id', $currentUser->id))
            ->when($search, fn ($q) => $this->applySearch($q, $search));

        $groups = $query
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        // Instituciones disponibles
        $institutions = $this->getAvailableInstitutions($currentUser);

        // Usuarios disponibles para el modal de miembros
        $availableUsers = $this->getAvailableUsers($currentUser);

        return view('livewire.admin.group-management', [
            'groups' => $groups,
            'totalGroups' => $this->getTotalGroups($currentUser),
            'institutions' => $institutions,
            'availableUsers' => $availableUsers,
        ]);
    }

    // Métodos privados

    private function applySearch(Builder $query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhereHas('institution', function ($q) use ($search) {
                  $q->where('name', 'like', "%{$search}%");
              });
        });
    }

    private function getAvailableInstitutions(User $currentUser): Collection
    {
        return $currentUser->role_id === 1
            ? Institution::where('active', true)->orderBy('name')->get()
            : Institution::where('id', $currentUser->institution_id)
                ->where('active', true)
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

        // Usuarios de la misma institución, role_id = 3
        $query = User::where('institution_id', $group->institution_id)
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
        $this->institution_id = null;
        $this->active = 1;
        $this->resetValidation();
    }
}