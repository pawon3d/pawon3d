<?php

namespace App\Livewire\Material;

use App\Models\Material;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

class Index extends Component
{
    use LivewireAlert, WithFileUploads, WithPagination;

    public $activityLogs = [];

    public $filterStatus = '';

    public $search = '';

    public $showHistoryModal = false;

    public $viewMode = 'grid';

    public $sortField = 'name';

    public $sortDirection = 'desc';

    protected $queryString = ['viewMode', 'filterStatus', 'search', 'sortField', 'sortDirection'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
        $this->resetPage();
    }

    public function riwayatPembaruan(): void
    {
        $this->activityLogs = Activity::inLog('materials')
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function cetakInformasi()
    {
        return redirect()->route('bahan-baku.pdf', [
            'search' => $this->search,
        ]);
    }

    public function updatedViewMode($value): void
    {
        session()->put('viewMode', $value);
    }

    public function mount(): void
    {
        View::share('mainTitle', 'Inventori');
        View::share('title', 'Bahan Baku');
        if (session()->has('success')) {
            $this->alert('success', session('success'));
        }
    }

    public function render()
    {
        $materials = Material::when($this->search, function ($query) {
            return $query->where('name', 'like', '%'.$this->search.'%');
        })->when($this->filterStatus, function ($query) {
            return $query->where('is_active', $this->filterStatus === 'aktif');
        })
            ->with('material_details')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.material.index', compact('materials'));
    }
}
