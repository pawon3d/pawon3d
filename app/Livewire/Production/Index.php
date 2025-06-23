<?php

namespace App\Livewire\Production;

use Livewire\Component;
use App\Models\Production;
use Livewire\WithPagination;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Spatie\Activitylog\Models\Activity;

class Index extends Component
{
    use WithPagination, LivewireAlert;

    public $activityLogs = [];
    public $filterStatus = '';
    public $search = '';
    public $showHistoryModal = false;
    public $viewMode = 'grid';
    public $method = 'pesanan-reguler';
    public $sortField = 'production_number';
    public $sortDirection = 'desc';

    protected $queryString = ['viewMode', 'method', 'search', 'sortField', 'sortDirection'];

    public function riwayatPembaruan()
    {
        $this->activityLogs = Activity::inLog('productions')
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function cetakInformasi()
    {
        return redirect()->route('produksi.pdf', [
            'search' => $this->search,
        ]);
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
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
        $this->resetPage();
    }

    public function mount()
    {
        View::share('title', 'Produksi');
        View::share('mainTitle', 'Produksi');


        if (session()->has('success')) {
            $this->alert('success', session('success'));
        }
        $this->method = session('method', 'pesanan-reguler');
    }

    public function updatedMethod($value)
    {
        session()->put('method', $value);
    }

    public function render()
    {
        $query = Production::with(['details.product', 'workers'])
            ->where('productions.production_number', 'like', '%' . $this->search . '%')
            ->where('productions.method', $this->method);

        if ($this->sortField === 'product_name') {
            $query->join('production_details', 'productions.id', '=', 'production_details.production_id')
                ->join('products', 'production_details.product_id', '=', 'products.id')
                ->orderBy('products.name', $this->sortDirection);
        } elseif ($this->sortField === 'worker_name') {
            $query->join('production_workers', 'productions.id', '=', 'production_workers.production_id')
                ->join('users', 'production_workers.user_id', '=', 'users.id')
                ->orderBy('users.name', $this->sortDirection);
        } else {
            $query->orderBy("productions.{$this->sortField}", $this->sortDirection);
        }

        $productions = $query->select('productions.*')->distinct()->paginate(10);


        return view('livewire.production.index', [
            'productions' => $productions,
        ]);
    }
}
