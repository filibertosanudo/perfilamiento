<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Renderless;
use App\Models\User;
use App\Models\Institution;

class UserManagement extends Component
{
    use WithPagination;

    //  Estado de búsqueda 
    public string $search = '';

    //  Estado del modal 
    public bool $isOpen = false;

    //  Campos del formulario 
    public ?int  $userId           = null;
    public string $first_name       = '';
    public string $last_name        = '';
    public string $second_last_name = '';
    public string $email            = '';
    public string $phone            = '';
    public ?int  $institution_id   = null;
    public ?int  $role_id          = null;
    public int   $active           = 1;

    //  Reset paginación al buscar 
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    //  Modal 

    #[Renderless]
    public function openModal(): void
    {
        $this->isOpen = true;
        $this->dispatch('modal-opened');
    }

    public function closeModal(): void
    {
        $this->isOpen = false;
        $this->resetInputFields();
        $this->dispatch('modal-closed');
    }

    //  CRUD 

    public function create(): void
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function edit(int $id): void
    {
        $user = User::findOrFail($id);

        $this->userId           = $user->id;
        $this->first_name       = $user->first_name;
        $this->last_name        = $user->last_name;
        $this->second_last_name = $user->second_last_name ?? '';
        $this->email            = $user->email;
        $this->phone            = $user->phone ?? '';
        $this->institution_id   = $user->institution_id;
        $this->role_id          = $user->role_id;
        $this->active           = $user->active ? 1 : 0;

        $this->openModal();
    }

    public function store(): void
    {
        $this->validate([
            'first_name'  => 'required|string|max:100',
            'last_name'   => 'required|string|max:100',
            'email'       => 'required|email|unique:users,email,' . $this->userId,
            'role_id'     => 'required|integer|in:1,2,3',
            'institution_id' => 'nullable|exists:institutions,id',
            'phone'       => 'nullable|string|max:20',
            'second_last_name' => 'nullable|string|max:100',
        ]);

        User::updateOrCreate(
            ['id' => $this->userId],
            [
                'first_name'       => $this->first_name,
                'last_name'        => $this->last_name,
                'second_last_name' => $this->second_last_name ?: null,
                'email'            => $this->email,
                'phone'            => $this->phone ?: null,
                'institution_id'   => $this->institution_id,
                'role_id'          => $this->role_id,
                'active'           => $this->active,
                'password'         => $this->userId
                    ? User::find($this->userId)->password
                    : bcrypt('password123'),
            ]
        );

        session()->flash('message',
            $this->userId ? 'Usuario actualizado correctamente.' : 'Usuario creado exitosamente.');

        $this->closeModal();
    }

    public function delete(int $id): void
    {
        User::findOrFail($id)->delete();
        session()->flash('message', 'Usuario eliminado correctamente.');
    }

    public function viewUser(int $id): void
    {
        // TODO: implementar cuando exista la vista de perfil
        // return redirect()->route('admin.users.show', $id);
    }

    //  Render 

    public function render()
    {
        $users = User::with(['institution', 'groups.creator'])
            ->where(function ($query) {
                $query->where('first_name',  'like', '%' . $this->search . '%')
                      ->orWhere('last_name',  'like', '%' . $this->search . '%')
                      ->orWhere('email',      'like', '%' . $this->search . '%')
                      ->orWhereHas('institution', fn($q) =>
                          $q->where('name', 'like', '%' . $this->search . '%')
                      );
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.admin.user-management', [
            'users'        => $users,
            'totalUsers'   => User::count(),
            'institutions' => Institution::where('active', true)->orderBy('name')->get(),
        ]);
    }

    //  Privados 

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