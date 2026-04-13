<?php

namespace App\Livewire\User;

use App\Models\SpatieRole;
use App\Models\User;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\Activitylog\Models\Activity;

class Form extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    public ?string $userId = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public mixed $image = null;

    public string $role = '';

    public ?string $phone = null;

    public string $gender = '';

    public string $is_active = '';

    public ?string $previewImage = null;

    public mixed $roles = null;

    public $showHistoryModal = false;

    public $activityLogs = [];

    protected $listeners = [
        'delete' => 'delete',
        'cancelled' => 'cancelled',
    ];

    public function mount(?string $id = null): void
    {
        $this->roles = SpatieRole::all();

        if ($id) {
            $this->userId = $id;
            $user = User::findOrFail($id);
            $this->name = $user->name;
            $this->email = $user->email;
            $this->phone = $user->phone;
            $this->gender = $user->gender ?? '';
            $this->role = $user->getRoleNames()->first() ?? '';
            $this->is_active = $user->is_active ? '1' : '0';
            if ($user->image) {
                $this->previewImage = env('APP_URL').'/storage/'.$user->image;
            }
            View::share('title', 'Rincian Pekerja');
        } else {
            View::share('title', 'Tambah Pekerja');
        }
        View::share('mainTitle', 'Pekerja');
    }

    public function isEditMode(): bool
    {
        return $this->userId !== null;
    }

    public function updatedImage(): void
    {
        $this->validate([
            'image' => 'image|max:2048|mimes:jpg,jpeg,png',
        ]);

        $this->previewImage = $this->image->temporaryUrl();
    }

    public function updatedPassword(): void
    {
        if ($this->password) {
            $this->validate(
                [
                    'password' => 'nullable|string|min:8|alpha_num|regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/',
                ],
                [
                    'password.min' => 'Password minimal 8 karakter.',
                    'password.alpha_num' => 'Password harus terdiri dari huruf dan angka.',
                    'password.regex' => 'Password harus mengandung setidaknya satu huruf dan satu angka.',
                ]
            );
        }
    }

    public function riwayatPembaruan(): void
    {
        $this->activityLogs = Activity::inLog('users')
            ->where('subject_id', $this->userId)
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function save(): mixed
    {
        if ($this->isEditMode()) {
            return $this->updateUser();
        }

        return $this->createUser();
    }

    public function createUser(): mixed
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'image' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
            'phone' => 'nullable|string|max:15',
            'password' => 'required|string|min:8|alpha_num|regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/',
            'gender' => 'required',
            'role' => 'required',
            'is_active' => 'nullable|boolean',
        ], [
            'password.required' => 'Password harus diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.alpha_num' => 'Password harus terdiri dari huruf dan angka.',
            'password.regex' => 'Password harus mengandung setidaknya satu huruf dan satu angka.',
            'name.required' => 'Nama harus diisi.',
            'email.required' => 'Email harus diisi.',
            'email.unique' => 'Email sudah digunakan.',
            'email.email' => 'Format email tidak valid.',
            'gender.required' => 'Jenis kelamin harus dipilih.',
            'role.required' => 'Peran harus dipilih.',
            'is_active.required' => 'Status pekerja harus dipilih.',
        ]);

        // Check if the role has reached its maximum number of users
        $selectedRole = SpatieRole::where('name', $this->role)->first();
        if ($selectedRole && $selectedRole->hasReachedMaxUsers()) {
            $this->addError('role', 'Peran "'.$selectedRole->name.'" sudah mencapai batas maksimum '.$selectedRole->max_users.' pekerja.');

            return null;
        }

        $user = new User;
        $user->name = $this->name;
        $user->email = $this->email;
        $user->password = bcrypt($this->password);
        $user->phone = $this->phone;
        $user->gender = $this->gender;
        $user->is_active = false;
        if ($this->image) {
            $user->image = $this->image->store('user_images', 'public');
        }
        $user->save();

        $user->assignRole($this->role);
        User::find($user->id)->sendInvitation();

        session()->flash('success', 'Pekerja berhasil ditambahkan.');

        return redirect()->route('user');
    }

    public function updateUser(): mixed
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$this->userId,
            'image' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
            'phone' => 'nullable|string|max:15',
            'password' => 'nullable|string|min:8|alpha_num|regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/',
            'gender' => 'required',
            'role' => 'required',
            'is_active' => 'required|boolean',
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
            'gender.required' => 'Jenis kelamin harus dipilih.',
            'role.required' => 'Peran harus dipilih.',
            'is_active.required' => 'Status pekerja harus dipilih.',
        ]);

        $user = User::findOrFail($this->userId);
        $currentRole = $user->getRoleNames()->first();

        // Check if user is changing role and if the new role has reached its maximum number of users
        if ($currentRole !== $this->role) {
            $selectedRole = SpatieRole::where('name', $this->role)->first();
            if ($selectedRole && $selectedRole->hasReachedMaxUsers()) {
                $this->addError('role', 'Peran "'.$selectedRole->name.'" sudah mencapai batas maksimum '.$selectedRole->max_users.' pekerja.');

                return null;
            }
        }

        $user = User::findOrFail($this->userId);
        $user->name = $this->name;
        $user->email = $this->email;
        if ($this->password) {
            $user->password = bcrypt($this->password);
        }
        $user->phone = $this->phone;
        $user->gender = $this->gender;
        $user->is_active = $this->is_active;
        if ($this->image) {
            if ($user->image) {
                $oldImagePath = public_path('storage/'.$user->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            $user->image = $this->image->store('user_images', 'public');
        }
        $user->save();

        $user->syncRoles([$this->role]);

        session()->flash('success', 'Pekerja berhasil diperbarui.');

        return redirect()->route('user');
    }

    public function confirmDelete(): void
    {
        $this->alert('warning', 'Hapus Pekerja?', [
            'text' => 'Apakah Anda yakin ingin menghapus pekerja ini? Data yang dihapus tidak dapat dikembalikan.',
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

    public function delete(): mixed
    {
        $user = User::findOrFail($this->userId);
        if ($user->image) {
            $oldImagePath = public_path('storage/'.$user->image);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }
        $user->delete();

        session()->flash('success', 'Pekerja berhasil dihapus.');

        return redirect()->route('user');
    }

    public function cancelled(): void
    {
        // Do nothing when cancelled
    }

    public function render()
    {
        return view('livewire.user.form');
    }
}