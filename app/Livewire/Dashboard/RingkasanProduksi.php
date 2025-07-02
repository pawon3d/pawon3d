<?php

namespace App\Livewire\Dashboard;

use App\Models\Production;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Livewire\Component;
use Livewire\WithPagination;

class RingkasanProduksi extends Component
{
    use WithPagination;
    public $currentMonth;
    public $currentYear;
    public $selectedDate;
    public $calendar = [];
    public $todayProductions = [];
    public $otherProductions = [];

    public $method = 'pesanan-reguler';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'method' => ['except' => 'pesanan-reguler'],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function mount()
    {
        $this->currentMonth = now()->startOfMonth();
        $this->currentYear = now()->year;
        $this->selectedDate = now()->toDateString();
        $this->generateCalendar();
        $this->fetchProductions();
        View::share('title', 'Ringkasan Umum');
        View::share('mainTitle', 'Dashboard');
    }

    public function previousMonth()
    {
        $this->currentMonth = Carbon::parse($this->currentMonth)->subMonth();
        $this->generateCalendar();
        $this->fetchProductions();
    }

    public function nextMonth()
    {
        $this->currentMonth = Carbon::parse($this->currentMonth)->addMonth();
        $this->generateCalendar();
        $this->fetchProductions();
    }

    public function selectDate($date)
    {
        $this->selectedDate = $date;
        $carbonDate = \Carbon\Carbon::parse($date);
        $this->currentMonth = $carbonDate->copy()->startOfMonth(); // tetap Carbon
        $this->currentYear = $carbonDate->year;

        $this->generateCalendar();
        $this->fetchProductions();
    }

    public function generateCalendar()
    {
        $monthStart = $this->currentMonth->copy()->startOfMonth();
        $start = Carbon::create($this->currentYear, Carbon::parse($this->currentMonth)->month, 1)->startOfWeek();
        $end = Carbon::create($this->currentYear, Carbon::parse($this->currentMonth)->month, 1)
            ->endOfMonth()
            ->endOfWeek();

        $productionDates = Production::whereBetween('start_date', [$start->toDateString(), $end->toDateString()])
            ->pluck('start_date')
            ->map(fn($date) => Carbon::parse($date)->toDateString())
            ->toArray();
        $calendar = [];

        while ($start <= $end) {
            $dateString = $start->toDateString();

            $calendar[$dateString] = [
                'isCurrentMonth' => $start->month === $monthStart->month,
                'date' => $start->copy(),
                'hasProduction' => in_array($dateString, $productionDates),
            ];
            $start->addDay();
        }

        $this->calendar = $calendar;
    }

    public function fetchProductions()
    {
        $this->todayProductions = Production::whereDate('start_date', $this->selectedDate)->get();

        $this->otherProductions = Production::whereDate('start_date', '!=', $this->selectedDate)
            ->whereMonth('start_date', Carbon::parse($this->currentMonth)->month)
            ->whereYear('start_date', Carbon::parse($this->currentMonth)->year)
            ->whereNotIn('status', ['Selesai', 'Gagal'])
            ->get();
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
    public function render()
    {
        $query = Production::with(['details.product', 'workers'])
            ->where('productions.method', $this->method)
            // ->where('productions.start_date', '>=', now())
            ->whereHas('workers', function ($q) {
                $q->where('user_id', Auth::id());
            });

        if ($this->sortField === 'product_name') {
            $query->join('production_details', 'productions.id', '=', 'production_details.production_id')
                ->join('products', 'production_details.product_id', '=', 'products.id')
                ->orderBy('products.name', $this->sortDirection);
        } elseif ($this->sortField === 'worker_name') {
            $query->join('production_workers', 'productions.id', '=', 'production_workers.production_id')
                ->join('users', 'production_workers.user_id', '=', 'users.id')
                ->orderBy('users.name', $this->sortDirection);
        } else {
            $query->orderBy("productions.{$this->sortField}", $this->sortDirection);
        }

        $productions = $query->select('productions.*')->distinct()->paginate(10);
        return view('livewire.dashboard.ringkasan-produksi', [
            'productions' => $productions,
        ]);
    }
}
