<?php

namespace App\Livewire\Category;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Category;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Index extends Component
{
    use WithPagination, LivewireAlert;

    public $name, $category_id;
    public $editId = null;
    public $search = '';
    public $showModal = false;
    public $showEditModal = false;
    protected $listeners = [
        'delete'
    ];

    protected $rules = [
        'name' => 'required|min:3',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
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
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.category.index', compact('categories'));
    }

    public function openAddModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(Category $category)
    {
        $this->editId = $category->id;
        $this->name = $category->name;
        $this->showEditModal = true;
    }

    public function store()
    {
        $this->validate();

        Category::create([
            'name' => $this->name,
        ]);

        $this->showModal = false;
        $this->alert('success', 'Kategori berhasil ditambahkan!');
        $this->resetForm();
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|min:3',
        ]);

        $category = Category::find($this->editId);
        $data = [
            'name' => $this->name,
        ];

        $category->update($data);

        $this->showEditModal = false;
        $this->alert('success', 'Kategori berhasil diupdate!');
        $this->resetForm();
    }

    public function confirmDelete(Category $category)
    {

        // Simpan ID category ke dalam properti
        $this->category_id = $category->id;

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

    // Method delete yang benar
    public function delete()
    {

        $category = Category::find($this->category_id);

        if ($category) {
            $category->delete();
            $this->alert('success', 'Kategori berhasil dihapus!');
        } else {
            $this->alert('error', 'Kategori tidak ditemukan!');
        }

        // Reset setelah dihapus
        $this->reset('category_id');
    }

    private function resetForm()
    {
        $this->reset(['name', 'editId']);
        $this->resetErrorBag();
    }
}