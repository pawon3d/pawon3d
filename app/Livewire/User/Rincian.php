<?php

namespace App\Livewire\User;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

class Rincian extends Component
{
    use \Livewire\WithFileUploads, \Jantinnerezo\LivewireAlert\LivewireAlert;

    public $name, $email, $password, $image, $role, $phone;
    public $previewImage;
    public $roles;
    public $userId;
    public $showHistoryModal = false;
    public $activityLogs = [];

    protected $listeners = [
        'delete' => 'delete',
        'cancelled' => 'cancelled',
    ];

    public function mount($id)
    {
        View::share('title', 'Rincian Pekerja');
        $this->roles = \App\Models\SpatieRole::all();
        $this->userId = $id;
        $user = \App\Models\User::findOrFail($id);
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->role = $user->getRoleNames()->first();
        if ($user->image) {
            $this->previewImage = env('APP_URL') . '/storage/' . $user->image;
        } else {
            $this->previewImage = null;
        }
    }

    public function updatedImage()
    {
        $this->validate([
            'image' => 'image|max:2048|mimes:jpg,jpeg,png',
        ]);

        // Untuk preview langsung setelah upload
        $this->previewImage = $this->image->temporaryUrl();
    }

    public function riwayatPembaruan()
    {
        $this->activityLogs = Activity::inLog('users')->where('subject_id', $this->userId)
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function updateUser()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'password' => 'nullable|string|min:4',
            'image' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
            'phone' => 'nullable|string|max:15',
        ]);

        $user = \App\Models\User::findOrFail($this->userId);
        $user->name = $this->name;
        $user->email = $this->email;
        if ($this->password) {
            $user->password = bcrypt($this->password);
        }
        $user->phone = $this->phone;
        if ($this->image) {
            // Hapus gambar lama jika ada
            if ($user->image) {
                $oldImagePath = public_path('storage/' . $user->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            $user->image = $this->image->store('user_images', 'public');
        }
        $user->save();

        // Update role
        $user->syncRoles([$this->role]);

        session()->flash('success', 'Pekerja berhasil diperbarui.');
        return redirect()->route('user');
    }

    public function confirmDelete()
    {
        $this->alert('warning', 'Apakah Anda yakin ingin menghapus pekerja ini?', [
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
        $user = \App\Models\User::findOrFail($this->userId);
        if ($user->image) {
            $oldImagePath = public_path('storage/' . $user->image);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }
        $user->delete();

        session()->flash('success', 'Pekerja berhasil dihapus.');
        return redirect()->route('user');
    }
    public function render()
    {
        return view('livewire.user.rincian');
    }
}