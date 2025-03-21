<?php

namespace App\Livewire\Prize;

use App\Models\Prize;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination, LivewireAlert;
    public $product_id, $prize_id, $code;
    public $editId = null;
    public $search_code = '';
    public $showModal = false;
    public $showEditModal = false;
    protected $listeners = [
        'delete',
        'productSelected' => 'handleProductSelected',
    ];
    public function render()
    {
        return view('livewire.prize.index', [
            'prizes' => \App\Models\Prize::when($this->search_code, function ($query) {
                return $query->where('code', 'like', '%' . $this->search_code . '%');
            })->with('product')->paginate(5),
        ]);
    }

    public function openAddModal()
    {
        $this->resetForm();
        $this->dispatch('clearProduct');
        $this->showModal = true;
    }

    public function openEditModal(Prize $prize)
    {
        $this->editId = $prize->id;
        $this->product_id = $prize->product_id;
        $this->code = $prize->code;
        $this->dispatch('productSelectedEdit', $this->product_id);
        $this->showEditModal = true;
    }

    public function handleProductSelected($productId)
    {
        $this->product_id = $productId;
    }

    public function store()
    {

        Prize::create([
            'product_id' => $this->product_id,
        ]);

        $this->showModal = false;
        $this->dispatch('clearProduct');
        $this->alert('success', 'Kode Hadiah berhasil ditambahkan!');

        $this->resetForm();
    }

    public function update()
    {

        $prize = Prize::find($this->editId);
        $data = [
            'product_id' => $this->product_id,
        ];

        $prize->update($data);

        $this->showEditModal = false;
        $this->dispatch('clearProduct');
        $this->alert('success', 'Hadiah berhasil diupdate!');
        $this->resetForm();
    }

    public function confirmDelete(Prize $prize)
    {

        // Simpan ID prize ke dalam properti
        $this->prize_id = $prize->id;

        // Konfirmasi menggunakan Livewire Alert
        $this->alert('warning', 'Apakah Anda yakin ingin menghapus kode hadiah ini?', [
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

        $prize = Prize::find($this->prize_id);

        if ($prize) {
            $prize->delete();
            $this->alert('success', 'Kode Hadiah berhasil dihapus!');
        } else {
            $this->alert('error', 'Kode Hadiah tidak ditemukan!');
        }

        // Reset setelah dihapus
        $this->reset('prize_id');
    }

    private function resetForm()
    {
        $this->reset(['product_id', 'editId']);
        $this->resetErrorBag();
    }
}