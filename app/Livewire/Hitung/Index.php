<?php

namespace App\Livewire\Hitung;

use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

class Index extends Component
{
    use WithPagination, LivewireAlert;

    public $search = '';
    public $filterStatus = '';
    public $showHistoryModal = false;
    public $activityLogs = [];

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
            'hitungs' => \App\Models\Hitung::with(['details'])
                ->when($this->search, function ($query) {
                    $query->where('hitung_number', 'like', '%' . $this->search . '%');
                })
                ->orderBy('hitung_number', 'desc')
                ->paginate(10)
        ]);
    }
}
