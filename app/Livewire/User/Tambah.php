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

    public $password;

    public $image;

    public $role;

    public $phone;

    public $gender;

    public $previewImage;

    public $roles;

    public array $pin = ['', '', '', '', '', ''];

    public bool $showPin = true;

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

    public function updatedPassword()
    {
        $this->validate(
            [
                'password' => 'required|string|min:8|alpha_num|regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/', // Minimal 8 karakter, harus mengandung huruf dan angka
            ],
            [
                'password.required' => 'Password harus diisi.',
                'password.min' => 'Password minimal 8 karakter.',
                'password.alpha_num' => 'Password harus terdiri dari huruf dan angka.',
                'password.regex' => 'Password harus mengandung setidaknya satu huruf dan satu angka.',
            ]
        );
    }

    public function createUser()
    {
        // $pinCode = implode('', $this->pin);

        // // Validasi jika perlu
        // if (!ctype_digit($pinCode) || strlen($pinCode) !== 6) {
        //     $this->alert('pin', 'PIN harus terdiri dari 6 digit angka.');
        //     return;
        // }
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'image' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
            'phone' => 'nullable|string|max:15',
            'password' => 'required|string|min:8|alpha_num|regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/', // Minimal 8 karakter, harus mengandung huruf dan angka
            'gender' => 'required',
        ]);

        $user = new \App\Models\User;
        $user->name = $this->name;
        $user->email = $this->email;
        $user->password = bcrypt($this->password);
        $user->phone = $this->phone;
        $user->gender = $this->gender;
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
