<?php

namespace App\Livewire\Advisor;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TestResponse;
use App\Models\User;
use App\Models\Test;
use Illuminate\Support\Collection;

class AdvisorResults extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $filterUser = null;
    public ?int $filterTest = null;
    public string $filterCategory = '';
    public string $sortField = 'finished_at';
    public string $sortDirection = 'desc';

    // Reset paginación
    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterUser(): void { $this->resetPage(); }
    public function updatingFilterTest(): void { $this->resetPage(); }
    public function updatingFilterCategory(): void { $this->resetPage(); }

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

    public function render()
    {
        $currentUser = auth()->user();

        // Obtener IDs de usuarios según el rol
        $myUserIds = $this->getMyUserIds($currentUser);

        // Query de resultados
        $query = TestResponse::whereIn('user_id', $myUserIds)
            ->where('completed', true)
            ->with(['user', 'assignment.test', 'assignment.assignedBy'])
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->whereHas('user', function ($q) {
                        $q->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                    })
                    ->orWhereHas('assignment.test', function ($q) {
                        $q->where('name', 'like', "%{$this->search}%");
                    });
                });
            })
            ->when($this->filterUser, fn($q) => $q->where('user_id', $this->filterUser))
            ->when($this->filterTest, fn($q) => $q->whereHas('assignment', function ($q) {
                $q->where('test_id', $this->filterTest);
            }))
            ->when($this->filterCategory, fn($q) => $q->where('result_category', 'like', "%{$this->filterCategory}%"));

        $results = $query
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        // Obtener datos para filtros
        $myUsers = User::whereIn('id', $myUserIds)->orderBy('first_name')->get();
        
        $availableTests = Test::whereHas('assignments.responses', function ($q) use ($myUserIds) {
            $q->whereIn('user_id', $myUserIds)->where('completed', true);
        })->orderBy('name')->get();

        $categories = TestResponse::whereIn('user_id', $myUserIds)
            ->where('completed', true)
            ->distinct()
            ->pluck('result_category')
            ->filter()
            ->sort()
            ->values();

        // Estadísticas
        $stats = $this->getStatistics($myUserIds);

        return view('livewire.advisor.advisor-results', [
            'results' => $results,
            'myUsers' => $myUsers,
            'availableTests' => $availableTests,
            'categories' => $categories,
            'stats' => $stats,
        ]);
    }

    private function getMyUserIds($advisor): array
    {
        // Si es admin, devolver todos los usuarios
        if ($advisor->role_id === 1) {
            return User::where('role_id', 3)
                ->where('active', true)
                ->pluck('id')
                ->toArray();
        }

        // Si es orientador, solo sus usuarios
        return User::whereHas('groups', function ($q) use ($advisor) {
            $q->where('creator_id', $advisor->id);
        })
        ->where('role_id', 3)
        ->where('active', true)
        ->pluck('id')
        ->toArray();
    }

    private function getStatistics($userIds): array
    {
        $allResults = TestResponse::whereIn('user_id', $userIds)
            ->where('completed', true)
            ->get();

        $thisMonth = $allResults->filter(fn($r) => $r->finished_at->isCurrentMonth());
        $thisWeek = $allResults->filter(fn($r) => $r->finished_at->isCurrentWeek());

        $concerningCategories = ['severa', 'moderada', 'baja'];
        $needAttention = $allResults->filter(function ($r) use ($concerningCategories) {
            $category = strtolower($r->result_category ?? '');
            foreach ($concerningCategories as $concerning) {
                if (str_contains($category, $concerning)) {
                    return true;
                }
            }
            return false;
        });

        return [
            'total' => $allResults->count(),
            'this_month' => $thisMonth->count(),
            'this_week' => $thisWeek->count(),
            'need_attention' => $needAttention->count(),
        ];
    }
}