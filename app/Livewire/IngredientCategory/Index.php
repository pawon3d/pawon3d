<?php

namespace App\Livewire\IngredientCategory;

use App\Models\IngredientCategory;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Spatie\Activitylog\Models\Activity;

class Index extends Component
{
    use WithPagination, LivewireAlert;
    public $search = '';
    public $showHistoryModal = false;
    public $activityLogs = [];
    public $filterStatus = '';
    public $sortField = 'name';
    public $sortDirection = 'desc';
    public $name, $is_active = true, $category_id, $products;
    public $showModal = false;
    public $showEditModal = false;
    public $sortByCategory = false;
    public $usageSearch = '';
    public $usageSortDirection = 'asc';
    public $jumlahPenggunaan = false;
    protected $listeners = [
        'delete',
        'cancelled',
    ];
    protected $rules = [
        'name' => 'required|min:3|unique:ingredient_categories,name',
    ];

    protected $messages = [
        'name.required' => 'Nama kategori tidak boleh kosong',
        'name.min' => 'Nama kategori minimal 3 karakter',
        'name.unique' => 'Nama kategori sudah ada',
    ];

    protected $queryString = ['search', 'filterStatus',  'sortField', 'sortDirection'];

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
        $this->activityLogs = Activity::inLog('ingredient_categories')
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function mount(): void
    {
        View::share('title', 'Kategori Persediaan');
        View::share('mainTitle', 'Inventori');
    }
    public function render()
    {
        $categories = IngredientCategory::when($this->search, function ($query) {
            return $query->where('name', 'like', '%' . $this->search . '%');
        })
            ->when($this->filterStatus, function ($query) {
                return $query->where('is_active', $this->filterStatus === 'aktif');
            })->with('details')->withCount('details')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
        return view('livewire.ingredient-category.index', [
            'categories' => $categories,
            'usageMaterials' => $this->usageMaterials,
        ]);
    }

    public function showAddModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function store(): void
    {
        $this->validate();

        IngredientCategory::create([
            'name' => $this->name,
            'is_active' => $this->is_active,
        ]);

        $this->resetForm();
        $this->alert('success', 'Kategori berhasil ditambahkan');
        $this->showModal = false;
    }

    public function edit($id): void
    {
        $category = IngredientCategory::withCount('details')->findOrFail($id);

        $this->category_id = $id;
        $this->products = $category->details_count;
        $this->name = $category->name;
        $this->is_active = $category->is_active;
        $this->showEditModal = true;
    }

    public function update(): void
    {
        $this->validate([
            'name' => [
                'required',
                'min:3',
                Rule::unique('ingredient_categories')->ignore($this->category_id),
            ],
        ]);

        $category = IngredientCategory::findOrFail($this->category_id);
        $category->update([
            'name' => $this->name,
            'is_active' => $this->is_active,
        ]);
        $this->alert('success', 'Kategori berhasil diperbarui');
        $this->showEditModal = false;
        $this->resetForm();
    }
    public function confirmDelete(): void
    {
        // Konfirmasi menggunakan Livewire Alert
        $this->alert('warning', 'Apakah Anda yakin ingin menghapus kategori ini?', [
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

    public function delete(): void
    {

        $category = IngredientCategory::find($this->category_id);

        if ($category) {
            $category->delete();
            $this->alert('success', 'Kategori berhasil dihapus!');
            $this->reset('category_id');
            Flux::modals()->close();
        } else {
            $this->alert('error', 'Kategori tidak ditemukan!');
        }
    }

    public function showUsageModal(): void
    {
        $this->usageSearch = '';
        $this->resetPage('usagePage');
        Flux::modal('jumlah-penggunaan')->show();
    }

    public function getUsageMaterialsProperty()
    {
        $category = \App\Models\IngredientCategory::with('details.material')->find($this->category_id);

        if ($category) {
            $materialIds = $category->details->pluck('material.id')->unique()->filter()->values()->all();

            $query = \App\Models\Material::whereIn('id', $materialIds);

            // Apply search filter
            if ($this->usageSearch) {
                $query->where('name', 'like', '%' . $this->usageSearch . '%');
            }

            // Apply sorting
            $query->orderBy('name', $this->usageSortDirection);

            return $query->paginate(2, ['*'], 'usagePage');
        }

        return new \Illuminate\Pagination\LengthAwarePaginator(
            [],
            0,
            2,
            1,
            ['path' => request()->url(), 'pageName' => 'usagePage']
        );
    }

    public function updatedUsageSearch(): void
    {
        $this->resetPage('usagePage');
    }

    public function sortUsageMaterials(): void
    {
        $this->usageSortDirection = $this->usageSortDirection === 'asc' ? 'desc' : 'asc';
    }

    public function removeFromCategory($materialId): void
    {
        // Find the detail entry and delete it
        $detail = \App\Models\IngredientCategoryDetail::where('category_id', $this->category_id)
            ->where('material_id', $materialId)
            ->first();

        if ($detail) {
            $detail->delete();
            $this->alert('success', 'Persediaan berhasil dihapus dari kategori');

            // Update count
            $this->products = IngredientCategory::where('id', $this->category_id)->withCount('details')->first()->details_count;
        }
    }

    public function resetForm(): void
    {
        $this->name = '';
        $this->is_active = true;
        $this->category_id = null;
    }

    public function cetakInformasi()
    {
        return redirect()->route('kategori-persediaan.pdf', [
            'search' => $this->search,
        ]);
    }
}
