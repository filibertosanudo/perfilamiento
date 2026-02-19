<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Renderless;
use App\Models\User;
use App\Models\Institution;
use Illuminate\Support\Str;
use App\Notifications\UserInvitation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Helpers\SecurityLog;

class UserManagement extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    // Búsqueda y filtros 
    public string $search = '';
    public bool $showInactive = false;
    public ?int $filterRole = null;  // 1=Admin, 2=Orientador, 3=Usuario, null=Todos

    // Ordenamiento 
    public string $sortField = 'id';      // campo por el que ordenar
    public string $sortDirection = 'desc'; // dirección: asc|desc

    //  Estado del modal 
    public bool $isOpen = false;
    public bool $isViewMode = false;

    // Campos del formulario
    public ?int  $userId           = null;
    public string $first_name       = '';
    public string $last_name        = '';
    public string $second_last_name = '';
    public string $email            = '';
    public string $phone            = '';
    public ?int  $institution_id   = null;
    public ?int  $role_id          = null;
    public int   $active           = 1;

    // Reset paginación al cambiar filtros
    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingShowInactive(): void { $this->resetPage(); }
    public function updatingFilterRole(): void { $this->resetPage(); }

    // Ordenamiento
    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            // Si ya está ordenado por este campo, invertir dirección
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            // Cambiar a nuevo campo, default DESC
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

    // Verificar permisos al montar el componente 
    public function mount()
    {
        // Solo admin puede gestionar usuarios
        $this->authorize('viewAny', User::class);
    }

    public function create(): void
    {
        $this->authorize('create', User::class);
        $this->resetInputFields();
        $this->isViewMode = false;
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
        $this->institution_id   = $user->institution_id;
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
            'first_name'  => 'required|string|min:2|max:100|regex:/^[\pL\s]+$/u',
            'last_name'   => 'required|string|min:2|max:100|regex:/^[\pL\s]+$/u',
            'email'       => [
                'required',
                'email:rfc,dns',
                'max:150',
                'unique:users,email,' . $this->userId,
            ],
            'role_id'     => 'required|integer|in:1,2,3',
            'second_last_name' => 'nullable|string|max:100|regex:/^[\pL\s]+$/u',
            'phone'            => 'nullable|string|regex:/^[\d\s\-\(\)\+]+$/|min:10|max:20',
            'institution_id'   => 'nullable|exists:institutions,id',
            'active'      => 'required|boolean',
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
            'institution_id.exists'   => 'La institución seleccionada no existe.',
        ]);

        $isNew = !$this->userId;
        $invitationToken = \Illuminate\Support\Str::random(64);

        $user = User::updateOrCreate(
            ['id' => $this->userId],
            [
                'first_name'       => trim($this->first_name),
                'last_name'        => trim($this->last_name),
                'second_last_name' => $this->second_last_name ? trim($this->second_last_name) : null,
                'email'            => strtolower(trim($this->email)),
                'phone'            => $this->phone ? preg_replace('/\s+/', '', $this->phone) : null,
                'institution_id'   => $this->institution_id,
                'role_id'          => $this->role_id,
                'active'           => $this->active,
                
                // Solo generar token si es usuario nuevo
                'invitation_token'   => $isNew ? $invitationToken : null,
                'invitation_sent_at' => $isNew ? now() : null,
                
                // Dejar password null si es nuevo (lo pondrá el usuario)
                'password' => $isNew
                    ? null
                    : User::find($this->userId)->password,
            ]
        );

        // Enviar email de invitación si es usuario nuevo
        if ($isNew) {
            try {
                $user->notify(new \App\Notifications\UserInvitation());
                
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
                        'first_name' => $this->first_name,
                        'last_name' => $this->last_name,
                        'email' => $this->email,
                        'role_id' => $this->role_id,
                        'institution_id' => $this->institution_id,
                        'active' => $this->active,
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
            'invitation_token' => Str::random(64),
            'invitation_sent_at' => now(),
        ]);

        try {
            $user->notify(new UserInvitation());
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

    // TODO: Validación en tiempo real con mensajes personalizados

    // public function updated($propertyName)
    // {
    //     $this->validateOnly($propertyName, [
    //         'first_name'  => 'required|string|min:2|max:100|regex:/^[\pL\s]+$/u',
    //         'last_name'   => 'required|string|min:2|max:100|regex:/^[\pL\s]+$/u',
    //         'email'       => 'required|email:rfc,dns|max:150|unique:users,email,' . $this->userId,
    //         'phone'       => 'nullable|string|regex:/^[\d\s\-\(\)\+]+$/|min:10|max:20',
    //     ]);
    // }

    // Render

    public function render()
    {
        $search = $this->search;
        $institutionIds = $search
            ? \DB::table('institutions')->where('name', 'like', '%' . $search . '%')->pluck('id')
            : collect();

        $users = User::with(['institution', 'groups.creator'])
            ->when(!$this->showInactive, fn($q) => $q->where('active', true))
            ->when($this->filterRole, fn($q) => $q->where('role_id', $this->filterRole))
            ->where(function ($query) use ($search, $institutionIds) {
                $query->where('first_name', 'like', '%' . $search . '%')
                      ->orWhere('last_name',  'like', '%' . $search . '%')
                      ->orWhere('email',      'like', '%' . $search . '%');

                if ($institutionIds->isNotEmpty()) {
                    $query->orWhereIn('institution_id', $institutionIds);
                }
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.admin.user-management', [
            'users'        => $users,
            'totalUsers'   => User::count(),
            'institutions' => Institution::where('active', true)->orderBy('name')->get(),
        ]);
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
        $this->institution_id   = null;
        $this->role_id          = null;
        $this->active           = 1;
        $this->resetValidation();
    }
}