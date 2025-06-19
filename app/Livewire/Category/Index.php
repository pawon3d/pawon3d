<?php

namespace App\Livewire\Category;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Category;
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
    public $name, $is_active = false, $category_id;
    public $showModal = false;
    public $showEditModal = false;
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
    }


    public function render()
    {
        $categories = Category::when($this->search, function ($query) {
            return $query->where('name', 'like', '%' . $this->search . '%');
        })
            ->when($this->filterStatus, function ($query) {
                return $query->where('is_active', $this->filterStatus === 'aktif');
            })->with('products', 'productCategories')->withCount('products')->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);


        return view('livewire.category.index', compact('categories'));
    }

    public function store()
    {
        $this->validate();

        Category::create([
            'name' => $this->name,
            'is_active' => $this->is_active,
        ]);

        $this->resetForm();
        $this->alert('success', 'Kategori berhasil ditambahkan');
        $this->showModal = false;
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
            'is_active' => $this->is_active,
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
            return redirect()->intended(route('kategori'));
        } else {
            $this->alert('error', 'Kategori tidak ditemukan!');
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
        return redirect()->route('kategori.pdf', [
            'search' => $this->search,
        ]);
    }
}
