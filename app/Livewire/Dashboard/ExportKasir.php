<?php

namespace App\Livewire\Dashboard;

use App\Models\User;
use Illuminate\Support\Facades\View;
use Livewire\Component;

class ExportKasir extends Component
{
    public $reportContent = '';

    public $selectedDate;

    public $selectedWorker = 'semua';

    public $saleMethod = 'semua';

    public function mount()
    {
        $this->selectedDate = $this->selectedDate ?? now()->toDateString();
        View::share('title', 'Export Laporan Kasir');
        View::share('mainTitle', 'Dashboard');
    }

    public function render()
    {
        $workers = User::all();

        return view('livewire.dashboard.export-kasir', compact('workers'));
    }
}
