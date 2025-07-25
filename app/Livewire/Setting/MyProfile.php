<?php

namespace App\Livewire\Setting;

use Illuminate\Support\Facades\View;
use Livewire\Component;

class MyProfile extends Component
{
    use \Livewire\WithFileUploads, \Jantinnerezo\LivewireAlert\LivewireAlert;

    public $name, $email, $password, $image, $role, $phone, $gender;
    public $previewImage;
    public $roles;
    public $userId;
    public $showHistoryModal = false;
    public $activityLogs = [];
    public array $pin = ['', '', '', '', '', ''];
    public bool $showPin = true;

    protected $listeners = [
        'delete' => 'delete',
        'cancelled' => 'cancelled',
    ];

    public function mount($id)
    {
        View::share('title', 'Rincian Pekerja');
        View::share('mainTitle', 'Pengaturan');
        $this->roles = \App\Models\SpatieRole::all();
        $this->userId = $id;
        $user = \App\Models\User::findOrFail($id);
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->gender = $user->gender;
        $this->password = $user->password; // Ambil password yang sudah ada, jika perlu
        // sensor sebagian password
        $this->password = str_repeat('*', strlen($user->password) - 4) . substr($user->password, -4);
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

    public function updatedPassword()
    {
        $this->validate(
            [
                'password' => 'nullable|string|min:8|alpha_num|regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/', // Minimal 8 karakter, harus mengandung huruf dan angka
            ],
            [
                'password.min' => 'Password minimal 8 karakter.',
                'password.alpha_num' => 'Password harus terdiri dari huruf dan angka.',
                'password.regex' => 'Password harus mengandung setidaknya satu huruf dan satu angka.',
            ]
        );
    }

    public function updateUser()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'image' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
            'phone' => 'nullable|string|max:15',
            'password' => 'nullable|string|min:8|alpha_num|regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/', // Minimal 8 karakter, harus mengandung huruf dan angka
        ], [
            'password.min' => 'Password minimal 8 karakter.',
            'password.alpha_num' => 'Password harus terdiri dari huruf dan angka.',
            'password.regex' => 'Password harus mengandung setidaknya satu huruf dan satu angka.',
            'email.unique' => 'Email sudah digunakan oleh pengguna lain.',
            'image.image' => 'File harus berupa gambar.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
            'image.mimes' => 'Format gambar harus jpg, jpeg, atau png.',
            'phone.max' => 'Nomor telepon maksimal 15 karakter.',
            'name.required' => 'Nama harus diisi.',
            'email.required' => 'Email harus diisi.',
        ]);

        // $pinCode = implode('', $this->pin);

        $user = \App\Models\User::findOrFail($this->userId);
        $user->name = $this->name;
        $user->email = $this->email;
        // if ($pinCode) {
        //     $user->password = bcrypt($pinCode);
        // }
        if ($this->password && $this->password !== str_repeat('*', strlen($user->password) - 4) . substr($user->password, -4)) {
            $user->password = bcrypt($this->password);
        }
        $user->phone = $this->phone;
        $user->gender = $this->gender;
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
    public function render()
    {
        return view('livewire.setting.my-profile');
    }
}
