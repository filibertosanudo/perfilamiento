<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TestResponse;
use Illuminate\Support\Collection;

class MyResults extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterCategory = ''; // Filtrar por categoría de resultado
    public string $sortField = 'finished_at';
    public string $sortDirection = 'desc';

    // Reset paginación
    public function updatingSearch(): void { $this->resetPage(); }
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
        $user = auth()->user();

        $query = TestResponse::where('user_id', $user->id)
            ->where('completed', true)
            ->with(['assignment.test', 'assignment.assignedBy'])
            ->when($this->search, function ($q) {
                $q->whereHas('assignment.test', function ($q) {
                    $q->where('name', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterCategory, function ($q) {
                $q->where('result_category', 'like', "%{$this->filterCategory}%");
            });

        $results = $query
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        // Obtener categorías únicas para el filtro
        $categories = TestResponse::where('user_id', $user->id)
            ->where('completed', true)
            ->distinct()
            ->pluck('result_category')
            ->filter()
            ->sort()
            ->values();

        // Estadísticas
        $stats = $this->getStatistics($user);

        return view('livewire.user.my-results', [
            'results' => $results,
            'categories' => $categories,
            'stats' => $stats,
        ]);
    }

    private function getStatistics($user): array
    {
        $allResults = TestResponse::where('user_id', $user->id)
            ->where('completed', true)
            ->get();

        $thisMonth = $allResults->filter(fn($r) => $r->finished_at->isCurrentMonth());
        $thisYear = $allResults->filter(fn($r) => $r->finished_at->isCurrentYear());

        return [
            'total' => $allResults->count(),
            'this_month' => $thisMonth->count(),
            'this_year' => $thisYear->count(),
            'avg_score' => round($allResults->avg('numeric_result'), 1),
        ];
    }
}