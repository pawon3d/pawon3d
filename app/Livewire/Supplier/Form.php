<?php

namespace App\Livewire\Supplier;

use App\Models\Supplier;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\Activitylog\Models\Activity;

class Form extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    public $supplierId;

    public $name;

    public $description;

    public $contact_name;

    public $phone;

    public $image;

    public $previewImage;

    public $street;

    public $landmark;

    public $maps_link;

    public $activityLogs = [];

    public $showHistoryModal = false;

    protected $listeners = [
        'updatedImage' => 'updatedImage',
        'removeImage' => 'removeImage',
        'delete',
    ];

    public function mount($id = null)
    {
        if ($id) {
            $this->supplierId = $id;
            $supplier = Supplier::findOrFail($this->supplierId);
            $this->name = $supplier->name;
            $this->description = $supplier->description;
            $this->contact_name = $supplier->contact_name;
            $this->phone = $supplier->phone;
            $this->street = $supplier->street;
            $this->landmark = $supplier->landmark;
            $this->maps_link = $supplier->maps_link;
            $this->previewImage = $supplier->image ? Storage::url($supplier->image) : null;
            View::share('title', 'Rincian Toko Persediaan');
        } else {
            View::share('title', 'Tambah Toko Persediaan');
        }

        View::share('mainTitle', 'Inventori');
    }

    public function isEditMode(): bool
    {
        return $this->supplierId !== null;
    }

    public function riwayatPembaruan(): void
    {
        if (! $this->isEditMode()) {
            return;
        }

        $this->activityLogs = Activity::inLog('suppliers')
            ->where('subject_id', $this->supplierId)
            ->with('causer:id,name')
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function updatedImage(): void
    {
        $this->validate([
            'image' => 'image|max:2048|mimes:jpg,jpeg,png',
        ]);

        $this->previewImage = $this->image?->temporaryUrl();
    }

    public function removeImage(): void
    {
        $this->reset('image', 'previewImage');
    }

    public function save()
    {
        if ($this->isEditMode()) {
            return $this->updateSupplier();
        }

        return $this->createSupplier();
    }

    public function createSupplier()
    {
        $this->validate([
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

        session()->flash('success', 'Toko Persediaan berhasil ditambahkan.');

        return redirect()->route('supplier');
    }

    public function updateSupplier()
    {
        $this->validate([
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

        $supplier = Supplier::findOrFail($this->supplierId);

        $supplier->update([
            'name' => $this->name,
            'description' => $this->description,
            'contact_name' => $this->contact_name,
            'phone' => $this->phone,
            'street' => $this->street,
            'landmark' => $this->landmark,
            'maps_link' => $this->maps_link,
        ]);

        if ($this->image) {
            if ($supplier->image) {
                Storage::disk('public')->delete($supplier->image);
            }

            $supplier->image = $this->image->store('supplier_images', 'public');
            $supplier->save();
        }

        session()->flash('success', 'Toko Persediaan berhasil diperbarui.');

        return redirect()->route('supplier');
    }

    public function confirmDelete(): void
    {
        $this->alert('warning', 'Hapus Toko?', [
            'text' => 'Apakah Anda yakin ingin menghapus toko persediaan ini? Data yang dihapus tidak dapat dikembalikan.',
            'showConfirmButton' => true,
            'showCancelButton' => true,
            'confirmButtonText' => 'Ya, Hapus',
            'cancelButtonText' => 'Batal',
            'onConfirmed' => 'delete',
            'onCancelled' => 'cancelled',
            'confirmButtonColor' => '#ef4444',
            'cancelButtonColor' => '#6b7280',
            'width' => '400',
            'padding' => '1.5rem',
            'toast' => false,
            'position' => 'center',
            'timer' => null,
        ]);
    }

    public function delete()
    {
        $supplier = Supplier::findOrFail($this->supplierId);

        if ($supplier->image) {
            Storage::disk('public')->delete($supplier->image);
        }

        $supplier->delete();

        session()->flash('success', 'Toko Persediaan berhasil dihapus.');

        return redirect()->route('supplier');
    }

    public function cancelled(): void
    {
        // Do nothing on cancel
    }

    public function render()
    {
        return view('livewire.supplier.form');
    }
}
