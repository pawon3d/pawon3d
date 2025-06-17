<?php

namespace App\Livewire\User;

use App\Models\SpatieRole;
use Illuminate\Support\Facades\View;
use Livewire\Component;

class Tambah extends Component
{
    use \Livewire\WithFileUploads, \Jantinnerezo\LivewireAlert\LivewireAlert;

    public $name, $email, $password, $image, $role, $phone;
    public $previewImage;
    public $roles;
    public array $pin = ['', '', '', '', '', ''];
    public bool $showPin = true;
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
        $pinCode = implode('', $this->pin);

        // Validasi jika perlu
        if (!ctype_digit($pinCode) || strlen($pinCode) !== 6) {
            $this->alert('pin', 'PIN harus terdiri dari 6 digit angka.');
            return;
        }
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'image' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
            'phone' => 'nullable|string|max:15',
        ]);

        $user = new \App\Models\User();
        $user->name = $this->name;
        $user->email = $this->email;
        $user->password = bcrypt($pinCode);
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