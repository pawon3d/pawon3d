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
    public $usagePage = 1;
    public $jumlahPenggunaan = false;
    protected $listeners = [
        'delete',
        'cancelled',
    ];
    protected $rules = [
        'name' => 'required|min:3|unique:categories,name',
    ];

    protected $messages = [
        'name.required' => 'Nama kategori tidak boleh kosong',
        'name.min' => 'Nama kategori minimal 3 karakter',
        'name.unique' => 'Nama kategori sudah ada',
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
        $this->activityLogs = Activity::inLog('ingredient_categories')
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function mount()
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

    public function showAddModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function store()
    {
        $this->validate();

        IngredientCategory::create([
            'name' => $this->name,
            'is_active' => true,
        ]);

        $this->resetForm();
        $this->alert('success', 'Kategori berhasil ditambahkan');
        $this->showModal = false;
    }

    public function edit($id)
    {
        $this->category_id = $id;
        $this->products = \App\Models\IngredientCategory::where('id', $this->category_id)->withCount('details')->first()->details_count;
        $this->name = \App\Models\IngredientCategory::find($this->category_id)->name;
        $this->is_active = \App\Models\IngredientCategory::find($this->category_id)->is_active;
        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate([
            'name' => [
                'required',
                'min:3',
                Rule::unique('categories')->ignore($this->category_id),
            ],
        ]);

        $category = \App\Models\IngredientCategory::find($this->category_id);
        $category->update([
            'name' => $this->name,
            'is_active' => true,
        ]);
        $this->alert('success', 'Kategori berhasil diperbarui');
        $this->showEditModal = false;
        $this->resetForm();
    }
    public function confirmDelete()
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

    public function delete()
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

    public function showUsageModal()
    {
        $this->usagePage = 1;
        $this->usageSearch = '';
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

            return $query->paginate(2, ['*'], 'usagePage', $this->usagePage);
        }

        return new \Illuminate\Pagination\LengthAwarePaginator(
            [],
            0,
            2,
            $this->usagePage,
            ['path' => request()->url(), 'pageName' => 'usagePage']
        );
    }

    public function updatedUsageSearch()
    {
        $this->usagePage = 1;
    }

    public function previousUsagePage()
    {
        if ($this->usagePage > 1) {
            $this->usagePage--;
        }
    }

    public function nextUsagePage()
    {
        if ($this->usageMaterials && $this->usageMaterials->hasMorePages()) {
            $this->usagePage++;
        }
    }

    public function removeFromCategory($materialId)
    {
        // Find the detail entry and delete it
        $detail = \App\Models\IngredientCategoryDetail::where('category_id', $this->category_id)
            ->where('material_id', $materialId)
            ->first();

        if ($detail) {
            $detail->delete();
            $this->alert('success', 'Persediaan berhasil dihapus dari kategori');

            // Update count
            $this->products = \App\Models\IngredientCategory::where('id', $this->category_id)->withCount('details')->first()->details_count;

            // Check if current page is now empty and go to previous page if needed
            if ($this->usageMaterials->isEmpty() && $this->usagePage > 1) {
                $this->usagePage--;
            }
        }
    }

    public function resetForm()
    {
        $this->name = '';
        $this->is_active = false;
        $this->category_id = null;
    }

    public function cetakInformasi()
    {
        return redirect()->route('kategori-persediaan.pdf', [
            'search' => $this->search,
        ]);
    }
}
