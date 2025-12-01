<?php

namespace App\Livewire\Dashboard;

use App\Models\Production;
use Carbon\Carbon;
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

    public $search = '';

    public $selectedSection = 'produksi';

    protected $queryString = [
        'method' => ['except' => 'pesanan-reguler'],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'search' => ['except' => ''],
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

    public function updatedSelectedSection()
    {
        if ($this->selectedSection == 'kasir') {
            $this->redirectIntended(default: route('ringkasan-kasir', absolute: false), navigate: true);
        } elseif ($this->selectedSection == 'inventori') {
            $this->redirectIntended(default: route('ringkasan-inventori', absolute: false), navigate: true);
        } else {
            // tetap di produksi
        }
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
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
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
        // Show productions that are scheduled in the near future (7-day window based on selectedDate).
        // Include productions that may not have a transaction (e.g. 'siap-beli').
        $startDate = Carbon::parse($this->selectedDate)->toDateString();
        $endDate = Carbon::parse($this->selectedDate)->addDays(6)->toDateString();
        $nearestQuery = Production::with(['details.product', 'workers', 'transaction'])
            ->whereNotIn('productions.status', ['Selesai', 'Gagal'])
            ->whereBetween('productions.start_date', [$startDate, $endDate]);

        if ($this->search) {
            $nearestQuery->where(function ($q) {
                $q->where('productions.production_number', 'like', "%{$this->search}%")
                    ->orWhereHas('transaction', function ($transactionQuery) {
                        $transactionQuery->where('invoice_number', 'like', "%{$this->search}%");
                    });
            });
        }

        if ($this->sortField === 'product_name') {
            $nearestQuery->join('production_details', 'productions.id', '=', 'production_details.production_id')
                ->join('products', 'production_details.product_id', '=', 'products.id')
                ->orderBy('products.name', $this->sortDirection);
        } elseif ($this->sortField === 'worker_name') {
            $nearestQuery->join('production_workers', 'productions.id', '=', 'production_workers.production_id')
                ->join('users', 'production_workers.user_id', '=', 'users.id')
                ->orderBy('users.name', $this->sortDirection);
        } else {
            $nearestQuery->orderBy("productions.{$this->sortField}", $this->sortDirection);
        }

        $nearestProductions = $nearestQuery->select('productions.*')->distinct()->paginate(6, ['*'], 'nearest_page');

        $ongoingProductions = Production::with(['details.product', 'workers', 'transaction'])
            ->where('productions.status', 'Sedang Diproses')
            ->orderBy('start_date', 'asc')
            ->paginate(6, ['*'], 'ongoing_page');

        return view('livewire.dashboard.ringkasan-produksi', [
            'nearestProductions' => $nearestProductions,
            'ongoingProductions' => $ongoingProductions,
        ]);
    }
}
