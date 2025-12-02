<?php

namespace App\Livewire\User;

use App\Models\User;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

class Index extends Component
{
    use LivewireAlert;
    use WithPagination;

    public $search = '';

    public $showHistoryModal = false;

    public $activityLogs = [];

    public $filterStatus = '';

    public $sortField = 'name';

    public $sortDirection = 'desc';

    protected $queryString = ['search', 'sortField', 'sortDirection'];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
        $this->resetPage();
    }

    public function riwayatPembaruan()
    {
        $this->activityLogs = Activity::inLog('users')
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function cetakInformasi()
    {
        return redirect()->route('user.pdf', [
            'search' => $this->search,
        ]);
    }

    public function mount()
    {
        View::share('title', 'Pekerja');
        View::share('mainTitle', 'Pekerja');
        if (session()->has('success')) {
            $this->alert('success', session('success'));
        }
    }

    public function render()
    {
        $users = User::query()
            ->leftJoin('model_has_roles', function ($join) {
                $join->on('users.id', '=', 'model_has_roles.model_id')
                    ->where('model_has_roles.model_type', '=', \App\Models\User::class);
            })
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('users.*', 'roles.name as role_name')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('users.name', 'like', '%'.$this->search.'%')
                        ->orWhere('users.email', 'like', '%'.$this->search.'%')
                        ->orWhere('users.phone', 'like', '%'.$this->search.'%')
                        ->orWhere('roles.name', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy(
                $this->sortField === 'role_name' ? 'roles.name' : 'users.'.$this->sortField,
                $this->sortDirection
            )->distinct()
            ->paginate(10);

        return view('livewire.user.index', compact('users'));
    }
}
