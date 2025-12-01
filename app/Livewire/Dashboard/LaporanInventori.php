<?php

namespace App\Livewire\Dashboard;

use App\Models\Expense;
use App\Models\ExpenseDetail;
use App\Models\Material;
use App\Models\Product;
use App\Models\Production;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Livewire\Component;

class LaporanInventori extends Component
{
    use \Livewire\WithPagination;

    public $currentPage = 1;

    public $perPage = 10;

    public $selectedDate;

    // Custom range start/end (YYYY-MM-DD)
    public $customStartDate = null;

    public $customEndDate = null;

    public $selectedWorker = 'semua';

    public $filterPeriod = 'Hari';

    public $search = '';

    public $expenses;

    public $prevExpenses;

    public $details;

    public $diffStats = [];

    public $topMaterialChartData = [];

    public $showCalendar = false;

    public $currentMonth;

    public $shouldUpdateChart = false;

    protected $listeners = ['refreshCharts' => '$refresh', 'update-top-products'];

    protected $queryString = [
        'selectedWorker' => ['except' => 'semua'],
        'filterPeriod' => ['except' => 'Hari'],
        'currentPage' => ['except' => 1],
    ];

    public function mount()
    {
        $this->selectedDate = $this->selectedDate ?? now()->toDateString();
        $this->currentMonth = Carbon::parse($this->selectedDate)->startOfMonth()->toDateString();
        View::share('title', 'Laporan Inventori');
        View::share('mainTitle', 'Dashboard');
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

        // Get the Monday of the week containing the first day of month
        $startOfCalendar = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        // Get the Sunday of the week containing the last day of month
        $endOfCalendar = $endOfMonth->copy()->endOfWeek(Carbon::SUNDAY);

        // Collect dates that have expenses or productions within the calendar range
        $calendarStart = $startOfCalendar->toDateString();
        $calendarEnd = $endOfCalendar->toDateString();

        $expenseQuery = \App\Models\Expense::whereBetween('expense_date', [$calendarStart, $calendarEnd]);
        if ($this->selectedWorker !== 'semua') {
            $expenseQuery->where('user_id', $this->selectedWorker);
        }
        $expenseCollection = $expenseQuery->get();
        $expenseCounts = $expenseCollection->groupBy(fn ($e) => Carbon::parse($e->expense_date)->toDateString())->map(fn ($g) => $g->count())->toArray();

        $productionQuery = Production::whereBetween('date', [$calendarStart, $calendarEnd]);
        if ($this->selectedWorker !== 'semua') {
            $productionQuery->whereHas('workers', fn ($q) => $q->where('user_id', $this->selectedWorker));
        }
        $productionCollection = $productionQuery->get();
        $productionCounts = $productionCollection->groupBy(fn ($p) => Carbon::parse($p->date)->toDateString())->map(fn ($g) => $g->count())->toArray();

        $dates = [];
        $current = $startOfCalendar->copy();

        // Prepare custom range boundaries if present
        $rangeStart = $this->customStartDate ? Carbon::parse($this->customStartDate) : null;
        $rangeEnd = $this->customEndDate ? Carbon::parse($this->customEndDate) : $rangeStart;

        while ($current <= $endOfCalendar) {
            $dateString = $current->toDateString();

            // For this calendar we mark dates based on productions only (usage of materials)
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
        // initialize or clear custom range when toggling
        if ($period === 'Custom') {
            $this->customStartDate = $this->selectedDate;
            $this->customEndDate = null;
        } else {
            // moving away from custom: clear custom and keep a single selectedDate
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
        // If we're in custom period, allow selecting a start and end date range
        if ($this->filterPeriod === 'Custom') {
            // No start yet
            if (empty($this->customStartDate)) {
                $this->customStartDate = $date;
                $this->customEndDate = null;
                // keep the calendar open until end is chosen
            } elseif (empty($this->customEndDate)) {
                // If user picks an earlier date than start, swap
                if (Carbon::parse($date)->lt(Carbon::parse($this->customStartDate))) {
                    $this->customEndDate = $this->customStartDate;
                    $this->customStartDate = $date;
                } else {
                    $this->customEndDate = $date;
                }
                // After selecting end, close calendar
                $this->showCalendar = false;
            } else {
                // both set -> start a new range
                $this->customStartDate = $date;
                $this->customEndDate = null;
            }

            $this->resetPage();
            $this->shouldUpdateChart = true;

            return;
        }

        // Non-custom mode: single date selection
        $this->selectedDate = $date;
        $this->showCalendar = false;
        $this->resetPage();
        $this->shouldUpdateChart = true;
    }

    public function resetPage()
    {
        $this->currentPage = 1;
    }

    public function render()
    {
        // Determine date range based on filter period
        if ($this->filterPeriod === 'Custom' && $this->customStartDate) {
            $startDate = Carbon::parse($this->customStartDate)->startOfDay();
            $endDate = $this->customEndDate ? Carbon::parse($this->customEndDate)->endOfDay() : Carbon::parse($this->customStartDate)->endOfDay();
            $lengthDays = $startDate->diffInDays($endDate) + 1;
            $prevStart = $startDate->copy()->subDays($lengthDays);
            $prevEnd = $startDate->copy()->subDay();
        } else {
            $selectedDate = Carbon::parse($this->selectedDate);

            // Determine date range based on filter period
            switch ($this->filterPeriod) {
                case 'Hari':
                    $startDate = $selectedDate->copy()->startOfDay();
                    $endDate = $selectedDate->copy()->endOfDay();
                    $prevStart = $startDate->copy()->subDay();
                    $prevEnd = $endDate->copy()->subDay();
                    break;
                case 'Minggu':
                    $startDate = $selectedDate->copy()->startOfWeek();
                    $endDate = $selectedDate->copy()->endOfWeek();
                    $prevStart = $startDate->copy()->subWeek();
                    $prevEnd = $endDate->copy()->subWeek();
                    break;
                case 'Bulan':
                    $startDate = $selectedDate->copy()->startOfMonth();
                    $endDate = $selectedDate->copy()->endOfMonth();
                    $prevStart = $startDate->copy()->subMonth();
                    $prevEnd = $endDate->copy()->subMonth();
                    break;
                case 'Tahun':
                    $startDate = $selectedDate->copy()->startOfYear();
                    $endDate = $selectedDate->copy()->endOfYear();
                    $prevStart = $startDate->copy()->subYear();
                    $prevEnd = $endDate->copy()->subYear();
                    break;
                default:
                    $startDate = $selectedDate->copy()->startOfDay();
                    $endDate = $selectedDate->copy()->endOfDay();
                    $prevStart = $startDate->copy()->subDay();
                    $prevEnd = $endDate->copy()->subDay();
            }
        }

        $expensesQuery = Expense::whereBetween('expense_date', [$startDate, $endDate]);

        // Filter by worker if not 'semua'
        if ($this->selectedWorker !== 'semua') {
            $expensesQuery->where('user_id', $this->selectedWorker);
        }

        $this->expenses = $expensesQuery->get();
        $expenses = $this->expenses;

        $this->prevExpenses = Expense::whereBetween('expense_date', [$prevStart, $prevEnd])
            ->get();
        $prevExpenses = $this->prevExpenses;

        $expenseIds = $expenses->pluck('id');
        $prevExpenseIds = $prevExpenses->pluck('id');

        $this->details = ExpenseDetail::with('material')
            ->whereIn('expense_id', $expenseIds)
            ->get();
        $details = $this->details;

        $prevDetails = ExpenseDetail::with('material')
            ->whereIn('expense_id', $prevExpenseIds)
            ->get();

        $groupedProducts = $details->groupBy('material_id')->map(function ($items) {
            $total = $items->sum('quantity_get');

            return [
                'total' => $total,
                'name' => $items->first()->material->name ?? 'Unknown',
            ];
        });

        $sorted = $groupedProducts->sortByDesc('total');
        $best = $sorted->first();

        $prevBest = $prevDetails->groupBy('material_id')->map(function ($items) {
            $total = $items->sum('quantity_get');

            return [
                'total' => $total,
                'name' => $items->first()->material->name ?? 'Unknown',
            ];
        })->sortByDesc('total')->first();

        $worst = $sorted->filter(fn ($p) => $p['total'] > 0)->sortBy('total')->first();

        $prevWorst = $prevDetails->groupBy('material_id')->map(function ($items) {
            $total = $items->sum('quantity_get');

            return [
                'total' => $total,
                'name' => $items->first()->material->name ?? 'Unknown',
            ];
        })->filter(fn ($p) => $p['total'] > 0)->sortBy('total')->first();

        $totalExpense = $expenses->count();
        $prevTotalExpense = $prevExpenses->count();

        $materials = Material::with(['material_details', 'batches'])->get();
        $remainGrandTotal = 0;
        foreach ($materials as $material) {
            $materialTotal = 0;
            foreach ($material->material_details as $detail) {
                $remainBatchQty = $material->batches->where('unit_id', $detail->unit_id)->sum('batch_quantity');
                $detailTotal = $detail->supply_price * $remainBatchQty;
                $materialTotal += $detailTotal;
            }
            $remainGrandTotal += $materialTotal;
        }
        $prevRemainGrandTotal = 0;
        foreach ($materials as $material) {
            $materialTotal = 0;
            foreach ($material->material_details as $detail) {
                $remainBatchQty = $material->batches->where('unit_id', $detail->unit_id)->sum('batch_quantity');
                $detailTotal = $detail->supply_price * $remainBatchQty;
                $materialTotal += $detailTotal;
            }
            $prevRemainGrandTotal += $materialTotal;
        }
        $products = Product::with(['product_compositions.material'])->get();
        $groupProductByMaterial = $products->flatMap(function ($product) {
            return $product->product_compositions->map(function ($composition) use ($product) {
                return [
                    'material_id' => $composition->material_id,
                    'material_quantity' => $composition->material_quantity,
                    'unit_id' => $composition->unit_id,
                    'pcs' => $product->pcs,
                    'product_id' => $product->id,
                    'material_name' => $composition->material->name ?? 'Unknown',
                ];
            });
        })->groupBy('material_id');

        // Loop untuk mengambil semua product_id dari semua material
        // Filter ProductionDetail berdasarkan tanggal dan worker
        $totalPrice = [];
        $usedGrandTotal = 0;
        foreach ($groupProductByMaterial as $materialId => $compositions) {
            foreach ($compositions as $composition) {
                $productId = $composition['product_id'];

                // Query ProductionDetail dengan filter tanggal dan worker
                $productionDetailsQuery = \App\Models\ProductionDetail::where('product_id', $productId)
                    ->whereHas('production', function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('date', [$startDate, $endDate]);

                        // Filter by worker if selected
                        if ($this->selectedWorker !== 'semua') {
                            $query->whereHas('workers', function ($workerQuery) {
                                $workerQuery->where('user_id', $this->selectedWorker);
                            });
                        }
                    });

                $productionDetails = $productionDetailsQuery->get();
                $totalProduction = $productionDetails->sum('quantity_get') + $productionDetails->sum('quantity_fail');
                $dividedQuantity = $totalProduction / $composition['pcs'];
                $totalMaterialQuantity = $dividedQuantity * $composition['material_quantity'];
                $material = Material::find($materialId);
                $materialPrice = $material->material_details->where('unit_id', $composition['unit_id'])->first()->supply_price ?? 0;
                $priceValue = $totalMaterialQuantity * $materialPrice;
                $totalPrice[$materialId][] = $priceValue;
                $usedGrandTotal += $priceValue;
            }
        }

        $sumPrices = collect($totalPrice)->mapWithKeys(function ($priceArray, $materialId) {
            return [$materialId => array_sum($priceArray)];
        });

        // Urutkan dari yang paling tinggi
        $sorted = $sumPrices->sortDesc()->take(10);

        $topMaterialChartData = [
            'labels' => [],
            'data' => [],
        ];

        foreach ($sorted as $materialId => $sumPrice) {
            // Skip materials with no value for the selected date/worker
            if (! $sumPrice || $sumPrice <= 0) {
                continue;
            }

            $material = Material::find($materialId);
            $label = $material?->name ?? 'Unknown';

            $topMaterialChartData['labels'][] = $label;
            $topMaterialChartData['data'][] = $sumPrice;
        }

        $this->topMaterialChartData = $topMaterialChartData;

        // Derive best and worst materials based on production value (same source as topMaterialChartData)
        $best = null;
        $worst = null;
        if (! empty($sorted) && $sorted->count() > 0) {
            // $sorted is materialId => value (desc)
            $firstId = $sorted->keys()->first();
            $firstValue = $sorted->first();
            $best = [
                'total' => $firstValue,
                'name' => Material::find($firstId)?->name ?? 'Unknown',
            ];

            $nonZero = $sorted->filter(fn ($v) => $v > 0);
            if ($nonZero->count() > 0) {
                $lastId = $nonZero->keys()->last();
                $lastValue = $nonZero->last();
                $worst = [
                    'total' => $lastValue,
                    'name' => Material::find($lastId)?->name ?? 'Unknown',
                ];
            }
        }

        $grandTotal = $remainGrandTotal + $usedGrandTotal;

        $materialTables = $materials->map(function ($material) use ($groupProductByMaterial) {
            $remainQty = 0;
            $remainValue = 0;
            $remainUnitAlias = null;

            foreach ($material->material_details as $detail) {
                $qty = $material->batches->where('unit_id', $detail->unit_id)->sum('batch_quantity');
                $remainQty += $qty;
                $remainValue += $qty * $detail->supply_price;

                // Ambil alias dari unit untuk remain
                if (! $remainUnitAlias && $qty > 0 && $detail->unit) {
                    $remainUnitAlias = $detail->unit->alias;
                }
            }

            $usedValue = 0;
            $usedQty = 0;
            $usedUnitAlias = null;

            $compositions = $groupProductByMaterial[$material->id] ?? collect();
            foreach ($compositions as $composition) {
                $productId = $composition['product_id'];
                $pcs = $composition['pcs'];
                if ($pcs <= 0) {
                    continue;
                }

                $productionDetails = \App\Models\ProductionDetail::where('product_id', $productId)->get();
                $totalProduction = $productionDetails->sum('quantity_get') + $productionDetails->sum('quantity_fail');
                $dividedQuantity = $totalProduction / $pcs;
                $totalMaterialQuantity = $dividedQuantity * $composition['material_quantity'];

                $materialDetail = $material->material_details->where('unit_id', $composition['unit_id'])->first();
                $materialPrice = $materialDetail->supply_price ?? 0;
                $usedValue += $totalMaterialQuantity * $materialPrice;
                $usedQty += $totalMaterialQuantity;

                if (! $usedUnitAlias && $materialDetail && $materialDetail->unit) {
                    $usedUnitAlias = $materialDetail->unit->alias;
                }
            }

            return (object) [
                'name' => $material->name,
                'total' => $usedQty + $remainQty,
                'total_alias' => $usedUnitAlias ?? $remainUnitAlias,
                'total_price' => $usedValue + $remainValue,

                'used' => $usedQty,
                'used_alias' => $usedUnitAlias,
                'used_price' => $usedValue,

                'remain' => $remainQty,
                'remain_alias' => $remainUnitAlias,
                'remain_price' => $remainValue,
            ];
        })->sortByDesc('total')->values();

        // Apply search filter to materialTables (case-insensitive)
        $filteredMaterials = $materialTables;
        if (! empty($this->search)) {
            $searchLower = Str::lower($this->search);
            $filteredMaterials = $materialTables->filter(fn ($m) => Str::contains(Str::lower($m->name), $searchLower))->values();
        }

        // Create a LengthAwarePaginator for the table component
        $total = $filteredMaterials->count();
        $currentPageItems = $filteredMaterials->slice(($this->currentPage - 1) * $this->perPage, $this->perPage)->values();
        $paginator = new LengthAwarePaginator($currentPageItems, $total, $this->perPage, $this->currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);

        $this->diffStats = [
            'totalExpense' => $this->calculateDiff($totalExpense, $prevTotalExpense),
            'grandTotal' => $this->calculateDiff($grandTotal, $grandTotal),
            'usedGrandTotal' => $this->calculateDiff($usedGrandTotal, $usedGrandTotal),
            'remainGrandTotal' => $this->calculateDiff($remainGrandTotal, $prevRemainGrandTotal),
            'best' => $this->calculateDiff($best['total'] ?? 0, $prevBest['total'] ?? 0),
            'worst' => $this->calculateDiff($worst['total'] ?? 0, $prevWorst['total'] ?? 0),
        ];

        // Dispatch chart update after data is calculated
        if ($this->shouldUpdateChart) {
            $this->dispatch('update-charts', [
                'topMaterialChartData' => $this->topMaterialChartData,
            ]);
            $this->shouldUpdateChart = false;
        }

        $tableHeaders = [
            ['label' => 'Persediaan', 'class' => 'text-left'],
            ['label' => 'Jumlah Belanja', 'class' => 'text-right', 'align' => 'right'],
            ['label' => 'Modal Belanja', 'class' => 'text-right', 'align' => 'right'],
            ['label' => 'Jumlah Terpakai', 'class' => 'text-right', 'align' => 'right'],
            ['label' => 'Modal Terpakai', 'class' => 'text-right', 'align' => 'right'],
            ['label' => 'Jumlah Tersisa', 'class' => 'text-right', 'align' => 'right'],
            ['label' => 'Modal Tersisa', 'class' => 'text-right', 'align' => 'right'],
        ];

        return view('livewire.dashboard.laporan-inventori', [
            'grandTotal' => $grandTotal,
            'usedGrandTotal' => $usedGrandTotal,
            'remainGrandTotal' => $remainGrandTotal,
            'totalExpense' => $totalExpense,
            'bestMaterial' => $best,
            'worstMaterial' => $worst,
            'diffStats' => $this->diffStats,
            'topMaterialChartData' => $this->topMaterialChartData,
            'paginator' => $paginator,
            'totalProductSales' => $materialTables->count(),
            'totalPages' => ceil($materialTables->count() / $this->perPage),
            'tableHeaders' => $tableHeaders,
        ]);
    }
}
