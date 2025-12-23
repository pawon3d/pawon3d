<?php

namespace App\Livewire\Dashboard;

use App\Models\Production;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Livewire\Component;

class ExportInventori extends Component
{
    public $reportContent = '';

    public $selectedDate;

    public $selectedWorker = 'semua';

    public $filterPeriod = 'Hari';

    public $customStartDate = null;

    public $customEndDate = null;

    public $showCalendar = false;

    public $currentMonth;

    public function mount()
    {
        $this->selectedDate = $this->selectedDate ?? now()->toDateString();
        $this->currentMonth = Carbon::parse($this->selectedDate)->startOfMonth()->toDateString();
        View::share('title', 'Export Laporan Inventori');
        View::share('mainTitle', 'Dashboard');

        if (Auth::user()->permission !== 'manajemen.pembayaran.kelola') {
            $this->selectedWorker = Auth::user()->id;
        }
    }

    public function toggleCalendar()
    {
        $this->showCalendar = ! $this->showCalendar;
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

    public function selectYear($year)
    {
        $this->selectedDate = Carbon::create($year, 1, 1)->toDateString();
        $this->currentMonth = $this->selectedDate;
        $this->showCalendar = false;
    }

    public function selectMonth($month)
    {
        $year = Carbon::parse($this->currentMonth)->year;

        if ($this->filterPeriod === 'Bulan') {
            $this->selectedDate = Carbon::create($year, $month, 1)->toDateString();
        } else {
            $this->selectedDate = Carbon::create($year, $month, 1)->startOfWeek()->toDateString();
        }

        $this->currentMonth = Carbon::create($year, $month, 1)->toDateString();
        $this->showCalendar = false;
    }

    public function selectDate($date)
    {
        if ($this->filterPeriod === 'Custom') {
            if (! $this->customStartDate || ($this->customStartDate && $this->customEndDate)) {
                $this->customStartDate = $date;
                $this->customEndDate = null;
            } elseif ($this->customStartDate && ! $this->customEndDate) {
                $start = Carbon::parse($this->customStartDate);
                $end = Carbon::parse($date);
                if ($end->lt($start)) {
                    $this->customEndDate = $this->customStartDate;
                    $this->customStartDate = $date;
                } else {
                    $this->customEndDate = $date;
                }
                $this->showCalendar = false;
            }
        } else {
            $this->selectedDate = $date;
            $this->showCalendar = false;
        }
    }

    public function setFilterPeriod($period)
    {
        $this->filterPeriod = $period;
        if ($period !== 'Custom') {
            $this->customStartDate = null;
            $this->customEndDate = null;
        }
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

        $expenseQuery = \App\Models\Expense::whereBetween('expense_date', [$calendarStart, $calendarEnd]);

        if ($this->selectedWorker !== 'semua') {
            $expenseIds = \Spatie\Activitylog\Models\Activity::inLog('expenses')
                ->where('causer_id', $this->selectedWorker)
                ->pluck('subject_id')
                ->unique();

            $expenseQuery->whereIn('id', $expenseIds);
        }

        $expenseCollection = $expenseQuery->get();
        $expenseCounts = $expenseCollection->groupBy(fn($e) => Carbon::parse($e->expense_date)->toDateString())->map(fn($g) => $g->count())->toArray();

        $productionQuery = Production::whereBetween('start_date', [$calendarStart, $calendarEnd]);
        if ($this->selectedWorker !== 'semua') {
            $productionQuery->whereHas('workers', fn($q) => $q->where('user_id', $this->selectedWorker));
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
            $expenseCount = $expenseCounts[$dateString] ?? 0;

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
                'hasExpense' => $expenseCount > 0,
                'expenseCount' => $expenseCount,
                'inRange' => $inRange,
                'isRangeStart' => $isRangeStart,
                'isRangeEnd' => $isRangeEnd,
            ];
            $current->addDay();
        }

        return $dates;
    }

