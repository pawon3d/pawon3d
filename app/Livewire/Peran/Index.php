<?php

namespace App\Livewire\Peran;

use App\Models\SpatieRole as Role;
use Illuminate\Support\Facades\View;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

class Index extends Component
{
    use \Jantinnerezo\LivewireAlert\LivewireAlert,
        \Livewire\WithPagination;

    public $search = '';

    public $showHistoryModal = false;

    public $activityLogs = [];

    public $filterStatus = '';

    public $sortField = 'name';

    public $sortDirection = 'desc';

    protected $queryString = ['search', 'sortField', 'sortDirection'];

    /**
     * Mapping dari prefix permission ke label kategori yang readable.
     */
    protected array $categoryLabels = [
        'kasir' => 'Kasir',
        'produksi' => 'Produksi (Koki)',
        'inventori' => 'Inventori (Inventaris)',
        'manajemen' => 'Manajemen',
    ];

    /**
     * Mengkonversi array permission names ke string kategori yang readable.
     */
    public function getReadableAccessLabels(Role $role): string
    {
        $permissionNames = $role->permissions->pluck('name')->toArray();

        if (empty($permissionNames)) {
            return 'Tidak ada akses';
        }

        $activeCategories = [];

        foreach ($this->categoryLabels as $prefix => $label) {
            foreach ($permissionNames as $permission) {
                if (str_starts_with($permission, $prefix.'.')) {
                    $activeCategories[$prefix] = $label;
                    break;
                }
            }
        }

        if (empty($activeCategories)) {
            return 'Tidak ada akses';
        }

        return implode(', ', $activeCategories);
    }

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
        View::share('mainTitle', 'Pekerja');
        if (session()->has('success')) {
            $this->alert('success', session('success'));
        }
    }

    public function render()
    {
        return view('livewire.peran.index', [
            'roles' => Role::when($this->search, function ($query) {
                return $query->where('name', 'like', '%'.$this->search.'%');
            })->with('permissions', 'users')->orderBy('name')->withCount('users')->withCount('permissions')
                ->orderBy(
                    $this->sortField,
                    $this->sortDirection
                )
                ->paginate(10),
        ]);
    }
}
