<?php

namespace App\Livewire\Supplier;

use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\Activitylog\Models\Activity;


class Rincian extends Component
{
    use WithFileUploads, LivewireAlert;
    public $name, $description, $contact_name, $phone, $image, $id;
    public $previewImage;
    public $activityLogs = [];
    public $showHistoryModal = false;

    protected $listeners = [
        'updatedImage' => 'updatedImage',
        'removeImage' => 'removeImage',
        'delete'
    ];

    public function mount($id)
    {
        View::share('title', 'Rincian Toko Persediaan');
        $this->id = $id;
        $supplier = \App\Models\Supplier::find($this->id);
        $this->name = $supplier->name;
        $this->description = $supplier->description;
        $this->contact_name = $supplier->contact_name;
        $this->phone = $supplier->phone;
        if ($supplier->image) {
            $this->previewImage = env('APP_URL') . '/storage/' . $supplier->image;
        } else {
            $this->previewImage = null;
        }
    }

    public function riwayatPembaruan()
    {
        $this->activityLogs = Activity::inLog('suppliers')->where('subject_id', $this->id)
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
        $this->validate([
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
            'contact_name' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'image' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
        ]);

        $supplier = \App\Models\Supplier::find($this->id);

        $supplier->update([
            'name' => $this->name,
            'description' => $this->description,
            'contact_name' => $this->contact_name,
            'phone' => $this->phone,
        ]);

        if ($this->image) {
            if ($supplier->image) {
                $oldImagePath = public_path('storage/' . $supplier->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
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
        $supplier = \App\Models\Supplier::find($this->id);

        if ($supplier) {
            $supplier->delete();
            if ($supplier->image) {
                $oldImagePath = public_path('storage/' . $supplier->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            return redirect()->intended(route('supplier'))->with('success', 'Toko Persediaan berhasil dihapus.');
        }
    }
    public function render()
    {
        return view('livewire.supplier.rincian');
    }
}
