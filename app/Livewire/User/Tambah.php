<?php

namespace App\Livewire\User;

use App\Models\SpatieRole;
use Illuminate\Support\Facades\View;
use Livewire\Component;

class Tambah extends Component
{
    use \Jantinnerezo\LivewireAlert\LivewireAlert, \Livewire\WithFileUploads;

    public $name;

    public $email;

    public $image;

    public $role;

    public $phone;

    public $gender;

    public $previewImage;

    public $roles;

    public function mount()
    {
        View::share('title', 'Tambah Pekerja');
        View::share('mainTitle', 'Pekerja');
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
            'image' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
            'phone' => 'nullable|string|max:15',
            'gender' => 'required',
            'role' => 'required|exists:roles,name',
        ], [
            'name.required' => 'Nama harus diisi.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'gender.required' => 'Jenis kelamin harus dipilih.',
            'role.required' => 'Peran harus dipilih.',
        ]);

        $user = new \App\Models\User;
        $user->name = $this->name;
        $user->email = $this->email;
        $user->phone = $this->phone;
        $user->gender = $this->gender;
        $user->password = bcrypt(\Illuminate\Support\Str::random(32)); // Temporary password
        $user->is_active = false;

        if ($this->image) {
            $user->image = $this->image->store('user_images', 'public');
        }

        $user->save();

        // Assign role
        $user->assignRole($this->role);

        // Kirim email invitation
        $user->sendInvitation();

        session()->flash('success', 'Pekerja berhasil ditambahkan. Email undangan telah dikirim ke '.$this->email);

        return redirect()->route('user');
    }

    public function render()
    {
        return view('livewire.user.tambah');
    }
}
