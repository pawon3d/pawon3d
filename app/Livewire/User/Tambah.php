<?php

namespace App\Livewire\User;

use App\Models\SpatieRole;
use Illuminate\Support\Facades\View;
use Livewire\Component;

class Tambah extends Component
{
    use \Livewire\WithFileUploads;

    public $name, $email, $password, $image, $role, $phone;
    public $previewImage;
    public $roles;

    public function mount()
    {
        View::share('title', 'Tambah Pekerja');
        $this->roles = SpatieRole::all();
    }

    public function updatedImage()
    {
        $this->validate([
            'image' => 'image|max:2048|mimes:jpg,jpeg,png',
        ]);

        // Untuk preview langsung setelah upload
        $this->previewImage = $this->image->temporaryUrl();
    }

    public function createUser()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:4',
            'image' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
            'phone' => 'nullable|string|max:15',
        ]);

        $user = new \App\Models\User();
        $user->name = $this->name;
        $user->email = $this->email;
        $user->password = bcrypt($this->password);
        $user->phone = $this->phone;
        if ($this->image) {
            $user->image = $this->image->store('user_images', 'public');
        }
        $user->save();

        // Assign role
        $user->assignRole($this->role);

        session()->flash('success', 'Pekerja berhasil ditambahkan.');
        return redirect()->route('user');
    }

    public function render()
    {
        return view('livewire.user.tambah');
    }
}