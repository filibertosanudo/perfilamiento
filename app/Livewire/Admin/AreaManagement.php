<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Area;
use App\Models\User;
use App\Models\TestResponse;
use Illuminate\Support\Collection;

class AreaManagement extends Component
{
    use WithPagination;

    // Búsqueda y filtros
    public string $search = '';
    public string $filterType = '';
    public bool $showInactive = false;

    // Ordenamiento
    public string $sortField = 'name';
    public string $sortDirection = 'asc';

    // Modal CRUD
    public bool $isOpen = false;
    public bool $isViewMode = false;

    // Campos del formulario
    public ?int $areaId = null;
    public string $name = '';
    public string $type = '';
    public string $city = '';
    public string $address = '';
    public string $phone = '';
    public int $active = 1;

    // Vista detallada (panel lateral)
    public ?int $detailId = null;

    // Tipos disponibles (ejemplo adaptado a contexto de áreas)
    public array $types = [
        'academica'        => 'Académica',
        'administrativa'   => 'Administrativa',
        'investigacion'    => 'Investigación',
        'tecnica'          => 'Técnica',
        'otro'             => 'Otro',
    ];

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterType(): void { $this->resetPage(); }
    public function updatingShowInactive(): void { $this->resetPage(); }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    // ── CRUD ──────────────────────────────────────────────────────────────────

    public function create(): void
    {
        $this->resetInputFields();
        $this->isViewMode = false;
        $this->isOpen = true;
    }

    public function edit(int $id): void
    {
        $area = Area::findOrFail($id);
        $this->areaId        = $area->id;
        $this->name          = $area->name;
        $this->type          = $area->type ?? '';
        $this->city          = $area->city ?? '';
        $this->address       = $area->address ?? '';
        $this->phone         = $area->phone ?? '';
        $this->active        = $area->active ? 1 : 0;
        $this->isViewMode    = false;
        $this->isOpen        = true;
    }

    public function store(): void
    {
        $this->validate([
            'name'    => 'required|string|min:2|max:200|unique:areas,name,' . $this->areaId,
            'type'    => 'nullable|string|max:60',
            'city'    => 'nullable|string|max:100',
            'address' => 'nullable|string|max:300',
            'phone'   => 'nullable|string|regex:/^[\d\s\-\(\)\+]+$/|min:7|max:30',
            'active'  => 'required|boolean',
        ], [
            'name.required' => 'El nombre del área es obligatorio.',
            'name.min'      => 'El nombre debe tener al menos 2 caracteres.',
            'name.unique'   => 'Ya existe un área con ese nombre.',
            'phone.regex'   => 'Formato de teléfono inválido.',
        ]);

        Area::updateOrCreate(
            ['id' => $this->areaId],
            [
                'name'    => trim($this->name),
                'type'    => $this->type ?: null,
                'city'    => $this->city ? trim($this->city) : null,
                'address' => $this->address ? trim($this->address) : null,
                'phone'   => $this->phone ? trim($this->phone) : null,
                'active'  => $this->active,
            ]
        );

        $message = $this->areaId
            ? 'Área actualizada correctamente.'
            : 'Área creada correctamente.';

        session()->flash('message', $message);
        $this->closeModal();
    }

    public function toggleActive(int $id): void
    {
        $area = Area::findOrFail($id);
        $area->update(['active' => !$area->active]);

        $status = $area->active ? 'activada' : 'desactivada';
        session()->flash('message', "Área {$status} correctamente.");
    }

    public function closeModal(): void
    {
        $this->isOpen = false;
        $this->isViewMode = false;
        $this->resetInputFields();
    }

    // ── VISTA DETALLADA ───────────────────────────────────────────────────────

    public function showDetail(int $id): void
    {
        $this->detailId = ($this->detailId === $id) ? null : $id;
    }

    public function closeDetail(): void
    {
        $this->detailId = null;
    }

    // ── RENDER ────────────────────────────────────────────────────────────────

    public function render()
    {
        $areas = Area::withCount([
                'users',
                'users as advisors_count' => fn ($q) => $q->where('role_id', 2),
                'users as students_count' => fn ($q) => $q->where('role_id', 3),
                'groups',
            ])
            ->when(!$this->showInactive, fn ($q) => $q->where('active', true))
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('city', 'like', "%{$this->search}%"))
            ->when($this->filterType, fn ($q) => $q->where('type', $this->filterType))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(12);

        // Detail data
        $detail = null;
        $detailStats = null;
        if ($this->detailId) {
            $detail = Area::with(['users.groups', 'groups'])
                ->find($this->detailId);

            if ($detail) {
                $detailStats = $this->getAreaStats($detail);
            }
        }

        $stats = [
            'total'    => Area::count(),
            'active'   => Area::where('active', true)->count(),
            'inactive' => Area::where('active', false)->count(),
        ];

        return view('livewire.admin.area-management', [
            'areas'        => $areas,
            'detail'       => $detail,
            'detailStats'  => $detailStats,
            'stats'        => $stats,
        ]);
    }

    // ── ESTADÍSTICAS ──────────────────────────────────────────────────────────

    private function getAreaStats(Area $area): array
    {
        $userIds = $area->users()->where('role_id', 3)->pluck('id');

        $totalTests = TestResponse::whereIn('user_id', $userIds)
            ->where('completed', true)->count();

        $thisMonth = TestResponse::whereIn('user_id', $userIds)
            ->where('completed', true)
            ->whereMonth('finished_at', now()->month)
            ->whereYear('finished_at', now()->year)
            ->count();

        $categories = TestResponse::whereIn('user_id', $userIds)
            ->where('completed', true)
            ->selectRaw('result_category, COUNT(*) as cnt')
            ->groupBy('result_category')
            ->orderByDesc('cnt')
            ->get();

        $concerning = TestResponse::whereIn('user_id', $userIds)
            ->where('completed', true)
            ->where(function ($q) {
                $q->where('result_category', 'like', '%severa%')
                  ->orWhere('result_category', 'like', '%moderada%');
            })->count();

        $advisors = $area->users()
            ->where('role_id', 2)
            ->where('active', true)
            ->with([
                'managedGroups' => fn ($q) => $q->where('active', true)->withCount('users'),
                'assignedTests',
            ])
            ->get();

        $activeUsers = $area->users()
            ->where('role_id', 3)
            ->where('active', true)
            ->latest()
            ->limit(8)
            ->get();

        return [
            'total_tests'    => $totalTests,
            'this_month'     => $thisMonth,
            'categories'     => $categories,
            'concerning'     => $concerning,
            'advisors'       => $advisors,
            'active_users'   => $activeUsers,
            'total_users'    => $userIds->count(),
            'total_groups'   => $area->groups()->where('active', true)->count(),
        ];
    }

    // ── HELPERS ───────────────────────────────────────────────────────────────

    private function resetInputFields(): void
    {
        $this->areaId = null;
        $this->name = '';
        $this->type = '';
        $this->city = '';
        $this->address = '';
        $this->phone = '';
        $this->active = 1;
        $this->resetValidation();
    }
}
