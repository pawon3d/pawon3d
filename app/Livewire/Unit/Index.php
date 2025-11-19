<?php

namespace App\Livewire\Unit;

use App\Models\Unit;
use Flux\Flux;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

class Index extends Component
{
    use WithPagination, LivewireAlert;
    public $search = '';
    public $showHistoryModal = false;
    public $activityLogs = [];
    public $filterStatus = '';
    public $sortField = 'group';
    public $sortDirection = 'asc';
    public $name, $alias, $unit_id, $materials, $group;
    public $showModal = false;
    public $showEditModal = false;
    public $sortByCategory = false;
    public $usageSearch = '';
    public $usageMaterials = null;

    protected $listeners = [
        'delete',
        'cancelled',
    ];
    protected $rules = [
        'name' => 'required|min:3|unique:units,name',
        'alias' => 'required|min:1',
        'group' => 'required',
    ];

    protected $messages = [
        'name.required' => 'Nama satuan tidak boleh kosong',
        'name.min' => 'Nama satuan minimal 3 karakter',
        'name.unique' => 'Nama satuan sudah ada',
        'alias.required' => 'Singkatan tidak boleh kosong',
        'alias.min' => 'Singkatan minimal 1 karakter',
        'group.required' => 'Kelompok satuan harus dipilih',
    ];

    protected $queryString = ['search', 'filterStatus',  'sortField', 'sortDirection'];

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
        $this->activityLogs = Activity::inLog('units')
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function mount()
    {
        View::share('title', 'Satuan Ukur');
        View::share('mainTitle', 'Inventori');
    }
    public function render()
    {
        $units = \App\Models\Unit::when($this->search, function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%');
        })->with('material_details')->withCount('material_details')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
        return view('livewire.unit.index', compact('units'));
    }

    public function showAddModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|min:3|unique:units,name',
            'alias' => 'required|min:1',
            'group' => 'required',
        ]);

        Unit::create([
            'name' => $this->name,
            'alias' => $this->alias,
            'group' => $this->group,
        ]);

        $this->resetForm();
        $this->alert('success', 'Satuan Ukur berhasil ditambahkan');
        $this->showModal = false;
    }

    public function edit($id)
    {
        $this->unit_id = $id;
        $unit = \App\Models\Unit::where('id', $this->unit_id)->withCount('material_details')->first();
        $this->materials = $unit->material_details_count;
        $this->name = $unit->name;
        $this->alias = $unit->alias;
        $this->group = $unit->group;

        // Load materials that use this unit
        $this->loadUsageMaterials();

        $this->showEditModal = true;
    }

    public function showUsageModal()
    {
        $this->loadUsageMaterials();
        Flux::modal('usage-modal')->show();
    }

    protected function loadUsageMaterials()
    {
        if ($this->unit_id) {
            $this->usageMaterials = \App\Models\Material::when($this->usageSearch, function ($query) {
                $query->where('name', 'like', '%' . $this->usageSearch . '%');
            })->whereHas('material_details', function ($query) {
                $query->where('unit_id', $this->unit_id);
            })->with(['material_details' => function ($query) {
                $query->where('unit_id', $this->unit_id)->with('unit');
            }])->get();
        }
    }

    public function updatedUsageSearch()
    {
        $this->loadUsageMaterials();
    }

    public function update()
    {
        $this->validate([
            'name' => [
                'required',
                'min:3',
                Rule::unique('units')->ignore($this->unit_id),
            ],
        ]);

        $unit = \App\Models\Unit::find($this->unit_id);
        $unit->update([
            'name' => $this->name,
            'alias' => $this->alias,
            'group' => $this->group,
        ]);
        $this->alert('success', 'Satuan Ukur berhasil diperbarui');
        $this->showEditModal = false;
        $this->resetForm();
    }
    public function confirmDelete()
    {
        // Konfirmasi menggunakan Livewire Alert
        $this->alert('warning', 'Apakah Anda yakin ingin menghapus satuan ini?', [
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
        $unit = Unit::find($this->unit_id);

        if ($unit) {
            // Check if unit is being used
            if ($unit->material_details()->count() > 0) {
                $this->alert('error', 'Satuan tidak dapat dihapus karena masih digunakan!');
                return;
            }

            $unit->delete();
            $this->alert('success', 'Satuan berhasil dihapus!');
            $this->reset('unit_id');
            $this->showEditModal = false;
        } else {
            $this->alert('error', 'Satuan tidak ditemukan!');
        }
    }


    public function resetForm()
    {
        $this->name = '';
        $this->alias = '';
        $this->group = '';
        $this->unit_id = null;
        $this->materials = null;
        $this->usageMaterials = null;
        $this->usageSearch = '';
    }
}
