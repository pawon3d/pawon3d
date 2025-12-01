<?php

namespace App\Livewire\Dashboard;

use App\Models\Product;
use App\Models\Production;
use App\Models\ProductionDetail;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\View;
use Livewire\Component;

class LaporanProduksi extends Component
{
    use \Livewire\WithPagination;

    public $currentPage = 1;

    public $perPage = 10;

    public $search = '';

    public $selectedDate;

    public $customStartDate = null;

    public $customEndDate = null;

    public $selectedWorker = 'semua';

    public $selectedMethod = 'semua';

    public $filterPeriod = 'Hari';

    public $productions;

    public $prevProductions;

    public $details;

    public $diffStats = [];

    public $topProductionsChartData = [];

    public $productionChartData = [];

    public $showCalendar = false;

    public $currentMonth;

    public $shouldUpdateChart = false;

    protected $listeners = ['refreshCharts' => '$refresh', 'update-top-products'];

    protected $queryString = [
        'selectedWorker' => ['except' => 'semua'],
        'selectedMethod' => ['except' => 'semua'],
        'filterPeriod' => ['except' => 'Hari'],
        'currentPage' => ['except' => 1],
    ];

    public function mount()
    {
        $this->selectedDate = $this->selectedDate ?? now()->toDateString();
        $this->currentMonth = Carbon::parse($this->selectedDate)->startOfMonth()->toDateString();
        View::share('title', 'Laporan Produksi');
        View::share('mainTitle', 'Dashboard');
    }

    public function updatedSearch()
    {
        $this->currentPage = 1;
    }

    public function toggleCalendar()
    {
        $this->showCalendar = ! $this->showCalendar;
    }

    public function previousMonth()
    {
        $this->currentMonth = Carbon::parse($this->currentMonth)->subMonth()->toDateString();
        $this->shouldUpdateChart = true;
    }

    public function nextMonth()
    {
        $this->currentMonth = Carbon::parse($this->currentMonth)->addMonth()->toDateString();
        $this->shouldUpdateChart = true;
    }

    public function getCalendarProperty()
    {
        $month = Carbon::parse($this->currentMonth);
        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth = $month->copy()->endOfMonth();

        $startOfCalendar = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        $endOfCalendar = $endOfMonth->copy()->endOfWeek(Carbon::SUNDAY);

        $calendarStart = $startOfCalendar->toDateString();
        $calendarEnd = $endOfCalendar->toDateString();

        // Query productions for calendar markers - only finished productions (same as chart data)
        $productionQuery = Production::whereBetween('start_date', [$calendarStart, $calendarEnd])
            ->where('is_finish', true);
        if ($this->selectedWorker !== 'semua') {
            $productionQuery->whereHas('workers', fn($q) => $q->where('user_id', $this->selectedWorker));
        }
        if ($this->selectedMethod !== 'semua') {
            $productionQuery->where('method', $this->selectedMethod);
        }
        $productionCollection = $productionQuery->get();
        $productionCounts = $productionCollection->groupBy(fn($p) => Carbon::parse($p->start_date)->toDateString())->map(fn($g) => $g->count())->toArray();

        $dates = [];
        $current = $startOfCalendar->copy();

        $rangeStart = $this->customStartDate ? Carbon::parse($this->customStartDate) : null;
        $rangeEnd = $this->customEndDate ? Carbon::parse($this->customEndDate) : $rangeStart;

        while ($current <= $endOfCalendar) {
            $dateString = $current->toDateString();

            $productionCount = $productionCounts[$dateString] ?? 0;

            $inRange = false;
            $isRangeStart = false;
            $isRangeEnd = false;
            if ($rangeStart) {
                $rangeEndCalc = $rangeEnd ?? $rangeStart;
                $inRange = ($current->betweenIncluded($rangeStart, $rangeEndCalc));
                $isRangeStart = $current->toDateString() === $rangeStart->toDateString();
                $isRangeEnd = $current->toDateString() === $rangeEndCalc->toDateString();
            }

            $dates[$dateString] = [
                'day' => $current->day,
                'isCurrentMonth' => $current->month === $month->month,
                'isWeekend' => $current->isWeekend(),
                'isSelected' => ($this->filterPeriod === 'Custom')
                    ? ($dateString === ($this->customStartDate ?? '') || $dateString === ($this->customEndDate ?? ''))
                    : ($dateString === $this->selectedDate),
                'isSaturday' => $current->isSaturday(),
                'isSunday' => $current->isSunday(),
                'hasData' => $productionCount > 0,
                'count' => $productionCount,
                'productionCount' => $productionCount,
                'inRange' => $inRange,
                'isRangeStart' => $isRangeStart,
                'isRangeEnd' => $isRangeEnd,
            ];
            $current->addDay();
        }

        return $dates;
    }

