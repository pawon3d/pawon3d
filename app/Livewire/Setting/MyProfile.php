<?php

namespace App\Livewire\Setting;

use Illuminate\Support\Facades\View;
use Livewire\Component;

class MyProfile extends Component
{
    use \Jantinnerezo\LivewireAlert\LivewireAlert, \Livewire\WithFileUploads;

    public ?string $name = null;

    public ?string $email = null;

    public ?string $password = null;

    public mixed $image = null;

    public ?string $role = null;

    public ?string $phone = null;

    public ?string $gender = null;

    public ?string $previewImage = null;

    public mixed $roles = null;

    public ?string $userId = null;

    public bool $showHistoryModal = false;

    public array $activityLogs = [];

    public array $pin = ['', '', '', '', '', ''];

    public bool $showPin = true;

    protected $listeners = [
        'delete' => 'delete',
        'cancelled' => 'cancelled',
    ];

    public function mount(string $id): void
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
        // $this->password = $user->password;
        // $this->password = str_repeat('*', strlen($user->password) - 4) . substr($user->password, -4);
        $this->role = $user->getRoleNames()->first();
        if ($user->image) {
            $this->previewImage = env('APP_URL').'/storage/'.$user->image;
        } else {
            $this->previewImage = null;
        }
    }

    public function updatedImage(): void
    {
        $this->validate([
            'image' => 'image|max:2048|mimes:jpg,jpeg,png',
        ]);

        // Untuk preview langsung setelah upload
        $this->previewImage = $this->image->temporaryUrl();
    }

    public function updatedPassword(): void
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

    protected function canEditProfile(): bool
    {
        return auth()->user()->hasRole(['Admin', 'Pemilik', 'Manajemen Sistem']);
    }

    public function updateUser(): mixed
    {
        $user = \App\Models\User::findOrFail($this->userId);

        if ($this->canEditProfile()) {
            $this->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,'.$this->userId,
                'image' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
                'phone' => 'nullable|string|max:15',
                'password' => 'nullable|string|min:8|alpha_num|regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/',
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

            $user->name = $this->name;
            $user->phone = $this->phone;
            $user->gender = $this->gender;

            if ($this->image) {
                if ($user->image) {
                    $oldImagePath = public_path('storage/'.$user->image);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                $user->image = $this->image->store('user_images', 'public');
            }
        } else {
            // Non-admin/supervisor: validasi dan simpan hanya password
            $this->validate([
                'password' => 'required|string|min:8|alpha_num|regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/',
            ], [
                'password.required' => 'Password harus diisi.',
                'password.min' => 'Password minimal 8 karakter.',
                'password.alpha_num' => 'Password harus terdiri dari huruf dan angka.',
                'password.regex' => 'Password harus mengandung setidaknya satu huruf dan satu angka.',
            ]);
        }

        if ($this->password && $this->password !== str_repeat('*', strlen($user->password) - 4).substr($user->password, -4)) {
            $user->password = bcrypt($this->password);
        }

        $user->save();

        // Update role hanya boleh dilakukan admin/supervisor
        if ($this->canEditProfile()) {
            $user->syncRoles([$this->role]);
        }

        session()->flash('success', 'Profil berhasil diperbarui.');

        return redirect()->route('pengaturan');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.setting.my-profile');
    }
}