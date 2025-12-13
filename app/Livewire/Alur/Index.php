<?php

namespace App\Livewire\Alur;

use App\Models\InventoryLog;
use Illuminate\Support\Facades\View;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterAction = '';

    protected $queryString = ['search', 'filterAction'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterAction(): void
    {
        $this->resetPage();
    }

    public function mount(): void
    {
        View::share('mainTitle', 'Inventori');
        View::share('title', 'Alur Persediaan');
    }

    public function keRincian($referenceType, $referenceId)
    {
        if (! $referenceType || ! $referenceId) {
            return;
        }

        // Map reference type ke route
        $routeMap = [
            'hitung' => 'hitung.rincian',
            'expense' => 'belanja.rincian',
            'production' => 'produksi.rincian',
        ];

        $route = $routeMap[$referenceType] ?? null;

        if ($route) {
            $this->redirectIntended(default: route($route, ['id' => $referenceId], absolute: false), navigate: true);
        }
    }

    public function render()
    {
        $inventoryLogs = InventoryLog::query()
            ->with(['material', 'materialBatch.unit', 'user'])
            ->when($this->search, function ($query) {
                $query->whereHas('material', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })
                    ->orWhereHas('materialBatch', function ($q) {
                        $q->where('batch_number', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('user', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->filterAction, function ($query) {
                $query->where('action', $this->filterAction);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.alur.index', [
            'inventoryLogs' => $inventoryLogs,
            'actionOptions' => [
                '' => 'Semua Aksi',
                'belanja' => 'Belanja',
                'rusak' => 'Rusak',
                'hilang' => 'Hilang',
                'hitung' => 'Hitung',
                'produksi' => 'Produksi',
            ],
        ]);
    }
}
