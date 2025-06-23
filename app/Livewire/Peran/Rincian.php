<?php

namespace App\Livewire\Peran;

use App\Models\SpatieRole;
use Illuminate\Support\Facades\View;
use Livewire\Component;

class Rincian extends Component
{
    use \Jantinnerezo\LivewireAlert\LivewireAlert;
    public $roleId;
    public $roleName;
    public $permissions = [];
    public $users;

    protected $listeners = [
        'delete',
    ];
    public function mount($id)
    {
        View::share('mainTitle', 'Pekerja');
        View::share('title', 'Rincian Peran');
        $role = SpatieRole::findOrFail($id);

        $this->roleId = $role->id;
        $this->roleName = $role->name;
        $this->permissions = $role->permissions->pluck('name')->toArray();
        $this->users = $role->users;
    }

    public function updateRole()
    {
        $this->validate([
            'roleName' => 'required|unique:roles,name,' . $this->roleId,
        ]);

        $role = SpatieRole::findOrFail($this->roleId);
        $role->update(['name' => $this->roleName]);
        $role->syncPermissions($this->permissions);

        session()->flash('success', 'Peran berhasil diperbarui!');
        return redirect()->route('role');
    }

    public function deleteRole()
    {
        $this->alert('warning', 'Apakah Anda yakin ingin menghapus peran ini?', [
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
        $role = SpatieRole::find($this->roleId);

        if ($role) {
            $role->delete();

            return redirect()->intended(route('role'))->with('success', 'Peran berhasil dihapus.');
        }
    }
    public function render()
    {
        return view('livewire.peran.rincian');
    }
}
