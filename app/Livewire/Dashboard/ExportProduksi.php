<?php

namespace App\Livewire\Dashboard;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Livewire\Component;

class ExportProduksi extends Component
{
    public $reportContent = '';

    public $selectedDate;

    public $selectedWorker = 'semua';

    public $selectedMethod = 'semua';

    // Calendar properties
    public $showCalendar = false;

    public $filterPeriod = 'Hari';

    public $currentMonth;

    public $customStartDate;

    public $customEndDate;

    public function mount()
    {
        $this->selectedDate = $this->selectedDate ?? now()->toDateString();
        $this->currentMonth = now()->toDateString();
        $this->customStartDate = now()->toDateString();
        $this->customEndDate = now()->toDateString();
        View::share('title', 'Export Laporan Produksi');
        View::share('mainTitle', 'Dashboard');

        if (Auth::user()->permission !== 'manajemen.pembayaran.kelola') {
            $this->selectedWorker = Auth::user()->id;
        }
    }

    public function setFilterPeriod($period)
    {
        $this->filterPeriod = $period;
    }

    public function previousMonth()
    {
        $this->currentMonth = Carbon::parse($this->currentMonth)->subMonth()->toDateString();
    }

    public function nextMonth()
    {
        $this->currentMonth = Carbon::parse($this->currentMonth)->addMonth()->toDateString();
    }

    public function previousYear()
    {
        $this->currentMonth = Carbon::parse($this->currentMonth)->subYear()->toDateString();
    }

    public function nextYear()
    {
        $this->currentMonth = Carbon::parse($this->currentMonth)->addYear()->toDateString();
    }

    public function selectDate($date)
    {
        $this->selectedDate = $date;
        $this->showCalendar = false;
    }

    public function selectYear($year)
    {
        $this->selectedDate = Carbon::create($year, 1, 1)->toDateString();
        $this->showCalendar = false;
    }

    public function selectMonth($month)
    {
        $year = Carbon::parse($this->currentMonth)->year;
        if ($this->filterPeriod === 'Bulan') {
            $this->selectedDate = Carbon::create($year, $month, 1)->toDateString();
        } else { // Minggu
            $this->selectedDate = Carbon::create($year, $month, 1)->startOfWeek()->toDateString();
        }
        $this->showCalendar = false;
    }

    public function render()
    {
        $workers = User::all();

        return view('livewire.dashboard.export-produksi', compact('workers'));
    }
}
