<?php

namespace App\Livewire\Material;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Material;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Index extends Component
{
    use WithPagination, LivewireAlert;

    public $name, $quantity, $unit, $material_id;
    public $editId = null;
    public $search = '';
    public $showModal = false;
    public $showEditModal = false;
    protected $listeners = [
        'delete'
    ];

    protected $rules = [
        'name' => 'required|min:3',
        'quantity' => 'required|numeric',
        'unit' => 'required',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function mount()
    {
        View::share('title', 'Bahan Baku');
    }


    public function render()
    {
        $materials = Material::when($this->search, function ($query) {
            return $query->where('name', 'like', '%' . $this->search . '%');
        })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.material.index', compact('materials'));
    }

    public function openAddModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(Material $material)
    {
        $this->editId = $material->id;
        $this->name = $material->name;
        $this->quantity = $material->quantity;
        $this->unit = $material->unit;
        $this->showEditModal = true;
    }

    public function store()
    {
        $this->validate();

        Material::create([
            'name' => $this->name,
            'quantity' => $this->quantity,
            'unit' => $this->unit,
        ]);

        $this->showModal = false;
        $this->alert('success', 'Bahan Baku berhasil ditambahkan!');
        $this->resetForm();
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|min:3',
            'quantity' => 'required|numeric',
            'unit' => 'required',
        ]);

        $material = Material::find($this->editId);
        $data = [
            'name' => $this->name,
            'quantity' => $this->quantity,
            'unit' => $this->unit,
        ];

        $material->update($data);

        $this->showEditModal = false;
        $this->alert('success', 'Bahan Baku berhasil diupdate!');
        $this->resetForm();
    }

    public function confirmDelete(Material $material)
    {

        // Simpan ID material ke dalam properti
        $this->material_id = $material->id;

        // Konfirmasi menggunakan Livewire Alert
        $this->alert('warning', 'Apakah Anda yakin ingin menghapus bahan baku ini?', [
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

        $material = Material::find($this->material_id);

        if ($material) {
            $material->delete();
            $this->alert('success', 'Bahan Baku berhasil dihapus!');
        } else {
            $this->alert('error', 'Bahan Baku tidak ditemukan!');
        }

        // Reset setelah dihapus
        $this->reset('material_id');
    }

    private function resetForm()
    {
        $this->reset(['name', 'quantity', 'unit', 'editId']);
        $this->resetErrorBag();
    }
}