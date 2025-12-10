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
    use LivewireAlert, WithPagination;

    public $search = '';

    public $showHistoryModal = false;

    public $activityLogs = [];

    public $filterStatus = '';

    public $sortField = 'group';

    public $sortDirection = 'asc';

    public $name;

    public $alias;

    public $unit_id;

    public $materials;

    public $group;

    public $showModal = false;

    public $showEditModal = false;

    public $sortByCategory = false;

    public $showUsageModal = false;

    public $usageSearch = '';

    public $usageMaterials = [];

    public $usageSummary = [
        'from' => 0,
        'to' => 0,
        'total' => 0,
        'pages' => 1,
    ];

    public $usagePage = 1;

    public $usagePerPage = 2;

    public $usageSortDirection = 'asc';

    public $base_unit_id = null;

    public $conversion_factor = 1;

    public $baseUnits = [];

    protected $listeners = [
        'delete',
        'cancelled',
    ];

    protected $rules = [
        'name' => 'required|min:3|unique:units,name',
        'alias' => 'required|min:1',
        'group' => 'required',
        'base_unit_id' => 'nullable|exists:units,id',
        'conversion_factor' => 'nullable|numeric|min:0.001',
    ];

    protected $messages = [
        'name.required' => 'Nama satuan tidak boleh kosong',
        'name.min' => 'Nama satuan minimal 3 karakter',
        'name.unique' => 'Nama satuan sudah ada',
        'alias.required' => 'Singkatan tidak boleh kosong',
        'alias.min' => 'Singkatan minimal 1 karakter',
        'group.required' => 'Kelompok satuan harus dipilih',
        'base_unit_id.exists' => 'Unit dasar yang dipilih tidak ditemukan',
        'conversion_factor.numeric' => 'Faktor konversi harus berupa angka',
        'conversion_factor.min' => 'Faktor konversi minimal 0.001',
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
        $this->loadBaseUnits();
    }

    protected function loadBaseUnits()
    {
        $this->baseUnits = Unit::whereNull('base_unit_id')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
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
            'base_unit_id' => 'nullable|exists:units,id',
            'conversion_factor' => 'nullable|numeric|min:0.001',
        ]);

        // Validasi: jika ada base_unit_id, conversion_factor harus diisi
        if ($this->base_unit_id && ! $this->conversion_factor) {
            $this->addError('conversion_factor', 'Faktor konversi harus diisi jika unit dasar dipilih');

            return;
        }

        Unit::create([
            'name' => $this->name,
            'alias' => $this->alias,
            'group' => $this->group,
            'base_unit_id' => $this->base_unit_id ?: null,
            'conversion_factor' => $this->conversion_factor ?: 1,
        ]);

        $this->resetForm();
        $this->loadBaseUnits();
        $this->alert('success', 'Satuan Ukur berhasil ditambahkan');
        $this->showModal = false;
    }

    public function edit($id)
    {
        $this->unit_id = $id;
        $unit = \App\Models\Unit::find($this->unit_id);

        // Count unique materials that use this unit
        $this->materials = \App\Models\Material::whereHas('material_details', function ($query) {
            $query->where('unit_id', $this->unit_id);
        })->count();

        $this->name = $unit->name;
        $this->alias = $unit->alias;
        $this->group = $unit->group;
        $this->base_unit_id = $unit->base_unit_id;
        $this->conversion_factor = $unit->conversion_factor;

        // Load materials that use this unit
        $this->usageSearch = '';
        $this->usagePage = 1;
        $this->refreshUsageList();

        $this->showEditModal = true;
    }

    public function openUsageModal()
    {
        $this->usageSearch = '';
        $this->usagePage = 1;
        $this->refreshUsageList();
        $this->showUsageModal = true;
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
        $this->usagePage = 1;
        $this->refreshUsageList();
    }

    public function previousUsagePage()
    {
        if ($this->usagePage <= 1) {
            return;
        }

        $this->usagePage--;
        $this->refreshUsageList();
    }

    public function nextUsagePage()
    {
        if ($this->usagePage >= $this->usageSummary['pages']) {
            return;
        }

        $this->usagePage++;
        $this->refreshUsageList();
    }

    public function sortUsageMaterials()
    {
        $this->usageSortDirection = $this->usageSortDirection === 'asc' ? 'desc' : 'asc';
        $this->refreshUsageList();
    }

    protected function refreshUsageList()
    {
        if (! $this->unit_id) {
            $this->usageMaterials = [];
            $this->usageSummary = [
                'from' => 0,
                'to' => 0,
                'total' => 0,
                'pages' => 1,
            ];

            return;
        }

        $unit = \App\Models\Unit::find($this->unit_id);

        if (! $unit) {
            $this->usageMaterials = [];
            $this->usageSummary = [
                'from' => 0,
                'to' => 0,
                'total' => 0,
                'pages' => 1,
            ];
            $this->showUsageModal = false;
            $this->alert('error', 'Satuan tidak ditemukan');

            return;
        }

        $materials = \App\Models\Material::when($this->usageSearch, function ($query) {
            $term = trim($this->usageSearch);

            return $query->where('name', 'like', '%' . $term . '%');
        })->whereHas('material_details', function ($query) {
            $query->where('unit_id', $this->unit_id);
        })->with(['material_details' => function ($query) {
            $query->where('unit_id', $this->unit_id)->with('unit');
        }])
            ->orderBy('name', $this->usageSortDirection)
            ->get();

        $total = $materials->count();
        $pages = max(1, (int) ceil($total / $this->usagePerPage));
        $this->usagePage = min($this->usagePage, $pages);
        $offset = ($this->usagePage - 1) * $this->usagePerPage;

        $current = $materials
            ->slice($offset, $this->usagePerPage)
            ->values()
            ->map(fn($material) => [
                'id' => $material->id,
                'name' => $material->name,
                'unit_alias' => $material->material_details->where('unit_id', $this->unit_id)->first() ? $material->material_details->where('unit_id', $this->unit_id)->first()->unit->alias : '-',
            ]);

        $from = $total ? $offset + 1 : 0;
        $to = $total ? $offset + $current->count() : 0;

        $this->usageMaterials = $current->toArray();
        $this->usageSummary = [
            'from' => $from,
            'to' => $to,
            'total' => $total,
            'pages' => $pages,
        ];
    }

    public function update()
    {
        $this->validate([
            'name' => [
                'required',
                'min:3',
                Rule::unique('units')->ignore($this->unit_id),
            ],
            'base_unit_id' => 'nullable|exists:units,id',
            'conversion_factor' => 'nullable|numeric|min:0.001',
        ]);

        // Validasi: jika ada base_unit_id, conversion_factor harus diisi
        if ($this->base_unit_id && ! $this->conversion_factor) {
            $this->addError('conversion_factor', 'Faktor konversi harus diisi jika unit dasar dipilih');

            return;
        }

        // Validasi: unit tidak boleh reference dirinya sendiri sebagai base_unit
        if ($this->base_unit_id === $this->unit_id) {
            $this->addError('base_unit_id', 'Unit tidak boleh menjadi referensi dari dirinya sendiri');

            return;
        }

        $unit = \App\Models\Unit::find($this->unit_id);
        $unit->update([
            'name' => $this->name,
            'alias' => $this->alias,
            'group' => $this->group,
            'base_unit_id' => $this->base_unit_id ?: null,
            'conversion_factor' => $this->conversion_factor ?: null,
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
            $usageCount = \App\Models\Material::whereHas('material_details', function ($query) {
                $query->where('unit_id', $this->unit_id);
            })->count();

            if ($usageCount > 0) {
                $this->alert('error', 'Satuan tidak dapat dihapus karena masih digunakan!');

                return;
            }

            $unit->delete();
            $this->alert('success', 'Satuan berhasil dihapus!');

            Flux::modals()->close();
            $this->resetForm();
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
        $this->usageMaterials = [];
        $this->usageSearch = '';
        $this->usagePage = 1;
        $this->base_unit_id = null;
        $this->conversion_factor = null;
        $this->resetErrorBag();
        $this->resetValidation();
    }
}
