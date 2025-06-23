<?php

namespace App\Livewire\Category;

use App\Models\Category;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rule;
use Spatie\Activitylog\Models\Activity;
use Livewire\Component;


class Rincian extends Component
{
    use \Jantinnerezo\LivewireAlert\LivewireAlert;

    public $category_id;
    public $name, $is_active;
    public $products;
    public $showHistoryModal = false;
    public $activityLogs = [];

    protected $listeners = [
        'delete'
    ];

    protected $messages = [
        'name.required' => 'Nama kategori tidak boleh kosong',
        'name.min' => 'Nama kategori minimal 3 karakter',
        'name.unique' => 'Nama kategori sudah ada',
    ];

    public function mount($id)
    {
        $this->category_id = $id;
        $this->products = \App\Models\Product::where('category_id', $this->category_id)->count();
        $this->name = \App\Models\Category::find($this->category_id)->name;
        $this->is_active = \App\Models\Category::find($this->category_id)->is_active;
        View::share('title', 'Rincian Kategori');
        View::share('mainTitle', 'Inventori');
    }

    public function riwayatPembaruan()
    {
        $this->activityLogs = Activity::inLog('categories')->where('subject_id', $this->category_id)
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
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

        return redirect()->intended(route('kategori'));
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

    public function render()
    {
        return view('livewire.category.rincian');
    }
}
