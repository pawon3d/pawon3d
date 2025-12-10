<?php

namespace App\Livewire\Dashboard;

use App\Models\User;
use Illuminate\Support\Facades\View;
use Livewire\Component;

class ExportProduksi extends Component
{
    public $reportContent = '';

    public $selectedDate;

    public $selectedWorker = 'semua';

    public function mount()
    {
        $this->selectedDate = $this->selectedDate ?? now()->toDateString();
        View::share('title', 'Export Laporan Produksi');
        View::share('mainTitle', 'Dashboard');
    }

    public function render()
    {
        $workers = User::all();

        return view('livewire.dashboard.export-produksi', compact('workers'));
    }
}
