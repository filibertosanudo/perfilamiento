<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Renderless;
use App\Models\TestAssignment;
use App\Models\Test;
use App\Models\User;
use App\Models\Group;
use App\Models\Institution;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use App\Helpers\NotificationHelper;

class TestAssignmentManagement extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    // Búsqueda y filtros
    public string $search = '';
    public bool $showInactive = false;

    // Ordenamiento
    public string $sortField = 'assigned_at';
    public string $sortDirection = 'desc';

    // Estado del modal
    public bool $isOpen = false;
    public bool $isViewMode = false;

    // Campos del formulario
    public ?int $assignmentId = null;
    public ?int $test_id = null;
    public string $assignment_type = 'individual';
    public ?int $user_id = null;
    public ?int $group_id = null;
    public ?int $institution_id = null;
    public ?string $due_date = null;
    public int $active = 1;

    // Verificar permisos
    public function mount()
    {
        $this->authorize('viewAny', TestAssignment::class);
    }

    // Reset paginación
    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingShowInactive(): void { $this->resetPage(); }

    // Cambio de tipo de asignación
    public function updatedAssignmentType(): void
    {
        $this->user_id = null;
        $this->group_id = null;
        
        $user = auth()->user();

        if ($this->assignment_type === 'institution' && $user->role_id === 2) {
            $this->institution_id = $user->institution_id;
        } else {
            $this->institution_id = null;
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

    // Modal

    #[Renderless]
    public function openModal(): void
    {
        $this->isOpen = true;

        $user = auth()->user();

        if ($this->assignment_type === 'institution' && $user->role_id === 2) {
            $this->institution_id = $user->institution_id;
        }

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
        $this->authorize('create', TestAssignment::class);
        $this->resetInputFields();
        $this->isViewMode = false;
        
        if (auth()->user()->role_id === 2) {
            $this->institution_id = auth()->user()->institution_id;
        }
        
        $this->due_date = now()->addWeek()->format('Y-m-d');
        $this->openModal();
    }

    public function edit(int $id): void
    {
        $assignment = TestAssignment::findOrFail($id);
        $this->authorize('delete', $assignment);

        $this->assignmentId = $assignment->id;
        $this->test_id = $assignment->test_id;
        $this->due_date = $assignment->due_date?->format('Y-m-d');
        $this->active = $assignment->active ? 1 : 0;
        $this->isViewMode = false;

        // Determinar tipo y cargar datos
        if ($assignment->user_id) {
            $this->assignment_type = 'individual';
            $this->user_id = $assignment->user_id;
        } elseif ($assignment->group_id) {
            $this->assignment_type = 'group';
            $this->group_id = $assignment->group_id;
        } elseif ($assignment->institution_id) {
            $this->assignment_type = 'institution';
            $this->institution_id = $assignment->institution_id;
        }

        $this->openModal();
    }

    public function viewAssignment(int $id): void
    {
        $assignment = TestAssignment::findOrFail($id);
        $this->authorize('view', $assignment);
        $this->edit($id);
        $this->isViewMode = true;
    }

    public function store(): void
    {
        if ($this->assignmentId) {
            $assignment = TestAssignment::findOrFail($this->assignmentId);
            $this->authorize('delete', $assignment);
        } else {
            $this->authorize('create', TestAssignment::class);
        }

        $rules = [
            'test_id' => 'required|exists:tests,id',
            'assignment_type' => 'required|in:individual,group,institution',
            'due_date' => 'required|date|after:' . now()->addDays(6)->format('Y-m-d'),
        ];

        if ($this->assignment_type === 'individual') {
            $rules['user_id'] = 'required|exists:users,id';
        } elseif ($this->assignment_type === 'group') {
            $rules['group_id'] = 'required|exists:groups,id';
        } elseif ($this->assignment_type === 'institution') {
            $rules['institution_id'] = 'required|exists:institutions,id';
        }

        $this->validate($rules, [
            'test_id.required' => 'Debes seleccionar un test.',
            'test_id.exists' => 'El test seleccionado no es válido.',
            'user_id.required' => 'Debes seleccionar un usuario.',
            'group_id.required' => 'Debes seleccionar un grupo.',
            'institution_id.required' => 'Debes seleccionar una institución.',
            'due_date.required' => 'Debes establecer una fecha límite.',
            'due_date.after' => 'La fecha límite debe ser al menos 7 días a partir de hoy.',
        ]);

        $isNew = !$this->assignmentId;

        $assignment = TestAssignment::updateOrCreate(
            ['id' => $this->assignmentId],
            [
                'test_id' => $this->test_id,
                'assigned_by' => $isNew ? auth()->id() : TestAssignment::find($this->assignmentId)->assigned_by,
                'user_id' => $this->assignment_type === 'individual' ? $this->user_id : null,
                'group_id' => $this->assignment_type === 'group' ? $this->group_id : null,
                'institution_id' => $this->assignment_type === 'institution' ? $this->institution_id : null,
                'assigned_at' => $isNew ? now() : TestAssignment::find($this->assignmentId)->assigned_at,
                'due_date' => $this->due_date,
                'active' => true,
            ]
        );

        if ($isNew) {
            $test = Test::find($this->test_id);
            $dueDate = \Carbon\Carbon::parse($this->due_date);

            // Obtener usuarios afectados
            $affectedUsers = collect();

            if ($this->assignment_type === 'individual' && $this->user_id) {
                $affectedUsers->push(User::find($this->user_id));
            } elseif ($this->assignment_type === 'group' && $this->group_id) {
                $group = Group::with('users')->find($this->group_id);
                $affectedUsers = $group->users;
            } elseif ($this->assignment_type === 'institution' && $this->institution_id) {
                $institution = Institution::with('users')->find($this->institution_id);
                $affectedUsers = $institution->users->where('role_id', 3);
            }

            // Enviar notificación a cada usuario
            foreach ($affectedUsers as $user) {
                NotificationHelper::testAssigned(
                    $user,
                    $test->name,
                    $test->id,
                    $dueDate
                );
            }

            \Log::info('Test asignado con notificaciones', [
                'assignment_id' => $assignment->id,
                'test_id' => $assignment->test_id,
                'type' => $this->assignment_type,
                'assigned_by' => auth()->id(),
                'affected_users' => $affectedUsers->count(),
                'notifications_sent' => $affectedUsers->count(),
            ]);

            $message = 'Test asignado correctamente a ' . $affectedUsers->count() . ' usuario(s). Notificaciones enviadas.';
        } else {
            \Log::info('Asignación de test actualizada', [
                'assignment_id' => $assignment->id,
                'updated_by' => auth()->id(),
            ]);
            $message = 'Asignación actualizada correctamente.';
        }

        session()->flash('message', $message);
        $this->closeModal();
    }

    public function delete(int $id): void
    {
        $assignment = TestAssignment::findOrFail($id);
        $this->authorize('delete', $assignment);

        $assignment->update(['active' => false]);

        \Log::warning('Asignación de test cancelada', [
            'assignment_id' => $assignment->id,
            'cancelled_by' => auth()->id(),
        ]);

        session()->flash('message', 'Asignación cancelada correctamente.');
    }

    public function activate(int $id): void
    {
        $assignment = TestAssignment::findOrFail($id);
        $this->authorize('delete', $assignment);

        $assignment->update(['active' => true]);

        \Log::info('Asignación de test reactivada', [
            'assignment_id' => $assignment->id,
            'reactivated_by' => auth()->id(),
        ]);

        session()->flash('message', 'Asignación reactivada correctamente.');
    }

    // Render

    public function render()
    {
        $currentUser = auth()->user();
        $search = $this->search;

        $query = TestAssignment::with(['test', 'assignedBy', 'user', 'group', 'institution', 'responses'])
            ->when(!$this->showInactive, fn ($q) => $q->where('active', true))
            ->when($currentUser->role_id === 2, function ($q) use ($currentUser) {
                // Orientador ve: sus asignaciones + asignaciones de admin a sus grupos
                $q->where(function ($q) use ($currentUser) {
                    $q->where('assigned_by', $currentUser->id)
                      ->orWhereHas('group', function ($q) use ($currentUser) {
                          $q->where('creator_id', $currentUser->id);
                      });
                });
            })
            ->when($search, fn ($q) => $this->applySearch($q, $search));

        $assignments = $query
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.admin.test-assignment-management', [
            'assignments' => $assignments,
            'totalAssignments' => $this->getTotalAssignments($currentUser),
            'tests' => $this->getAvailableTests(),
            'users' => $this->getAvailableUsers($currentUser),
            'groups' => $this->getAvailableGroups($currentUser),
            'institutions' => $this->getAvailableInstitutions($currentUser),
        ]);
    }

    // Métodos privados

    private function applySearch(Builder $query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->whereHas('test', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->orWhereHas('user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->orWhereHas('group', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->orWhereHas('institution', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        });
    }

    private function getTotalAssignments(User $currentUser): int
    {
        if ($currentUser->role_id === 1) {
            return TestAssignment::count();
        }

        // Orientador: sus asignaciones + asignaciones a sus grupos
        return TestAssignment::where(function ($q) use ($currentUser) {
            $q->where('assigned_by', $currentUser->id)
              ->orWhereHas('group', function ($q) use ($currentUser) {
                  $q->where('creator_id', $currentUser->id);
              });
        })->count();
    }

    private function getAvailableTests(): Collection
    {
        if (!$this->isOpen) {
            return collect();
        }

        return Test::where('active', true)->orderBy('name')->get();
    }

    private function getAvailableUsers(User $currentUser): Collection
    {
        if (!$this->isOpen || $this->assignment_type !== 'individual') {
            return collect();
        }

        $query = User::where('role_id', 3)->where('active', true);

        if ($currentUser->role_id === 2) {
            $query->whereHas('groups', function ($q) use ($currentUser) {
                $q->where('creator_id', $currentUser->id);
            });
        }

        return $query->orderBy('first_name')->get();
    }

    private function getAvailableGroups(User $currentUser): Collection
    {
        if (!$this->isOpen || $this->assignment_type !== 'group') {
            return collect();
        }

        $query = Group::where('active', true);

        if ($currentUser->role_id === 2) {
            $query->where('creator_id', $currentUser->id);
        }

        return $query->orderBy('name')->get();
    }

    private function getAvailableInstitutions(User $currentUser): Collection
    {
        if (!$this->isOpen || $this->assignment_type !== 'institution') {
            return collect();
        }

        if ($currentUser->role_id === 2) {
            return Institution::where('id', $currentUser->institution_id)
                ->where('active', true)
                ->get();
        }

        return Institution::where('active', true)->orderBy('name')->get();
    }

    private function resetInputFields(): void
    {
        $this->assignmentId = null;
        $this->test_id = null;
        $this->assignment_type = 'individual';
        $this->user_id = null;
        $this->group_id = null;
        $this->institution_id = null;
        $this->due_date = null;
        $this->active = 1;
        $this->resetValidation();
    }
}