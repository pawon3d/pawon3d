<?php

namespace App\Livewire\Supplier;

use Illuminate\Support\Facades\View;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

class Index extends Component
{
    use WithPagination, LivewireAlert;

    public $search = '';
    public $showHistoryModal = false;
    public $activityLogs = [];
    public $filterStatus = '';

    public function riwayatPembaruan()
    {
        $this->activityLogs = Activity::inLog('suppliers')
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function mount()
    {
        View::share('title', 'Toko Persediaan');
        View::share('mainTitle', 'Inventori');
        if (session()->has('success')) {
            $this->alert('success', session('success'));
        }
    }

    public function cetakInformasi()
    {
        return redirect()->route('supplier.pdf', [
            'search' => $this->search,
        ]);
    }
    public function render()
    {
        return view('livewire.supplier.index', [
            'suppliers' => \App\Models\Supplier::when($this->search, function ($query) {
                return $query->where('name', 'like', '%' . $this->search . '%');
            })->paginate(10),
        ]);
    }
}
