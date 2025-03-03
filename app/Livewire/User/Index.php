<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Index extends Component
{
    use WithPagination, LivewireAlert;

    public $name, $username, $password, $role, $user_id;
    public $editId = null;
    public $search = '';
    public $showModal = false;
    public $showEditModal = false;
    protected $listeners = [
        'delete'
    ];

    protected $rules = [
        'name' => 'required|min:3',
        'username' => 'required|unique:users,username',
        'password' => 'required|min:6',
        'role' => 'required|in:produksi,kasir,pemilik'
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function mount()
    {
        View::share('title', 'Pengguna');
    }


    public function render()
    {
        $users = User::when($this->search, function ($query) {
            return $query->where('name', 'like', '%' . $this->search . '%');
        })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.user.index', compact('users'));
    }

    public function openAddModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(User $user)
    {
        $this->editId = $user->id;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->role = $user->role;
        $this->password = '';
        $this->showEditModal = true;
    }

    public function store()
    {
        $this->validate();

        User::create([
            'name' => $this->name,
            'username' => $this->username,
            'password' => Hash::make($this->password),
            'role' => $this->role
        ]);

        $this->showModal = false;
        $this->alert('success', 'Pengguna berhasil ditambahkan!');
        $this->resetForm();
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|min:3',
            'username' => 'required|unique:users,username,' . $this->editId,
            'password' => 'nullable|min:6',
            'role' => 'required|in:produksi,kasir,pemilik'
        ]);

        $user = User::find($this->editId);
        $data = [
            'name' => $this->name,
            'username' => $this->username,
            'role' => $this->role
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        $user->update($data);

        $this->showEditModal = false;
        $this->alert('success', 'Pengguna berhasil diupdate!');
        $this->resetForm();
    }

    public function confirmDelete(User $user)
    {
        // Jika role pemilik, block
        if ($user->role === 'pemilik') {
            $this->alert('error', 'Pemilik tidak bisa dihapus!');
            return;
        }

        // Simpan ID user ke dalam properti
        $this->user_id = $user->id;

        // Konfirmasi menggunakan Livewire Alert
        $this->alert('warning', 'Apakah Anda yakin ingin menghapus pengguna ini?', [
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

        $user = User::find($this->user_id);

        if ($user) {
            $user->delete();
            $this->alert('success', 'Pengguna berhasil dihapus!');
        } else {
            $this->alert('error', 'Pengguna tidak ditemukan!');
        }

        // Reset ID user setelah dihapus
        $this->reset('user_id');
    }

    private function resetForm()
    {
        $this->reset(['name', 'username', 'password', 'role', 'editId']);
        $this->resetErrorBag();
    }
}