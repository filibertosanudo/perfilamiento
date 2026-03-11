<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Renderless;
use App\Models\User;
use App\Models\Area;
use Illuminate\Support\Str;
use App\Notifications\UserInvitation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Helpers\SecurityLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class UserManagement extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    // Búsqueda y filtros
    public string $search = '';
    public bool $showInactive = false;
    public ?int $filterRole = null;  // 1=Admin, 2=Orientador, 3=Usuario, null=Todos

    // Ordenamiento
    public string $sortField = 'id';
    public string $sortDirection = 'desc';

    // Estado del modal
    public bool $isOpen = false;
    public bool $isViewMode = false;

    // Campos del formulario
    public ?int  $userId           = null;
    public string $first_name       = '';
    public string $last_name        = '';
    public string $second_last_name = '';
    public string $email            = '';
    public string $phone            = '';
    public ?int  $area_id          = null;
    public ?int  $role_id          = null;
    public int   $active           = 1;

    // Verificar permisos al montar
    public function mount()
    {
        // Seguridad
        $this->authorize('viewAny', User::class);

        // UX / lógica de filtros
        if (auth()->user()->role_id !== 1) {
            $this->filterRole = 3;
        }
    }

    // Reset paginación al cambiar filtros
    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingShowInactive(): void { $this->resetPage(); }
    public function updatingFilterRole(): void { $this->resetPage(); }

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

    // Modal

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

    // CRUD

    public function create(): void
    {
        $this->authorize('create', User::class);
        $this->resetInputFields();
        $this->isViewMode = false;
        
        // Si es orientador, pre-seleccionar su área
        if (auth()->user()->role_id === 2) {
            $this->area_id = auth()->user()->area_id;
        }
        
        $this->openModal();
    }

    public function edit(int $id): void
    {
        $user = User::findOrFail($id);
        $this->authorize('update', $user);

        $this->userId           = $user->id;
        $this->first_name       = $user->first_name;
        $this->last_name        = $user->last_name;
        $this->second_last_name = $user->second_last_name ?? '';
        $this->email            = $user->email;
        $this->phone            = $user->phone ?? '';
        $this->area_id          = $user->area_id;
        $this->role_id          = $user->role_id;
        $this->active           = $user->active ? 1 : 0;
        $this->isViewMode       = false;

        $this->openModal();
    }

    public function viewUser(int $id): void
    {
        $user = User::findOrFail($id);
        $this->authorize('view', $user);
        $this->edit($id);
        $this->isViewMode = true;
    }

    public function store(): void
    {
        // Autorizar según acción
        if ($this->userId) {
            $user = User::findOrFail($this->userId);
            $this->authorize('update', $user);
        } else {
            $this->authorize('create', User::class);
        }

        $this->validate([
            'first_name'       => 'required|string|min:2|max:100|regex:/^[\pL\s]+$/u',
            'last_name'        => 'required|string|min:2|max:100|regex:/^[\pL\s]+$/u',
            'email'            => [
                'required',
                'email:rfc,dns',
                'max:150',
                'unique:users,email,' . $this->userId,
            ],
            'role_id'          => 'required|integer|in:1,2,3',
            'second_last_name' => 'nullable|string|max:100|regex:/^[\pL\s]+$/u',
            'phone'            => 'nullable|string|regex:/^[\d\s\-\(\)\+]+$/|min:10|max:20',
            'area_id'          => 'nullable|exists:areas,id',
            'active'           => 'required|boolean',
        ], [
            'first_name.required'     => 'El nombre es obligatorio.',
            'first_name.min'          => 'El nombre debe tener al menos 2 caracteres.',
            'first_name.regex'        => 'El nombre solo puede contener letras y espacios.',
            'last_name.required'      => 'El apellido paterno es obligatorio.',
            'last_name.min'           => 'El apellido debe tener al menos 2 caracteres.',
            'last_name.regex'         => 'El apellido solo puede contener letras y espacios.',
            'second_last_name.regex'  => 'El apellido materno solo puede contener letras y espacios.',
            'email.required'          => 'El correo electrónico es obligatorio.',
            'email.email'             => 'Ingresa un correo electrónico válido.',
            'email.unique'            => 'Este correo ya está registrado.',
            'phone.regex'             => 'Formato de teléfono inválido.',
            'phone.min'               => 'El teléfono debe tener al menos 10 dígitos.',
            'role_id.required'        => 'Debes seleccionar un tipo de usuario.',
            'role_id.in'              => 'El tipo de usuario seleccionado no es válido.',
            'area_id.exists'          => 'El área seleccionada no existe.',
        ]);

        $isNew = !$this->userId;
        $invitationToken = Str::random(64);

        $user = User::updateOrCreate(
            ['id' => $this->userId],
            [
                'first_name'         => trim($this->first_name),
                'last_name'          => trim($this->last_name),
                'second_last_name'   => $this->second_last_name ? trim($this->second_last_name) : null,
                'email'              => strtolower(trim($this->email)),
                'phone'              => $this->phone ? preg_replace('/\s+/', '', $this->phone) : null,
                'area_id'            => $this->area_id,
                'role_id'            => $this->role_id,
                'active'             => $this->active,
                'invitation_token'   => $isNew ? $invitationToken : null,
                'invitation_sent_at' => $isNew ? now() : null,
                'password'           => $isNew ? null : User::find($this->userId)->password,
            ]
        );

        if ($isNew) {
            try {
                $user->notify(new UserInvitation());
                SecurityLog::invitationSent(auth()->user(), $user);
                $message = 'Usuario creado. Se ha enviado un correo de invitación a ' . $user->email;
            } catch (\Exception $e) {
                \Log::error('Error al enviar invitación: ' . $e->getMessage());
                $message = 'Usuario creado, pero hubo un error al enviar el correo de invitación.';
            }
        } else {
            SecurityLog::permissionChange(
                auth()->user(), 
                $user, 
                'Actualización de datos de usuario',
                [
                    'campos_actualizados' => [
                        'first_name'     => $this->first_name,
                        'last_name'      => $this->last_name,
                        'email'          => $this->email,
                        'role_id'        => $this->role_id,
                        'area_id'        => $this->area_id,
                        'active'         => $this->active,
                    ]
                ]
            );
            $message = 'Usuario actualizado correctamente.';
        }

        session()->flash('message', $message);
        $this->closeModal();
    }

    public function resendInvitation(int $userId): void
    {
        $user = User::findOrFail($userId);

        if ($user->active || $user->hasAcceptedInvitation()) {
            session()->flash('message', 'Error: No se puede reenviar la invitación a este usuario.');
            return;
        }

        $user->update([
            'invitation_token'   => Str::random(64),
            'invitation_sent_at' => now(),
        ]);

        try {
            $user->notify(new UserInvitation());
            SecurityLog::invitationSent(auth()->user(), $user);
            session()->flash('message', 'Se ha reenviado la invitación a ' . $user->email);
        } catch (\Exception $e) {
            \Log::error('Error al reenviar invitación: ' . $e->getMessage());
            session()->flash('message', 'Hubo un error al reenviar el correo de invitación.');
        }
    }

    public function delete(int $id): void
    {
        $user = User::findOrFail($id);
        $this->authorize('delete', $user);
        
        $user->update(['active' => false]);
        SecurityLog::userDeactivated(auth()->user(), $user);
        
        session()->flash('message', 'Usuario desactivado correctamente.');
    }

    public function activate(int $id): void
    {
        $user = User::findOrFail($id);
        $this->authorize('delete', $user);
        
        $user->update(['active' => true]);
        SecurityLog::userReactivated(auth()->user(), $user);
        
        session()->flash('message', 'Usuario reactivado correctamente.');
    }

    // Render con filtrado adaptativo según rol

    public function render()
    {
        $currentUser = auth()->user();
        $search = $this->search;

        $areaIds = $this->getAreaIdsBySearch($search);

        $query = User::with(['area', 'groups.creator'])
            ->when(!$this->showInactive, fn ($q) => $q->where('active', true))
            ->when($currentUser->role_id === 2, fn ($q) => $this->applyOrientadorRestrictions($q, $currentUser))
            ->when(
                $currentUser->role_id === 1 && $this->filterRole,
                fn ($q) => $q->where('role_id', $this->filterRole)
            )
            ->where(fn ($q) => $this->applySearch($q, $search, $areaIds));

        $users = $query
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.admin.user-management', [
            'users'        => $users,
            'totalUsers'   => $this->getTotalUsers($currentUser),
            'areas'        => $this->getAvailableAreas($currentUser),
        ]);
    }

    /**
     * Obtiene IDs de áreas que coinciden con el término de búsqueda
     */
    private function getAreaIdsBySearch(?string $search): Collection
    {
        if (!$search) {
            return collect();
        }

        return \DB::table('areas')
            ->where('name', 'like', "%{$search}%")
            ->pluck('id');
    }

    /**
     * Aplica restricciones de seguridad para orientadores
     * Solo pueden ver usuarios role_id=3 de sus grupos
     */
    private function applyOrientadorRestrictions(Builder $query, User $currentUser): void
    {
        $query->where('role_id', 3)
            ->whereHas('groups', function ($q) use ($currentUser) {
                $q->where('creator_id', $currentUser->id);
            });
    }

    /**
     * Aplica filtros de búsqueda por nombre, email y área
     */
    private function applySearch(Builder $query, ?string $search, Collection $areaIds): void
    {
        $query->where(function ($q) use ($search, $areaIds) {
            if ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name',  'like', "%{$search}%")
                ->orWhere('email',      'like', "%{$search}%");
            }

            if ($areaIds->isNotEmpty()) {
                $q->orWhereIn('area_id', $areaIds);
            }
        });
    }

    /**
     * Obtiene áreas disponibles según el rol del usuario
     */
    private function getAvailableAreas(User $currentUser): Collection
    {
        return $currentUser->role_id === 1
            ? Area::where('active', true)->orderBy('name')->get()
            : Area::where('id', $currentUser->area_id)
                ->where('active', true)
                ->get();
    }

    /**
     * Calcula el total de usuarios según el rol
     */
    private function getTotalUsers(User $currentUser): int
    {
        return $currentUser->role_id === 1
            ? User::count()
            : User::whereHas('groups', fn ($q) => $q->where('creator_id', $currentUser->id))->count();
    }

    // Privados

    private function resetInputFields(): void
    {
        $this->userId           = null;
        $this->first_name       = '';
        $this->last_name        = '';
        $this->second_last_name = '';
        $this->email            = '';
        $this->phone            = '';
        $this->area_id          = null;
        $this->role_id          = null;
        $this->active           = 1;
        $this->resetValidation();
    }
}