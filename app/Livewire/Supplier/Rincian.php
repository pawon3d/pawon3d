<?php

namespace App\Livewire\Supplier;

use App\Models\Supplier;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\Activitylog\Models\Activity;

class Rincian extends Component
{
    use LivewireAlert, WithFileUploads;

    public $id;

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

    public function mount($id)
    {
        View::share('title', 'Rincian Toko Persediaan');
        View::share('mainTitle', 'Inventori');
        $this->id = $id;
        $supplier = Supplier::findOrFail($this->id);
        $this->name = $supplier->name;
        $this->description = $supplier->description;
        $this->contact_name = $supplier->contact_name;
        $this->phone = $supplier->phone;
        $this->street = $supplier->street;
        $this->landmark = $supplier->landmark;
        $this->maps_link = $supplier->maps_link;

        if ($supplier->image) {
            $this->previewImage = Storage::url($supplier->image);
        } else {
            $this->previewImage = null;
        }
    }

    public function riwayatPembaruan()
    {
        $this->activityLogs = Activity::inLog('suppliers')
            ->where('subject_id', $this->id)
            ->with('causer:id,name')
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
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

    public function update()
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

        $supplier = Supplier::findOrFail($this->id);

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

        return redirect()->intended(route('supplier'))->with('success', 'Toko Persediaan berhasil diperbarui.');
    }

    public function confirmDelete()
    {
        $this->alert('warning', 'Apakah Anda yakin ingin menghapus toko persediaan ini?', [
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
        $supplier = Supplier::findOrFail($this->id);

        if ($supplier->image) {
            Storage::disk('public')->delete($supplier->image);
        }

        $supplier->delete();

        return redirect()->intended(route('supplier'))->with('success', 'Toko Persediaan berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.supplier.rincian');
    }
}
