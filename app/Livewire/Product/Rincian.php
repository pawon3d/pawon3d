<?php

namespace App\Livewire\Product;

use App\Models\Product;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\View;
use Livewire\WithFileUploads;

class Rincian extends Component
{
    use \Jantinnerezo\LivewireAlert\LivewireAlert, WithFileUploads;

    public $name, $product_image;
    public $product_id;
    public $previewImage = null;
    public $showHistoryModal = false;
    public $activityLogs = [];

    protected $listeners = [
        'delete',
        'updatePreview',
    ];

    protected $messages = [
        'product_image.image' => 'File yang diunggah harus berupa gambar.',
        'product_image.max' => 'Ukuran gambar tidak boleh lebih dari 2 MB.',
        'product_image.mimes' => 'Format gambar yang diizinkan adalah jpg, jpeg, png.',
    ];

    public function mount($id)
    {
        View::share('title', 'Rincian Produk');
        $this->product_id = $id;
        $product = Product::find($this->product_id);
        $this->name = $product->name;
        if ($product->product_image) {
            $this->previewImage = env('APP_URL') . '/storage/' . $product->product_image;
        } else {
            $this->previewImage = null;
        }
    }

    public function riwayatPembaruan()
    {
        $this->activityLogs = Activity::inLog('products')->where('subject_id', $this->product_id)
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function updatedProductImage()
    {
        $this->validate([
            'product_image' => 'image|max:2048|mimes:jpg,jpeg,png',
        ]);

        // Untuk preview langsung setelah upload
        $this->previewImage = $this->product_image->temporaryUrl();
    }

    public function removeImage()
    {
        $this->reset('product_image', 'previewImage');
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|min:3',
            'product_image' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
        ]);

        $product = Product::find($this->product_id);
        $product->update([
            'name' => $this->name,
        ]);

        if ($this->product_image) {
            $product->product_image = $this->product_image->store('product_images', 'public');
            $product->save();
        }

        return redirect()->intended(route('produk'))->with('success', 'Produk berhasil diperbarui.');
    }
    public function confirmDelete()
    {
        $this->alert('warning', 'Apakah Anda yakin ingin menghapus produk ini?', [
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
        $product = Product::find($this->product_id);

        if ($product) {
            $product->delete();
            $this->alert('success', 'Produk berhasil dihapus!');
            return redirect()->intended(route('produk'));
        }
    }


    public function render()
    {
        return view('livewire.product.rincian');
    }
}
