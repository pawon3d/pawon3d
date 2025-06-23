<?php

namespace App\Livewire\Peran;

use Illuminate\Support\Facades\View;
use Livewire\Component;
use App\Models\SpatieRole;

class Tambah extends Component
{
    public $roleName;
    public $permissions = [];

    public function mount()
    {
        View::share('title', 'Tambah Peran');
        View::share('mainTitle', 'Pekerja');
    }

    public function createRole()
    {
        $this->validate([
            'roleName' => 'required|unique:roles,name',
        ]);

        $role = SpatieRole::create(['name' => $this->roleName]);
        $role->syncPermissions($this->permissions);

        session()->flash('success', 'Peran berhasil dibuat!');
        return redirect()->route('role');
    }
    public function render()
    {
        return view('livewire.peran.tambah');
    }
}
