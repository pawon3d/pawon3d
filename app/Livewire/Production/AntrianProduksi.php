<?php

namespace App\Livewire\Production;

use App\Models\Production;
use Illuminate\Support\Facades\View;
use Livewire\Component;
use Livewire\WithPagination;

class AntrianProduksi extends Component
{
    use WithPagination;

    public $search = '';

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    public function mount()
    {
        View::share('title', 'Antrian Produksi');
        View::share('mainTitle', 'Produksi');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $query = Production::with(['details.product'])
            ->where('productions.method', 'siap-beli')
            ->whereIn('productions.status', ['Belum Diproses']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('productions.id', 'like', '%'.$this->search.'%')
                    ->orWhereHas('details.product', function ($q) {
                        $q->where('name', 'like', '%'.$this->search.'%');
                    });
            });
        }

        $productions = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.production.antrian-produksi', [
            'productions' => $productions,
        ]);
    }
}