    public function render()
    {
        $workers = User::all();

        // Initialize data variables
        $expenseData = collect([]);
        $inventoryData = collect([]);
        $flowData = collect([]);

        // Only load data if reportContent is selected
        if ($this->reportContent && $this->selectedDate) {
            // Determine date range based on filter period
            if ($this->filterPeriod === 'Custom' && $this->customStartDate) {
                $startDate = Carbon::parse($this->customStartDate)->startOfDay();
                $endDate = $this->customEndDate ? Carbon::parse($this->customEndDate)->endOfDay() : Carbon::parse($this->customStartDate)->endOfDay();
            } else {
                $selectedDate = Carbon::parse($this->selectedDate);

                switch ($this->filterPeriod) {
                    case 'Hari':
                        $startDate = $selectedDate->copy()->startOfDay();
                        $endDate = $selectedDate->copy()->endOfDay();
                        break;
                    case 'Minggu':
                        $startDate = $selectedDate->copy()->startOfWeek();
                        $endDate = $selectedDate->copy()->endOfWeek();
                        break;
                    case 'Bulan':
                        $startDate = $selectedDate->copy()->startOfMonth();
                        $endDate = $selectedDate->copy()->endOfMonth();
                        break;
                    case 'Tahun':
                        $startDate = $selectedDate->copy()->startOfYear();
                        $endDate = $selectedDate->copy()->endOfYear();
                        break;
                    default:
                        $startDate = $selectedDate->copy()->startOfDay();
                        $endDate = $selectedDate->copy()->endOfDay();
                }
            }

            // Load data based on reportContent
            if ($this->reportContent === 'belanja') {
                // Get expenses in date range with activity log for worker info
                $expensesQuery = \App\Models\Expense::whereBetween('expense_date', [$startDate->toDateString(), $endDate->toDateString()]);

                if ($this->selectedWorker !== 'semua') {
                    $expenseIds = \Spatie\Activitylog\Models\Activity::inLog('expenses')
                        ->where('causer_id', $this->selectedWorker)
                        ->pluck('subject_id')
                        ->unique();

                    $expensesQuery->whereIn('id', $expenseIds);
                }

                $expenses = $expensesQuery->with(['supplier', 'expenseDetails.material', 'expenseDetails.unit'])->get();

                $expenseData = $expenses->map(function ($expense) {
                    // Get worker from activity log
                    $workerName = \Spatie\Activitylog\Models\Activity::inLog('expenses')
                        ->where('subject_id', $expense->id)
                        ->latest()
                        ->first()?->causer->name ?? '-';

                    return (object) [
                        'expense_number' => $expense->expense_number,
                        'expense_date' => Carbon::parse($expense->expense_date)->format('d M Y'),
                        'supplier' => $expense->supplier->name ?? '-',
                        'worker' => $workerName,
                        'status' => $expense->status,
                        'total_expect' => $expense->grand_total_expect,
                        'total_actual' => $expense->grand_total_actual,
                        'details' => $expense->expenseDetails->map(function ($detail) {
                            return (object) [
                                'material' => $detail->material->name ?? '-',
                                'quantity_expect' => $detail->quantity_expect,
                                'quantity_get' => $detail->quantity_get,
                                'unit' => $detail->unit->alias ?? '-',
                                'price_expect' => $detail->price_expect,
                                'price_get' => $detail->price_get,
                                'total_expect' => $detail->total_expect,
                                'total_actual' => $detail->total_actual,
                            ];
                        }),
                    ];
                });
            } elseif ($this->reportContent === 'persediaan') {
                // Get inventory value data
                $cumulativeExpenseDetails = \App\Models\ExpenseDetail::with('material')
                    ->whereHas('expense', function ($query) use ($endDate) {
                        $query->where('expense_date', '<=', $endDate->toDateString());
                    })
                    ->get();

                $grandTotal = 0;
                foreach ($cumulativeExpenseDetails as $detail) {
                    $grandTotal += $detail->quantity_get * $detail->price_get;
                }

                // Get material usage (production)
                $products = \App\Models\Product::with(['product_compositions.material'])->get();
                $groupProductByMaterial = $products->flatMap(function ($product) {
                    return $product->product_compositions->map(function ($composition) use ($product) {
                        return [
                            'material_id' => $composition->material_id,
                            'material_quantity' => $composition->material_quantity,
                            'unit_id' => $composition->unit_id,
                            'pcs' => $product->pcs,
                            'product_id' => $product->id,
                        ];
                    });
                })->groupBy('material_id');

                $usedGrandTotal = 0;
                $materialUsage = [];

                foreach ($groupProductByMaterial as $materialId => $compositions) {
                    foreach ($compositions as $composition) {
                        $productId = $composition['product_id'];

                        $productionDetailsQuery = \App\Models\ProductionDetail::where('product_id', $productId)
                            ->whereHas('production', function ($query) use ($endDate) {
                                $query->where('start_date', '<=', $endDate->toDateString());
                            });

                        $productionDetails = $productionDetailsQuery->get();
                        $totalProduction = $productionDetails->sum('quantity_get') + $productionDetails->sum('quantity_fail');
                        $dividedQuantity = $totalProduction / $composition['pcs'];
                        $totalMaterialQuantity = $dividedQuantity * $composition['material_quantity'];
                        $material = \App\Models\Material::find($materialId);
                        $materialPrice = $material->material_details->where('unit_id', $composition['unit_id'])->first()->supply_price ?? 0;
                        $priceValue = $totalMaterialQuantity * $materialPrice;
                        $usedGrandTotal += $priceValue;

                        if (! isset($materialUsage[$materialId])) {
                            $materialUsage[$materialId] = [
                                'material_name' => $material->name,
                                'quantity_used' => 0,
                                'value_used' => 0,
                            ];
                        }
                        $materialUsage[$materialId]['quantity_used'] += $totalMaterialQuantity;
                        $materialUsage[$materialId]['value_used'] += $priceValue;
                    }
                }

                $remainGrandTotal = $grandTotal - $usedGrandTotal;

                $inventoryData = (object) [
                    'grand_total' => $grandTotal,
                    'used_grand_total' => $usedGrandTotal,
                    'remain_grand_total' => $remainGrandTotal,
                    'material_usage' => collect($materialUsage)->values(),
                ];
            } elseif ($this->reportContent === 'alur') {
                // Get inventory flow/logs
                $flowQuery = \App\Models\InventoryLog::with(['material', 'materialBatch.unit', 'user'])
                    ->whereBetween('created_at', [$startDate, $endDate]);

                if ($this->selectedWorker !== 'semua') {
                    $flowQuery->where('user_id', $this->selectedWorker);
                }

                $flowData = $flowQuery->orderBy('created_at', 'desc')->get()->map(function ($log) {
                    return (object) [
                        'date' => Carbon::parse($log->created_at)->format('d M Y H:i'),
                        'material' => $log->material->name ?? '-',
                        'batch' => $log->materialBatch->batch_number ?? '-',
                        'action' => ucfirst($log->action),
                        'quantity_change' => $log->quantity_change,
                        'quantity_after' => $log->quantity_after,
                        'unit' => $log->materialBatch->unit->alias ?? '-',
                        'user' => $log->user->name ?? '-',
                        'reference' => $log->reference_type ?? '-',
                        'note' => $log->note ?? '-',
                    ];
                });
            }
        }

        return view('livewire.dashboard.export-inventori', compact('workers', 'expenseData', 'inventoryData', 'flowData'));
    }
}
