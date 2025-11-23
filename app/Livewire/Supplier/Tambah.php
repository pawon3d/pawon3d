<?php

namespace App\Livewire\Supplier;

use App\Models\Supplier;
use Illuminate\Support\Facades\View;
use Livewire\Component;
use Livewire\WithFileUploads;

class Tambah extends Component
{
    use WithFileUploads;

    public $name;

    public $description;

    public $contact_name;

    public $phone;

    public $image;

    public $previewImage;

    public $street;

    public $landmark;

    public $maps_link;

    protected $listeners = [
        'updatedImage' => 'updatedImage',
        'removeImage' => 'removeImage',
    ];

    public function mount()
    {
        View::share('title', 'Tambah Toko Persediaan');
        View::share('mainTitle', 'Inventori');
    }

    public function updatedImage()
    {
        $this->validate([
            'image' => 'image|max:2048|mimes:jpg,jpeg,png',
        ]);
        $this->previewImage = $this->image->temporaryUrl();
    }

    public function removeImage()
    {
        $this->reset('image', 'previewImage');
    }

    public function store()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
            'contact_name' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'street' => 'nullable|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'maps_link' => 'nullable|url|max:500',
            'image' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
        ], [
            'name.required' => 'Nama toko wajib diisi.',
            'name.max' => 'Nama toko maksimal 50 karakter.',
            'phone.max' => 'Nomor telepon maksimal 20 karakter.',
            'maps_link.url' => 'Link Google Maps harus berupa URL yang valid.',
            'image.image' => 'File harus berupa gambar.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $supplier = Supplier::create([
            'name' => $this->name,
            'description' => $this->description,
            'contact_name' => $this->contact_name,
            'phone' => $this->phone,
            'street' => $this->street,
            'landmark' => $this->landmark,
            'maps_link' => $this->maps_link,
        ]);

        if ($this->image) {
            $supplier->image = $this->image->store('supplier_images', 'public');
            $supplier->save();
        }

        return redirect()->intended(route('supplier'))->with('success', 'Toko Persediaan berhasil ditambahkan.');
    }

    public function render()
    {
        return view('livewire.supplier.tambah');
    }
}
