<?php

namespace App\Livewire\Supplier;

use Illuminate\Support\Facades\View;
use Livewire\Component;
use Livewire\WithFileUploads;

class Tambah extends Component
{
    use WithFileUploads;
    public $name, $description, $contact_name, $phone, $image;
    public $previewImage;

    protected $listeners = [
        'updatedImage' => 'updatedImage',
        'removeImage' => 'removeImage',
    ];

    public function mount()
    {
        View::share('title', 'Tambah Toko Persediaan');
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
        $this->validate([
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
            'contact_name' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'image' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
        ]);

        $supplier = \App\Models\Supplier::create([
            'name' => $this->name,
            'description' => $this->description,
            'contact_name' => $this->contact_name,
            'phone' => $this->phone,
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
