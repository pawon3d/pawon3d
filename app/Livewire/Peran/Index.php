<?php

namespace App\Livewire\Peran;

use Illuminate\Support\Facades\View;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;
use App\Models\SpatieRole as Role;

class Index extends Component
{
    use \Livewire\WithPagination,
        \Jantinnerezo\LivewireAlert\LivewireAlert;

    public $search = '';
    public $showHistoryModal = false;
    public $activityLogs = [];
    public $filterStatus = '';
    public $sortField = 'name';
    public $sortDirection = 'desc';

    protected $queryString = ['search', 'sortField', 'sortDirection'];

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function riwayatPembaruan()
    {
        $this->activityLogs = Activity::inLog('roles')
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function cetakInformasi()
    {
        return redirect()->route('role.pdf', [
            'search' => $this->search,
        ]);
    }

    public function mount()
    {
        View::share('title', 'Peran');
        if (session()->has('success')) {
            $this->alert('success', session('success'));
        }
    }
    public function render()
    {
        return view('livewire.peran.index', [
            'roles' => Role::when($this->search, function ($query) {
                return $query->where('name', 'like', '%' . $this->search . '%');
            })->with('permissions', 'users')->orderBy('name')->withCount('users')->withCount('permissions')
             ->orderBy(
        $this->sortField,
        $this->sortDirection
    )
            ->paginate(10)
        ]);
    }
}
