<?php

namespace App\Livewire\Supplier;

use App\Models\Supplier;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

class Index extends Component
{
    use LivewireAlert, WithPagination;

    public $search = '';

    public $showHistoryModal = false;

    public $activityLogs = [];

    public $sortBy = 'name';

    public $sortDirection = 'asc';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function riwayatPembaruan()
    {
        $this->activityLogs = Activity::inLog('suppliers')
            ->with('causer:id,name')
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function mount()
    {
        View::share('title', 'Toko Persediaan');
        View::share('mainTitle', 'Inventori');
        if (session()->has('success')) {
            $this->alert('success', session('success'));
        }
    }

    public function sortByColumn($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function cetakInformasi()
    {
        return redirect()->route('supplier.pdf', [
            'search' => $this->search,
        ]);
    }

    public function render()
    {
        $suppliers = Supplier::select('id', 'name', 'contact_name', 'phone', 'image')
            ->when($this->search, function ($query) {
                return $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('contact_name', 'like', '%'.$this->search.'%')
                        ->orWhere('phone', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);

        return view('livewire.supplier.index', [
            'suppliers' => $suppliers,
        ]);
    }
}
