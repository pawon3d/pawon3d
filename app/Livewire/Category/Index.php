<?php

namespace App\Livewire\Category;

use App\Models\Category;
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

    public $sortField = 'name';

    public $sortDirection = 'desc';

    public $name;

    public $is_active = true;

    public $category_id;

    public $products;

    public $showModal = false;

    public $showEditModal = false;

    public $showUsageModal = false;

    public $usageCategoryId;

    public $usageProducts = [];

    public $usageSummary = [
        'from' => 0,
        'to' => 0,
        'total' => 0,
        'pages' => 1,
    ];

    public $usagePage = 1;

    public $usagePerPage = 2;

    public $usageSearch = '';

    public $usageSortDirection = 'asc';

    public $sortByCategory = false;

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

    protected $queryString = ['search', 'sortField', 'sortDirection'];

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function updatedUsageSearch()
    {
        $this->usagePage = 1;
        $this->refreshUsageList();
    }

    public function riwayatPembaruan()
    {
        $this->activityLogs = Activity::inLog('categories')
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function mount()
    {
        View::share('title', 'Kategori');
        View::share('mainTitle', 'Inventori');
    }

    public function render()
    {
        $categories = Category::when($this->search, function ($query) {
            return $query->where('name', 'like', '%'.$this->search.'%');
        })
            ->when($this->filterStatus, function ($query) {
                return $query->where('is_active', $this->filterStatus === 'aktif');
            })->with('products', 'productCategories')->withCount('products')->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.category.index', compact('categories'));
    }

    public function showAddModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function store()
    {
        $this->validate();

        Category::create([
            'name' => $this->name,
            'is_active' => (bool) $this->is_active,
        ]);

        $this->resetForm();
        $this->alert('success', 'Kategori berhasil ditambahkan');
        $this->showModal = false;
    }

    public function edit($id)
    {
        $category = Category::withCount('products')->find($id);

        if (! $category) {
            $this->alert('error', 'Kategori tidak ditemukan');

            return;
        }

        $this->category_id = $id;
        $this->products = $category->products_count;
        $this->name = $category->name;
        $this->is_active = (bool) $category->is_active;
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

        $category = \App\Models\Category::find($this->category_id);
        $category->update([
            'name' => $this->name,
            'is_active' => (bool) $this->is_active,
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

        $category = Category::find($this->category_id);

        if ($category) {
            $category->delete();
            $this->alert('success', 'Kategori berhasil dihapus!');
            $this->reset('category_id');
            Flux::modals()->close();
        } else {
            $this->alert('error', 'Kategori tidak ditemukan!');
        }
    }

    public function openUsageModal($categoryId)
    {
        $this->usageCategoryId = $categoryId;
        $this->usageSearch = '';
        $this->usagePage = 1;
        $this->refreshUsageList();
        $this->showUsageModal = true;
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

    public function sortUsageProducts()
    {
        $this->usageSortDirection = $this->usageSortDirection === 'asc' ? 'desc' : 'asc';
        $this->refreshUsageList();
    }

    protected function refreshUsageList()
    {
        if (! $this->usageCategoryId) {
            $this->usageProducts = [];
            $this->usageSummary = [
                'from' => 0,
                'to' => 0,
                'total' => 0,
                'pages' => 1,
            ];

            return;
        }

        $category = Category::find($this->usageCategoryId);

        if (! $category) {
            $this->usageProducts = [];
            $this->usageSummary = [
                'from' => 0,
                'to' => 0,
                'total' => 0,
                'pages' => 1,
            ];
            $this->showUsageModal = false;
            $this->alert('error', 'Kategori tidak ditemukan');

            return;
        }

        $products = $category->products()
            ->when($this->usageSearch, function ($query) {
                $term = trim($this->usageSearch);

                return $query->where('products.name', 'like', '%'.$term.'%');
            })
            ->orderBy('products.name', $this->usageSortDirection)
            ->get();

        $total = $products->count();
        $pages = max(1, (int) ceil($total / $this->usagePerPage));
        $this->usagePage = min($this->usagePage, $pages);
        $offset = ($this->usagePage - 1) * $this->usagePerPage;

        $current = $products
            ->slice($offset, $this->usagePerPage)
            ->values()
            ->map(fn ($product) => [
                'id' => $product->id,
                'name' => $product->name,
            ]);

        $from = $total ? $offset + 1 : 0;
        $to = $total ? $offset + $current->count() : 0;

        $this->usageProducts = $current->toArray();
        $this->usageSummary = [
            'from' => $from,
            'to' => $to,
            'total' => $total,
            'pages' => $pages,
        ];
    }

    public function resetForm()
    {
        $this->name = '';
        $this->is_active = true;
        $this->category_id = null;
        $this->products = null;
    }

    public function cetakInformasi()
    {
        return redirect()->route('kategori.pdf', [
            'search' => $this->search,
        ]);
    }
}
