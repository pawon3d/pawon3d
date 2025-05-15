<?php

namespace App\Livewire\Product;

use App\Models\Product;
use Illuminate\Support\Facades\View;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class Tambah extends Component
{
    use WithFileUploads;

    public $product_image, $name;
    public $previewImage;

    protected $listeners = [
        'updatePreview',
    ];

    protected $messages = [
        'product_image.image' => 'File yang diunggah harus berupa gambar.',
        'product_image.max' => 'Ukuran gambar tidak boleh lebih dari 2 MB.',
        'product_image.mimes' => 'Format gambar yang diizinkan adalah jpg, jpeg, png.',
    ];

    public function mount()
    {
        View::share('title', 'Tambah Produk');
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

    public function store()
    {
        $product = Product::create([
            'name' => $this->name,
            'category_id' => '334f6dc6-2d4b-4b04-9596-71180c9cd967',
            'price' => 0,
            'stock' => 0,
            'is_ready' => true,
        ]);

        if ($this->product_image) {
            $product->product_image = $this->product_image->store('product_images', 'public');
            $product->save();
        }

        $this->resetForm();
        return redirect()->intended(route('produk'))->with('success', 'Produk berhasil ditambahkan.');
    }

    public function resetForm()
    {
        $this->reset([
            'name',
            'product_image',
            'previewImage',
        ]);
    }

    public function render()
    {
        return view('livewire.product.tambah');
    }
}