    private function calculateDiff($current, $previous)
    {
        $diff = $current - $previous;
        $percentage = $previous > 0 ? round(($diff / $previous) * 100, 2) : ($current > 0 ? 100 : 0);

        return [
            'value' => $current,
            'diff' => $diff,
            'percentage' => $percentage,
        ];
    }

    public function updatedSelectedWorker()
    {
        $this->resetPage();
        $this->shouldUpdateChart = true;
    }

    public function updatedSelectedMethod()
    {
        $this->resetPage();
        $this->shouldUpdateChart = true;
    }

    public function updatedFilterPeriod()
    {
        $this->resetPage();
        $this->shouldUpdateChart = true;
    }

    public function updatedCustomStartDate()
    {
        $this->resetPage();
        $this->shouldUpdateChart = true;
    }

    public function updatedCustomEndDate()
    {
        $this->resetPage();
        $this->shouldUpdateChart = true;
    }

    public function updatedSelectedDate()
    {
        $this->resetPage();
        $this->shouldUpdateChart = true;
    }

    public function setFilterPeriod($period)
    {
        $this->filterPeriod = $period;
        $this->resetPage();
        if ($period === 'Custom') {
            $this->customStartDate = $this->selectedDate;
            $this->customEndDate = null;
        } else {
            if ($this->customStartDate) {
                $this->selectedDate = $this->customStartDate;
            }
            $this->customStartDate = null;
            $this->customEndDate = null;
        }

        $this->shouldUpdateChart = true;
    }

    public function clearCustomRange()
    {
        $this->customStartDate = null;
        $this->customEndDate = null;
        $this->resetPage();
        $this->shouldUpdateChart = true;
    }

    public function selectDate($date)
    {
        if ($this->filterPeriod === 'Custom') {
            if (empty($this->customStartDate)) {
                $this->customStartDate = $date;
                $this->customEndDate = null;
            } elseif (empty($this->customEndDate)) {
                if (Carbon::parse($date)->lt(Carbon::parse($this->customStartDate))) {
                    $this->customEndDate = $this->customStartDate;
                    $this->customStartDate = $date;
                } else {
                    $this->customEndDate = $date;
                }
                $this->showCalendar = false;
            } else {
                $this->customStartDate = $date;
                $this->customEndDate = null;
            }

            $this->resetPage();
            $this->shouldUpdateChart = true;

            return;
        }

        $this->selectedDate = $date;
        $this->showCalendar = false;
        $this->resetPage();
        $this->shouldUpdateChart = true;
    }

    public function resetPage()
    {
        $this->currentPage = 1;
    }

    protected function updateChartData($productions, $details)
    {
        $groupedProducts = $details->groupBy('product_id')->map(function ($items) {
            $total = $items->sum('quantity_get');

            return [
                'total' => $total,
                'name' => $items->first()->product->name ?? 'Unknown',
            ];
        });

        $sorted = $groupedProducts->sortByDesc('total');

        $top10 = $sorted->take(10);
        $topProductionsChartData = [
            'labels' => $top10->pluck('name')->values(),
            'data' => $top10->pluck('total')->values(),
        ];

        // Calculate total products (pcs) per method, not just production count
        $productionIds = $productions->pluck('id');
        $methodProductTotals = $productions->mapWithKeys(function ($production) use ($details) {
            $productTotal = $details->where('production_id', $production->id)->sum('quantity_get');

            return [$production->id => ['method' => $production->method, 'total' => $productTotal]];
        })->groupBy('method')->map(function ($items) {
            return $items->sum('total');
        });

        $methodNames = $methodProductTotals->keys()->transform(function ($method) {
            return match ($method) {
                'pesanan-reguler' => 'Pesanan Reguler',
                'pesanan-kotak' => 'Pesanan Kotak',
                default => 'Siap Saji',
            };
        });
        $productionChartData = [
            'labels' => $methodNames,
            'data' => $methodProductTotals->values(),
        ];
        $this->topProductionsChartData = $topProductionsChartData ?? ['labels' => [], 'data' => []];
        $this->productionChartData = $productionChartData ?? ['labels' => [], 'data' => []];
    }

