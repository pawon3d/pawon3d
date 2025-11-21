<?php

namespace App\Livewire\TypeCost;

use App\Models\TypeCost;
use App\Models\Product;
use Flux\Flux;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Str;

class Index extends Component
{
    use WithPagination, LivewireAlert;

    public $search = '';
    public $showHistoryModal = false;
    public $activityLogs = [];
    public $filterStatus = '';
    public $sortField = 'name';
    public $sortDirection = 'desc';
    public $name, $type_cost_id, $products;
    public $showModal = false;
    public $showEditModal = false;
    public $usageSearch = '';
    public $usageProducts = null;

    protected $listeners = [
        'delete',
        'cancelled',
    ];

    protected $rules = [
        'name' => 'required|min:3|unique:type_costs,name',
    ];

    protected $messages = [
        'name.required' => 'Nama jenis biaya tidak boleh kosong',
        'name.min' => 'Nama jenis biaya minimal 3 karakter',
        'name.unique' => 'Nama jenis biaya sudah ada',
    ];

    protected $queryString = ['search', 'filterStatus', 'sortField', 'sortDirection'];

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

    public function riwayatPembaruan()
    {
        $this->activityLogs = Activity::inLog('type_costs')
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function mount()
    {
        View::share('title', 'Jenis Biaya Produksi');
        View::share('mainTitle', 'Inventori');
    }

    public function render()
    {
        $typeCosts = TypeCost::when($this->search, function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%');
        })->withCount('otherCosts')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.type-cost.index', compact('typeCosts'));
    }

    public function showAddModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|min:3|unique:type_costs,name',
        ]);

        TypeCost::create([
            'id' => Str::uuid(),
            'name' => $this->name,
        ]);

        $this->resetForm();
        $this->alert('success', 'Jenis Biaya berhasil ditambahkan');
        $this->showModal = false;
    }

    public function edit($id)
    {
        $this->type_cost_id = $id;
        $typeCost = TypeCost::where('id', $this->type_cost_id)->withCount('otherCosts')->first();
        $this->products = $typeCost->other_costs_count;
        $this->name = $typeCost->name;

        // Load products that use this type cost
        $this->loadUsageProducts();

        $this->showEditModal = true;
    }

    public function showUsageModal()
    {
        $this->loadUsageProducts();
        Flux::modal('usage-modal')->show();
    }

    protected function loadUsageProducts()
    {
        if ($this->type_cost_id) {
            $this->usageProducts = Product::when($this->usageSearch, function ($query) {
                $query->where('name', 'like', '%' . $this->usageSearch . '%');
            })->whereHas('other_costs', function ($query) {
                $query->where('type_cost_id', $this->type_cost_id);
            })->with(['other_costs' => function ($query) {
                $query->where('type_cost_id', $this->type_cost_id);
            }])->get();
        }
    }

    public function updatedUsageSearch()
    {
        $this->loadUsageProducts();
    }

    public function update()
    {
        $this->validate([
            'name' => [
                'required',
                'min:3',
                Rule::unique('type_costs')->ignore($this->type_cost_id),
            ],
        ]);

        $typeCost = TypeCost::find($this->type_cost_id);
        $typeCost->update([
            'name' => $this->name,
        ]);
        $this->alert('success', 'Jenis Biaya berhasil diperbarui');
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function confirmDelete()
    {
        $this->alert('warning', 'Apakah Anda yakin ingin menghapus jenis biaya ini?', [
            'showConfirmButton' => true,
            'showCancelButton' => true,
            'confirmButtonText' => 'Ya, hapus',
            'cancelButtonText' => 'Batal',
            'onConfirmed' => 'delete',
            'onCancelled' => 'cancelled',
            'toast' => false,
            'position' => 'center',
            'timer' => null,
        ]);
    }

    public function delete()
    {
        $typeCost = TypeCost::find($this->type_cost_id);

        if ($typeCost) {
            // Check if type cost is being used
            if ($typeCost->otherCosts()->count() > 0) {
                $this->alert('error', 'Jenis Biaya tidak dapat dihapus karena masih digunakan!');
                return;
            }

            $typeCost->delete();
            $this->alert('success', 'Jenis Biaya berhasil dihapus!');
            $this->reset('type_cost_id');
            $this->showEditModal = false;
        } else {
            $this->alert('error', 'Jenis Biaya tidak ditemukan!');
        }
    }

    public function resetForm()
    {
        $this->name = '';
        $this->type_cost_id = null;
        $this->products = null;
        $this->usageProducts = null;
        $this->usageSearch = '';
    }
}
