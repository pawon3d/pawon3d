<?php

namespace App\Livewire\Hitung;

use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

class Index extends Component
{
    use LivewireAlert, WithPagination;

    public $search = '';

    public $filterStatus = '';

    public $showHistoryModal = false;

    public $activityLogs = [];

    public $sortField = 'hitung_number';

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
        $this->activityLogs = Activity::inLog('hitungs')
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function mount()
    {
        View::share('mainTitle', 'Inventori');
        View::share('title', 'Hitung dan Catat Persediaan');
        if (session()->has('success')) {
            $this->alert('success', session('success'));
        }
    }

    public function cetakInformasi()
    {
        return redirect()->route('hitung.pdf', [
            'search' => $this->search,
            'status' => 'all',
        ]);
    }

    public function render()
    {
        return view('livewire.hitung.index', [
            'hitungs' => \App\Models\Hitung::with(['details.material', 'user'])
                ->when($this->search, function ($query) {
                    $query->where('hitung_number', 'like', '%'.$this->search.'%')
                        ->orWhere('action', 'like', '%'.$this->search.'%');
                })->where('status', 'Sedang Diproses')
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate(10),
        ]);
    }
}