    public function render()
    {
        // Determine date range based on filter period
        // Use toDateString() for DATE column compatibility
        if ($this->filterPeriod === 'Custom' && $this->customStartDate) {
            $startDate = Carbon::parse($this->customStartDate)->toDateString();
            $endDate = $this->customEndDate ? Carbon::parse($this->customEndDate)->toDateString() : $startDate;
            $lengthDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
            $prevStart = Carbon::parse($startDate)->subDays($lengthDays)->toDateString();
            $prevEnd = Carbon::parse($startDate)->subDay()->toDateString();
        } else {
            $selectedDate = Carbon::parse($this->selectedDate);

            switch ($this->filterPeriod) {
                case 'Hari':
                    $startDate = $selectedDate->toDateString();
                    $endDate = $selectedDate->toDateString();
                    $prevStart = $selectedDate->copy()->subDay()->toDateString();
                    $prevEnd = $selectedDate->copy()->subDay()->toDateString();
                    break;
                case 'Minggu':
                    $startDate = $selectedDate->copy()->startOfWeek()->toDateString();
                    $endDate = $selectedDate->copy()->endOfWeek()->toDateString();
                    $prevStart = $selectedDate->copy()->subWeek()->startOfWeek()->toDateString();
                    $prevEnd = $selectedDate->copy()->subWeek()->endOfWeek()->toDateString();
                    break;
                case 'Bulan':
                    $startDate = $selectedDate->copy()->startOfMonth()->toDateString();
                    $endDate = $selectedDate->copy()->endOfMonth()->toDateString();
                    $prevStart = $selectedDate->copy()->subMonth()->startOfMonth()->toDateString();
                    $prevEnd = $selectedDate->copy()->subMonth()->endOfMonth()->toDateString();
                    break;
                case 'Tahun':
                    $startDate = $selectedDate->copy()->startOfYear()->toDateString();
                    $endDate = $selectedDate->copy()->endOfYear()->toDateString();
                    $prevStart = $selectedDate->copy()->subYear()->startOfYear()->toDateString();
                    $prevEnd = $selectedDate->copy()->subYear()->endOfYear()->toDateString();
                    break;
                default:
                    $startDate = $selectedDate->toDateString();
                    $endDate = $selectedDate->toDateString();
                    $prevStart = $selectedDate->copy()->subDay()->toDateString();
                    $prevEnd = $selectedDate->copy()->subDay()->toDateString();
            }
        }

        // Query productions
        $productionsQuery = Production::whereBetween('start_date', [$startDate, $endDate])
            ->where('is_finish', true);

        if ($this->selectedWorker !== 'semua') {
            $productionsQuery->whereHas('workers', fn($q) => $q->where('user_id', $this->selectedWorker));
        }

        if ($this->selectedMethod !== 'semua') {
            $productionsQuery->where('method', $this->selectedMethod);
        }

        $this->productions = $productionsQuery->get();
        $productions = $this->productions;

        // Prev productions
        $prevProductionsQuery = Production::whereBetween('start_date', [$prevStart, $prevEnd])
            ->where('is_finish', true);

        if ($this->selectedWorker !== 'semua') {
            $prevProductionsQuery->whereHas('workers', fn($q) => $q->where('user_id', $this->selectedWorker));
        }

        if ($this->selectedMethod !== 'semua') {
            $prevProductionsQuery->where('method', $this->selectedMethod);
        }

        $this->prevProductions = $prevProductionsQuery->get();
        $prevProductions = $this->prevProductions;

        $productionIds = $productions->pluck('id');
        $prevProductionIds = $prevProductions->pluck('id');

        $this->details = ProductionDetail::with('product')
            ->whereIn('production_id', $productionIds)
            ->get();
        $details = $this->details;

        $prevDetails = ProductionDetail::with('product')
            ->whereIn('production_id', $prevProductionIds)
            ->get();

        $this->updateChartData($productions, $details);

        $groupedProducts = $details->groupBy('product_id')->map(function ($items) {
            $total = $items->sum('quantity_get');

            return [
                'total' => $total,
                'name' => $items->first()->product->name ?? 'Unknown',
            ];
        });

        $sorted = $groupedProducts->sortByDesc('total');

        $top10 = $sorted->take(10);
        $best = $sorted->first();

        $prevBest = $prevDetails->groupBy('product_id')->map(function ($items) {
            $total = $items->sum('quantity_get');

            return [
                'total' => $total,
                'name' => $items->first()->product->name ?? 'Unknown',
            ];
        })->sortByDesc('total')->first();

        $worst = $sorted->filter(fn($p) => $p['total'] > 0)->sortBy('total')->first();

        $prevWorst = $prevDetails->groupBy('product_id')->map(function ($items) {
            $total = $items->sum('quantity_get');

            return [
                'total' => $total,
                'name' => $items->first()->product->name ?? 'Unknown',
            ];
        })->filter(fn($p) => $p['total'] > 0)->sortBy('total')->first();

        $successProduction = $details
            ->where('quantity_get', '>', 0)
            ->sum('quantity_get');
        $prevSuccessProduction = $prevDetails
            ->where('quantity_get', '>', 0)
            ->sum('quantity_get');

        $failedProduction = $details
            ->where('quantity_fail', '>', 0)
            ->sum('quantity_fail');
        $prevFailedProduction = $prevDetails
            ->where('quantity_fail', '>', 0)
            ->sum('quantity_fail');

        $totalProduction = $successProduction + $failedProduction;
        $prevTotalProduction = $prevSuccessProduction + $prevFailedProduction;

        $products = Product::all();
        $productionProducts = $products->map(function ($product) use ($details) {
            $berhasil = $details->where('product_id', $product->id)->sum('quantity_get');
            $gagal = $details->where('product_id', $product->id)->sum('quantity_fail');
            $total = $berhasil + $gagal;

            return (object) [
                'name' => $product->name,
                'total' => $total,
                'success' => $berhasil,
                'fail' => $gagal,
            ];
        })->sortByDesc('total')->values();

        // Filter production products by search term
        if ($this->search) {
            $productionProducts = $productionProducts->filter(fn($item) => stripos($item->name, $this->search) !== false)->values();
        }

        $this->diffStats = [
            'successProduction' => $this->calculateDiff($successProduction, $prevSuccessProduction),
            'failedProduction' => $this->calculateDiff($failedProduction, $prevFailedProduction),
            'totalProduction' => $this->calculateDiff($totalProduction, $prevTotalProduction),
            'best' => $this->calculateDiff($best['total'] ?? 0, $prevBest['total'] ?? 0),
            'worst' => $this->calculateDiff($worst['total'] ?? 0, $prevWorst['total'] ?? 0),
        ];

        // Dispatch chart update if needed
        if ($this->shouldUpdateChart) {
            $this->dispatch('update-charts', [
                'topProductionsChartData' => $this->topProductionsChartData,
                'productionChartData' => $this->productionChartData,
            ]);
            $this->shouldUpdateChart = false;
        }

        // Create a LengthAwarePaginator for the table component
        $total = $productionProducts->count();
        $currentPageItems = $productionProducts->slice(($this->currentPage - 1) * $this->perPage, $this->perPage)->values();
        $paginator = new LengthAwarePaginator($currentPageItems, $total, $this->perPage, $this->currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);

        $tableHeaders = [
            ['label' => 'Produk', 'class' => 'text-left'],
            ['label' => 'Produksi', 'class' => 'text-left'],
            ['label' => 'Berhasil', 'class' => 'text-left'],
            ['label' => 'Gagal', 'class' => 'text-left'],
        ];

        return view('livewire.dashboard.laporan-produksi', [
            'successProduction' => $successProduction,
            'failedProduction' => $failedProduction,
            'totalProduction' => $totalProduction,
            'topProductions' => $top10,
            'bestProduction' => $best,
            'worstProduction' => $worst,
            'diffStats' => $this->diffStats,
            'topProductionsChartData' => $this->topProductionsChartData,
            'productionChartData' => $this->productionChartData,
            'paginator' => $paginator,
            'tableHeaders' => $tableHeaders,
        ]);
    }
}
