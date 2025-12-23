<?php

namespace App\Livewire\Dashboard;

use App\Models\Payment;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Livewire\Component;

class LaporanKasir extends Component
{
    use \Livewire\WithPagination;

    public $readyToLoad = false;

    public $currentPage = 1;

    public $perPage = 10;

    public $searchProduct = '';

    public $searchReport = '';

    public $selectedDate;

    public $customStartDate = null;

    public $customEndDate = null;

    public $selectedWorker = 'semua';

    public $selectedMethod = 'semua';

    public $filterPeriod = 'Hari';

    public $selectedChart = 'gross';

    public $transactions;

    public $prevTransactions;

    public $details;

    public $diffStats = [];

    public $topProductsChartData = [];

    public $paymentChartData = [];

    public $salesChartData = [];

    public $chartRevenue = [];

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

    public function loadData(): void
    {
        $this->readyToLoad = true;
    }

    public function mount()
    {
        $this->selectedDate = $this->selectedDate ?? now()->toDateString();
        $this->currentMonth = Carbon::parse($this->selectedDate)->startOfMonth()->toDateString();
        View::share('title', 'Laporan Kasir');
        View::share('mainTitle', 'Dashboard');

        if (Auth::user()->permission !== 'manajemen.pembayaran.kelola') {
            $this->selectedWorker = Auth::user()->id;
        }
    }

    public function updatedSearchProduct()
    {
        $this->currentPage = 1;
    }

    public function updatedSearchReport()
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

    public function previousYear()
    {
        $this->currentMonth = Carbon::parse($this->currentMonth)->subYear()->toDateString();
        $this->shouldUpdateChart = true;
    }

    public function nextYear()
    {
        $this->currentMonth = Carbon::parse($this->currentMonth)->addYear()->toDateString();
        $this->shouldUpdateChart = true;
    }

    public function selectYear($year)
    {
        $this->selectedDate = Carbon::create($year, 1, 1)->toDateString();
        $this->currentMonth = $this->selectedDate;
        $this->showCalendar = false;
        $this->resetPage();
        $this->shouldUpdateChart = true;
    }

    public function selectMonth($month)
    {
        $year = Carbon::parse($this->currentMonth)->year;

        if ($this->filterPeriod === 'Bulan') {
            // For Bulan filter, select first day of that month
            $this->selectedDate = Carbon::create($year, $month, 1)->toDateString();
        } else {
            // For Minggu filter, select first day of first week of that month
            $this->selectedDate = Carbon::create($year, $month, 1)->startOfWeek()->toDateString();
        }

        $this->currentMonth = Carbon::create($year, $month, 1)->toDateString();
        $this->showCalendar = false;
        $this->resetPage();
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

        // Query transactions for calendar markers - only completed transactions
        $transactionQuery = Transaction::whereBetween('start_date', [$calendarStart, $calendarEnd])
            ->where('status', 'Selesai');
        if ($this->selectedWorker !== 'semua') {
            $transactionQuery->where('user_id', $this->selectedWorker);
        }
        if ($this->selectedMethod !== 'semua') {
            $transactionQuery->where('method', $this->selectedMethod);
        }
        $transactionCollection = $transactionQuery->get();
        $transactionCounts = $transactionCollection->groupBy(fn($t) => Carbon::parse($t->start_date)->toDateString())->map(fn($g) => $g->count())->toArray();

        $dates = [];
        $current = $startOfCalendar->copy();

        $rangeStart = $this->customStartDate ? Carbon::parse($this->customStartDate) : null;
        $rangeEnd = $this->customEndDate ? Carbon::parse($this->customEndDate) : $rangeStart;

        while ($current <= $endOfCalendar) {
            $dateString = $current->toDateString();

            $transactionCount = $transactionCounts[$dateString] ?? 0;

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
                'hasData' => $transactionCount > 0,
                'count' => $transactionCount,
                'transactionCount' => $transactionCount,
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

    public function updatedSelectedChart()
    {
        // Trigger re-render so chartRevenue gets recalculated based on new selectedChart
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

    // public function exportPdf()
    // {
    //     // Determine date range based on filter period
    //     if ($this->filterPeriod === 'Custom' && $this->customStartDate) {
    //         $startDate = Carbon::parse($this->customStartDate)->startOfDay();
    //         $endDate = $this->customEndDate ? Carbon::parse($this->customEndDate)->endOfDay() : Carbon::parse($this->customStartDate)->endOfDay();
    //         $lengthDays = Carbon::parse($this->customStartDate)->diffInDays(Carbon::parse($this->customEndDate ?? $this->customStartDate)) + 1;
    //         $prevStart = Carbon::parse($this->customStartDate)->subDays($lengthDays)->startOfDay();
    //         $prevEnd = Carbon::parse($this->customStartDate)->subDay()->endOfDay();
    //         $dateRange = Carbon::parse($this->customStartDate)->translatedFormat('d F Y') . ' - ' . Carbon::parse($this->customEndDate ?? $this->customStartDate)->translatedFormat('d F Y');
    //     } else {
    //         $selectedDate = Carbon::parse($this->selectedDate);

    //         switch ($this->filterPeriod) {
    //             case 'Hari':
    //                 $startDate = $selectedDate->copy()->startOfDay();
    //                 $endDate = $selectedDate->copy()->endOfDay();
    //                 $prevStart = $selectedDate->copy()->subDay()->startOfDay();
    //                 $prevEnd = $selectedDate->copy()->subDay()->endOfDay();
    //                 $dateRange = $selectedDate->translatedFormat('d F Y');
    //                 break;
    //             case 'Minggu':
    //                 $startDate = $selectedDate->copy()->startOfWeek()->startOfDay();
    //                 $endDate = $selectedDate->copy()->endOfWeek()->endOfDay();
    //                 $prevStart = $selectedDate->copy()->subWeek()->startOfWeek()->startOfDay();
    //                 $prevEnd = $selectedDate->copy()->subWeek()->endOfWeek()->endOfDay();
    //                 $dateRange = $startDate->translatedFormat('d F Y') . ' - ' . $endDate->translatedFormat('d F Y');
    //                 break;
    //             case 'Bulan':
    //                 $startDate = $selectedDate->copy()->startOfMonth()->startOfDay();
    //                 $endDate = $selectedDate->copy()->endOfMonth()->endOfDay();
    //                 $prevStart = $selectedDate->copy()->subMonth()->startOfMonth()->startOfDay();
    //                 $prevEnd = $selectedDate->copy()->subMonth()->endOfMonth()->endOfDay();
    //                 $dateRange = $selectedDate->translatedFormat('F Y');
    //                 break;
    //             case 'Tahun':
    //                 $startDate = $selectedDate->copy()->startOfYear()->startOfDay();
    //                 $endDate = $selectedDate->copy()->endOfYear()->endOfDay();
    //                 $prevStart = $selectedDate->copy()->subYear()->startOfYear()->startOfDay();
    //                 $prevEnd = $selectedDate->copy()->subYear()->endOfYear()->endOfDay();
    //                 $dateRange = 'Tahun ' . $selectedDate->year;
    //                 break;
    //             default:
    //                 $startDate = $selectedDate->copy()->startOfDay();
    //                 $endDate = $selectedDate->copy()->endOfDay();
    //                 $prevStart = $selectedDate->copy()->subDay()->startOfDay();
    //                 $prevEnd = $selectedDate->copy()->subDay()->endOfDay();
    //                 $dateRange = $selectedDate->translatedFormat('d F Y');
    //         }
    //     }

    //     // Query transactions
    //     $transactionsQuery = Transaction::whereBetween('start_date', [$startDate, $endDate])
    //         ->where('status', 'Selesai');

    //     if ($this->selectedWorker !== 'semua') {
    //         $transactionsQuery->where('user_id', $this->selectedWorker);
    //     }

    //     if ($this->selectedMethod !== 'semua') {
    //         $transactionsQuery->where('method', $this->selectedMethod);
    //     }

    //     $transactions = $transactionsQuery->get();

    //     // Prev transactions
    //     $prevTransactionsQuery = Transaction::whereBetween('start_date', [$prevStart, $prevEnd])
    //         ->where('status', 'Selesai');

    //     if ($this->selectedWorker !== 'semua') {
    //         $prevTransactionsQuery->where('user_id', $this->selectedWorker);
    //     }

    //     if ($this->selectedMethod !== 'semua') {
    //         $prevTransactionsQuery->where('method', $this->selectedMethod);
    //     }

    //     $prevTransactions = $prevTransactionsQuery->get();

    //     $transactionIds = $transactions->pluck('id');
    //     $prevTransactionIds = $prevTransactions->pluck('id');

    //     $details = TransactionDetail::with('product')
    //         ->whereIn('transaction_id', $transactionIds)
    //         ->get();

    //     $prevDetails = TransactionDetail::with('product')
    //         ->whereIn('transaction_id', $prevTransactionIds)
    //         ->get();

    //     $groupedProducts = $details->groupBy('product_id')->map(function ($items) {
    //         $total = $items->sum(fn($d) => $d->quantity - $d->refund_quantity);

    //         return [
    //             'total' => $total,
    //             'name' => $items->first()->product->name ?? 'Unknown',
    //         ];
    //     });

    //     $sorted = $groupedProducts->sortByDesc('total');
    //     $top10 = $sorted->take(10);
    //     $best = $sorted->first();

    //     $prevBest = $prevDetails->groupBy('product_id')->map(function ($items) {
    //         $total = $items->sum(fn($d) => $d->quantity - $d->refund_quantity);

    //         return [
    //             'total' => $total,
    //             'name' => $items->first()->product->name ?? 'Unknown',
    //         ];
    //     })->sortByDesc('total')->first();

    //     $worst = $sorted->filter(fn($p) => $p['total'] > 0)->sortBy('total')->first();

    //     $prevWorst = $prevDetails->groupBy('product_id')->map(function ($items) {
    //         $total = $items->sum(fn($d) => $d->quantity - $d->refund_quantity);

    //         return [
    //             'total' => $total,
    //             'name' => $items->first()->product->name ?? 'Unknown',
    //         ];
    //     })->filter(fn($p) => $p['total'] > 0)->sortBy('total')->first();

    //     $sessionCount = $transactions->unique('created_by_shift')->count();
    //     $prevSessionCount = $prevTransactions->unique('created_by_shift')->count();

    //     $transactionCount = $transactions->count();
    //     $prevTransactionCount = $prevTransactions->count();

    //     $customerCount = $transactions->unique('phone')->count();
    //     $prevCustomerCount = $prevTransactions->unique('phone')->count();

    //     $productSold = $details->sum(fn($d) => $d->quantity - $d->refund_quantity);
    //     $prevProductSold = $prevDetails->sum(fn($d) => $d->quantity - $d->refund_quantity);

    //     $grossRevenue = $transactions->sum('total_amount');
    //     $prevGrossRevenue = $prevTransactions->sum('total_amount');

    //     $discountTotal = $transactions->sum('points_discount');
    //     $prevDiscountTotal = $prevTransactions->sum('points_discount');

    //     $refundTotal = $transactions->sum('total_refund');
    //     $prevRefundTotal = $prevTransactions->sum('total_refund');

    //     $netRevenue = $grossRevenue - $refundTotal - $discountTotal;
    //     $prevNetRevenue = $prevGrossRevenue - $prevRefundTotal - $prevDiscountTotal;

    //     $capitalTotal = $details->sum(function ($d) {
    //         return ($d->product->pcs_capital ?? 0) * ($d->quantity - $d->refund_quantity);
    //     });
    //     $prevCapitalTotal = $prevDetails->sum(function ($d) {
    //         return ($d->product->pcs_capital ?? 0) * ($d->quantity - $d->refund_quantity);
    //     });
    //     $profit = $netRevenue - $capitalTotal;
    //     $prevProfit = $prevNetRevenue - $prevCapitalTotal;

    //     $diffStats = [
    //         'sessionCount' => $this->calculateDiff($sessionCount, $prevSessionCount),
    //         'transactionCount' => $this->calculateDiff($transactionCount, $prevTransactionCount),
    //         'customerCount' => $this->calculateDiff($customerCount, $prevCustomerCount),
    //         'productSold' => $this->calculateDiff($productSold, $prevProductSold),
    //         'best' => $this->calculateDiff($best['total'] ?? 0, $prevBest['total'] ?? 0),
    //         'worst' => $this->calculateDiff($worst['total'] ?? 0, $prevWorst['total'] ?? 0),
    //         'grossRevenue' => $this->calculateDiff($grossRevenue, $prevGrossRevenue),
    //         'discount' => $this->calculateDiff($discountTotal, $prevDiscountTotal),
    //         'refund' => $this->calculateDiff($refundTotal, $prevRefundTotal),
    //         'netRevenue' => $this->calculateDiff($netRevenue, $prevNetRevenue),
    //         'profit' => $this->calculateDiff($profit, $prevProfit),
    //     ];

    //     // Product sales for table
    //     $products = Product::all();
    //     $productSales = $products->map(function ($product) use ($details) {
    //         $terjual = $details->where('product_id', $product->id)->sum(fn($d) => $d->quantity - $d->refund_quantity);
    //         $produksi = $terjual;
    //         $tidakTerjual = max(0, $produksi - $terjual);

    //         return (object) [
    //             'name' => $product->name,
    //             'produksi' => $produksi,
    //             'sold' => $terjual,
    //             'unsold' => $tidakTerjual,
    //         ];
    //     })->filter(fn($item) => $item->sold > 0)->sortByDesc('sold')->values();

    //     // Worker and method names
    //     $workerName = $this->selectedWorker === 'semua' ? 'Semua Pekerja' : (User::find($this->selectedWorker)?->name ?? 'Unknown');
    //     $methodName = match ($this->selectedMethod) {
    //         'semua' => 'Semua Metode',
    //         'pesanan-reguler' => 'Pesanan Reguler',
    //         'pesanan-kotak' => 'Pesanan Kotak',
    //         'siap-beli' => 'Siap Saji',
    //         default => 'Semua Metode',
    //     };

    //     $pdf = Pdf::loadView('pdf.laporan-kasir', [
    //         'dateRange' => $dateRange,
    //         'workerName' => $workerName,
    //         'methodName' => $methodName,
    //         'sessionCount' => $sessionCount,
    //         'transactionCount' => $transactionCount,
    //         'customerCount' => $customerCount,
    //         'productSold' => $productSold,
    //         'bestProduct' => $best,
    //         'worstProduct' => $worst,
    //         'grossRevenue' => $grossRevenue,
    //         'discountTotal' => $discountTotal,
    //         'refundTotal' => $refundTotal,
    //         'netRevenue' => $netRevenue,
    //         'profit' => $profit,
    //         'diffStats' => $diffStats,
    //         'topProducts' => $top10->toArray(),
    //         'productSales' => $productSales,
    //     ]);

    //     return response()->streamDownload(function () use ($pdf) {
    //         echo $pdf->output();
    //     }, 'laporan-kasir-' . now()->format('Y-m-d') . '.pdf');
    // }

    protected function updateChartData($transactions, $details)
    {
        $groupedProducts = $details->groupBy('product_id')->map(function ($items) {
            $total = $items->sum(fn($d) => $d->quantity - $d->refund_quantity);

            return [
                'total' => $total,
                'name' => $items->first()->product->name ?? 'Unknown',
            ];
        });

        $sorted = $groupedProducts->sortByDesc('total');

        $top10 = $sorted->take(10);
        $topProductsChartData = [
            'labels' => $top10->pluck('name')->values(),
            'data' => $top10->pluck('total')->values(),
        ];

        // Data untuk pie chart metode pembayaran
        $paymentMethodCounts = Payment::whereIn('transaction_id', $transactions->pluck('id'))
            ->groupBy('payment_method')
            ->selectRaw('payment_method, sum(paid_amount) as total')
            ->pluck('total', 'payment_method');
        $paymentMethodCounts = $paymentMethodCounts->mapWithKeys(function ($total, $method) {
            return [
                match ($method) {
                    'tunai' => 'Tunai',
                    default => 'Non Tunai',
                } => $total,
            ];
        });
        $paymentChartData = [
            'labels' => $paymentMethodCounts->keys(),
            'data' => $paymentMethodCounts->values(),
        ];

        // Data untuk pie chart metode penjualan
        $salesMethodTotals = $transactions->groupBy('method')->map(fn($g) => $g->sum('total_amount'));
        $salesMethodNames = $salesMethodTotals->keys()->transform(function ($method) {
            return match ($method) {
                'pesanan-reguler' => 'Pesanan Reguler',
                'pesanan-kotak' => 'Pesanan Kotak',
                'siap-beli' => 'Siap Saji',
                default => 'Siap Saji',
            };
        });
        $salesChartData = [
            'labels' => $salesMethodNames,
            'data' => $salesMethodTotals->values(),
        ];

        $this->topProductsChartData = $topProductsChartData ?? ['labels' => [], 'data' => []];
        $this->paymentChartData = $paymentChartData ?? ['labels' => [], 'data' => []];
        $this->salesChartData = $salesChartData ?? ['labels' => [], 'data' => []];
    }

    /**
     * Get empty state data for initial loading
     */
    protected function getEmptyState(): array
    {
        $emptyPaginator = new LengthAwarePaginator([], 0, $this->perPage, 1, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);

        return [
            'sessionCount' => 0,
            'transactionCount' => 0,
            'customerCount' => 0,
            'productSold' => 0,
            'topProducts' => [],
            'bestProduct' => null,
            'worstProduct' => null,
            'diffStats' => [
                'sessionCount' => 0,
                'transactionCount' => 0,
                'customerCount' => 0,
                'productSold' => 0,
                'best' => 0,
                'worst' => 0,
                'grossRevenue' => 0,
                'discount' => 0,
                'refund' => 0,
                'netRevenue' => 0,
                'profit' => 0,
            ],
            'topProductsChartData' => ['labels' => [], 'data' => []],
            'paymentChartData' => ['labels' => [], 'data' => []],
            'salesChartData' => ['labels' => [], 'data' => []],
            'chartRevenue' => ['labels' => [], 'data' => []],
            'grossRevenue' => 0,
            'discountTotal' => 0,
            'refundTotal' => 0,
            'netRevenue' => 0,
            'profit' => 0,
            'productSalesPaginator' => $emptyPaginator,
            'productSalesHeaders' => [
                ['label' => 'Produk', 'class' => 'text-left text-[#fff]'],
                ['label' => 'Produksi', 'class' => 'text-center text-[#fff]'],
                ['label' => 'Terjual', 'class' => 'text-center text-[#fff]'],
                ['label' => 'Tidak Terjual', 'class' => 'text-center text-[#fff]'],
            ],
            'monthlyReportsPaginator' => $emptyPaginator,
            'monthlyReportsHeaders' => [
                ['label' => 'Waktu', 'class' => 'text-left text-[#fff]'],
                ['label' => 'Pendapatan Kotor', 'class' => 'text-center text-[#fff]'],
                ['label' => 'Refund', 'class' => 'text-center text-[#fff]'],
                ['label' => 'Potongan Harga', 'class' => 'text-center text-[#fff]'],
                ['label' => 'Pendapatan Bersih', 'class' => 'text-center text-[#fff]'],
                ['label' => 'Modal', 'class' => 'text-center text-[#fff]'],
                ['label' => 'Keuntungan', 'class' => 'text-center text-[#fff]'],
            ],
        ];
    }

    public function render()
    {
        // Return empty state while loading
        if (! $this->readyToLoad) {
            return view('livewire.dashboard.laporan-kasir', $this->getEmptyState());
        }

        // Determine date range based on filter period
        if ($this->filterPeriod === 'Custom' && $this->customStartDate) {
            $startDate = Carbon::parse($this->customStartDate)->startOfDay();
            $endDate = $this->customEndDate ? Carbon::parse($this->customEndDate)->endOfDay() : Carbon::parse($this->customStartDate)->endOfDay();
            $lengthDays = Carbon::parse($this->customStartDate)->diffInDays(Carbon::parse($this->customEndDate ?? $this->customStartDate)) + 1;
            $prevStart = Carbon::parse($this->customStartDate)->subDays($lengthDays)->startOfDay();
            $prevEnd = Carbon::parse($this->customStartDate)->subDay()->endOfDay();
        } else {
            $selectedDate = Carbon::parse($this->selectedDate);

            switch ($this->filterPeriod) {
                case 'Hari':
                    $startDate = $selectedDate->copy()->startOfDay();
                    $endDate = $selectedDate->copy()->endOfDay();
                    $prevStart = $selectedDate->copy()->subDay()->startOfDay();
                    $prevEnd = $selectedDate->copy()->subDay()->endOfDay();
                    break;
                case 'Minggu':
                    $startDate = $selectedDate->copy()->startOfWeek()->startOfDay();
                    $endDate = $selectedDate->copy()->endOfWeek()->endOfDay();
                    $prevStart = $selectedDate->copy()->subWeek()->startOfWeek()->startOfDay();
                    $prevEnd = $selectedDate->copy()->subWeek()->endOfWeek()->endOfDay();
                    break;
                case 'Bulan':
                    $startDate = $selectedDate->copy()->startOfMonth()->startOfDay();
                    $endDate = $selectedDate->copy()->endOfMonth()->endOfDay();
                    $prevStart = $selectedDate->copy()->subMonth()->startOfMonth()->startOfDay();
                    $prevEnd = $selectedDate->copy()->subMonth()->endOfMonth()->endOfDay();
                    break;
                case 'Tahun':
                    $startDate = $selectedDate->copy()->startOfYear()->startOfDay();
                    $endDate = $selectedDate->copy()->endOfYear()->endOfDay();
                    $prevStart = $selectedDate->copy()->subYear()->startOfYear()->startOfDay();
                    $prevEnd = $selectedDate->copy()->subYear()->endOfYear()->endOfDay();
                    break;
                default:
                    $startDate = $selectedDate->copy()->startOfDay();
                    $endDate = $selectedDate->copy()->endOfDay();
                    $prevStart = $selectedDate->copy()->subDay()->startOfDay();
                    $prevEnd = $selectedDate->copy()->subDay()->endOfDay();
            }
        }

        // Query transactions
        $transactionsQuery = Transaction::whereBetween('start_date', [$startDate, $endDate])
            ->where('status', 'Selesai');

        if ($this->selectedWorker !== 'semua') {
            $transactionsQuery->where('user_id', $this->selectedWorker);
        }

        if ($this->selectedMethod !== 'semua') {
            $transactionsQuery->where('method', $this->selectedMethod);
        }

        $this->transactions = $transactionsQuery->get();
        $transactions = $this->transactions;

        // Prev transactions
        $prevTransactionsQuery = Transaction::whereBetween('start_date', [$prevStart, $prevEnd])
            ->where('status', 'Selesai');

        if ($this->selectedWorker !== 'semua') {
            $prevTransactionsQuery->where('user_id', $this->selectedWorker);
        }

        if ($this->selectedMethod !== 'semua') {
            $prevTransactionsQuery->where('method', $this->selectedMethod);
        }

        $this->prevTransactions = $prevTransactionsQuery->get();
        $prevTransactions = $this->prevTransactions;

        $transactionIds = $transactions->pluck('id');
        $prevTransactionIds = $prevTransactions->pluck('id');

        $this->details = TransactionDetail::with('product')
            ->whereIn('transaction_id', $transactionIds)
            ->get();
        $details = $this->details;

        $prevDetails = TransactionDetail::with('product')
            ->whereIn('transaction_id', $prevTransactionIds)
            ->get();

        $this->updateChartData($transactions, $details);

        $groupedProducts = $details->groupBy('product_id')->map(function ($items) {
            $total = $items->sum(fn($d) => $d->quantity - $d->refund_quantity);

            return [
                'total' => $total,
                'name' => $items->first()->product->name ?? 'Unknown',
            ];
        });

        $sorted = $groupedProducts->sortByDesc('total');

        $top10 = $sorted->take(10);
        $best = $sorted->first();

        $prevBest = $prevDetails->groupBy('product_id')->map(function ($items) {
            $total = $items->sum(fn($d) => $d->quantity - $d->refund_quantity);

            return [
                'total' => $total,
                'name' => $items->first()->product->name ?? 'Unknown',
            ];
        })->sortByDesc('total')->first();

        $worst = $sorted->filter(fn($p) => $p['total'] > 0)->sortBy('total')->first();

        $prevWorst = $prevDetails->groupBy('product_id')->map(function ($items) {
            $total = $items->sum(fn($d) => $d->quantity - $d->refund_quantity);

            return [
                'total' => $total,
                'name' => $items->first()->product->name ?? 'Unknown',
            ];
        })->filter(fn($p) => $p['total'] > 0)->sortBy('total')->first();

        $sessionCount = $transactions->unique('created_by_shift')->count();
        $prevSessionCount = $prevTransactions->unique('created_by_shift')->count();

        $transactionCount = $transactions->count();
        $prevTransactionCount = $prevTransactions->count();

        $customerCount = $transactions->unique('phone')->count();
        $prevCustomerCount = $prevTransactions->unique('phone')->count();

        $productSold = $details->sum(fn($d) => $d->quantity - $d->refund_quantity);
        $prevProductSold = $prevDetails->sum(fn($d) => $d->quantity - $d->refund_quantity);

        // === Pendapatan dan Statistik Penjualan ===
        $grossRevenue = $transactions->sum('total_amount');
        $prevGrossRevenue = $prevTransactions->sum('total_amount');

        // Potongan harga dari points_discount
        $discountTotal = $transactions->sum('points_discount');
        $prevDiscountTotal = $prevTransactions->sum('points_discount');

        $refundTotal = $transactions->sum('total_refund');
        $prevRefundTotal = $prevTransactions->sum('total_refund');

        $netRevenue = $grossRevenue - $refundTotal - $discountTotal;
        $prevNetRevenue = $prevGrossRevenue - $prevRefundTotal - $prevDiscountTotal;

        // Calculate profit/keuntungan
        $capitalTotal = $details->sum(function ($d) {
            return ($d->pcs_capital_snapshot ?? 0) * ($d->quantity - $d->refund_quantity);
        });
        $prevCapitalTotal = $prevDetails->sum(function ($d) {
            return ($d->pcs_capital_snapshot ?? 0) * ($d->quantity - $d->refund_quantity);
        });
        $profit = $netRevenue - $capitalTotal;
        $prevProfit = $prevNetRevenue - $prevCapitalTotal;

        // === Data Bulanan untuk chart (semua tipe) ===
        $chartGross = [];
        $chartDiscount = [];
        $chartRefund = [];
        $chartNet = [];
        $chartProfit = [];

        foreach (range(1, 12) as $month) {
            $monthTransactions = $transactions->filter(fn($trx) => Carbon::parse($trx->start_date)->month === $month);
            $monthDetails = $details->filter(function ($d) use ($month, $transactions) {
                $trx = $transactions->firstWhere('id', $d->transaction_id);

                return $trx && Carbon::parse($trx->start_date)->month === $month;
            });

            $gross = $monthTransactions->sum('total_amount');
            $refund = $monthTransactions->sum('total_refund');
            $discount = $monthTransactions->sum('points_discount');
            $net = $gross - $refund - $discount;
            $modal = $monthDetails->sum(fn($d) => ($d->pcs_capital_snapshot ?? 0) * ($d->quantity - $d->refund_quantity));
            $monthProfit = $net - $modal;

            $chartGross[] = $gross;
            $chartDiscount[] = $discount;
            $chartRefund[] = $refund;
            $chartNet[] = $net;
            $chartProfit[] = $monthProfit;
        }

        // Set chart data berdasarkan selectedChart
        $this->chartRevenue = match ($this->selectedChart) {
            'gross' => $chartGross,
            'discount' => $chartDiscount,
            'refund' => $chartRefund,
            'net' => $chartNet,
            'profit' => $chartProfit,
            default => $chartGross,
        };

        // Product sales data for table
        $products = Product::all();
        $productSales = $products->map(function ($product) use ($details, $startDate, $endDate) {
            $terjual = $details->where('product_id', $product->id)->sum(fn($d) => $d->quantity - $d->refund_quantity);

            // Get production count from productions table using start_date
            $produksi = \App\Models\ProductionDetail::whereHas('production', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate]);
            })
                ->where('product_id', $product->id)
                ->sum('quantity_get');

            $tidakTerjual = max(0, $produksi - $terjual);

            return (object) [
                'name' => $product->name,
                'produksi' => $produksi,
                'sold' => $terjual,
                'unsold' => $tidakTerjual,
            ];
        })->sortByDesc('sold')->values();

        // Filter product sales by search term
        if ($this->searchProduct) {
            $productSales = $productSales->filter(fn($item) => stripos($item->name, $this->searchProduct) !== false)->values();
        }

        // Monthly reports for Rincian Penjualan table
        $monthlyReports = [];
        foreach (range(1, 12) as $month) {
            $monthTransactions = $transactions->filter(fn($trx) => Carbon::parse($trx->start_date)->month === $month);
            $monthDetails = $details->filter(function ($d) use ($month, $transactions) {
                $trx = $transactions->firstWhere('id', $d->transaction_id);

                return $trx && Carbon::parse($trx->start_date)->month === $month;
            });

            $penjualan = $monthTransactions->sum('total_amount');
            $refund = $monthTransactions->sum('total_refund');
            $potonganHarga = $monthTransactions->sum('points_discount');
            $pendapatanBersih = $penjualan - $refund - $potonganHarga;
            $modal = $monthDetails->sum(function ($d) {
                return ($d->pcs_capital_snapshot ?? 0) * ($d->quantity - $d->refund_quantity);
            });

            $keuntungan = $pendapatanBersih - $modal;

            $monthName = Carbon::create()->month($month)->year(Carbon::parse($this->selectedDate)->year)->translatedFormat('d M Y');
            if ($penjualan > 0 || $refund > 0) {
                $monthlyReports[] = (object) [
                    'waktu' => $monthName,
                    'pendapatanKotor' => $penjualan,
                    'refund' => $refund,
                    'potonganHarga' => $potonganHarga,
                    'pendapatanBersih' => $pendapatanBersih,
                    'modal' => $modal,
                    'keuntungan' => $keuntungan,
                ];
            }
        }

        // Filter monthly reports by search term
        if ($this->searchReport) {
            $monthlyReports = collect($monthlyReports)->filter(fn($item) => stripos($item->waktu, $this->searchReport) !== false)->values()->toArray();
        }

        $this->diffStats = [
            'sessionCount' => $this->calculateDiff($sessionCount, $prevSessionCount),
            'transactionCount' => $this->calculateDiff($transactionCount, $prevTransactionCount),
            'customerCount' => $this->calculateDiff($customerCount, $prevCustomerCount),
            'productSold' => $this->calculateDiff($productSold, $prevProductSold),
            'best' => $this->calculateDiff($best['total'] ?? 0, $prevBest['total'] ?? 0),
            'worst' => $this->calculateDiff($worst['total'] ?? 0, $prevWorst['total'] ?? 0),
            'grossRevenue' => $this->calculateDiff($grossRevenue, $prevGrossRevenue),
            'discount' => $this->calculateDiff($discountTotal, $prevDiscountTotal),
            'refund' => $this->calculateDiff($refundTotal, $prevRefundTotal),
            'netRevenue' => $this->calculateDiff($netRevenue, $prevNetRevenue),
            'profit' => $this->calculateDiff($profit, $prevProfit),
        ];

        // Dispatch chart update if needed
        if ($this->shouldUpdateChart) {
            $this->dispatch('update-charts', [
                'topProductsChartData' => $this->topProductsChartData,
                'paymentChartData' => $this->paymentChartData,
                'salesChartData' => $this->salesChartData,
                'chartRevenue' => $this->chartRevenue,
            ]);
            $this->shouldUpdateChart = false;
        }

        // Create paginators for tables
        $totalProductSales = $productSales->count();
        $currentProductSalesItems = $productSales->slice(($this->currentPage - 1) * $this->perPage, $this->perPage)->values();
        $productSalesPaginator = new LengthAwarePaginator($currentProductSalesItems, $totalProductSales, $this->perPage, $this->currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);

        $totalMonthlyReports = count($monthlyReports);
        $currentMonthlyItems = collect($monthlyReports)->slice(($this->currentPage - 1) * $this->perPage, $this->perPage)->values();
        $monthlyReportsPaginator = new LengthAwarePaginator($currentMonthlyItems, $totalMonthlyReports, $this->perPage, $this->currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);

        $productSalesHeaders = [
            ['label' => 'Produk', 'class' => 'text-left text-[#fff]'],
            ['label' => 'Produksi', 'class' => 'text-center text-[#fff]'],
            ['label' => 'Terjual', 'class' => 'text-center text-[#fff]'],
            ['label' => 'Tidak Terjual', 'class' => 'text-center text-[#fff]'],
        ];

        $monthlyReportsHeaders = [
            ['label' => 'Waktu', 'class' => 'text-left text-[#fff]'],
            ['label' => 'Pendapatan Kotor', 'class' => 'text-center text-[#fff]'],
            ['label' => 'Refund', 'class' => 'text-center text-[#fff]'],
            ['label' => 'Potongan Harga', 'class' => 'text-center text-[#fff]'],
            ['label' => 'Pendapatan Bersih', 'class' => 'text-center text-[#fff]'],
            ['label' => 'Modal', 'class' => 'text-center text-[#fff]'],
            ['label' => 'Keuntungan', 'class' => 'text-center text-[#fff]'],
        ];

        return view('livewire.dashboard.laporan-kasir', [
            'sessionCount' => $sessionCount,
            'transactionCount' => $transactionCount,
            'customerCount' => $customerCount,
            'productSold' => $productSold,
            'topProducts' => $top10,
            'bestProduct' => $best,
            'worstProduct' => $worst,
            'diffStats' => $this->diffStats,
            'topProductsChartData' => $this->topProductsChartData,
            'paymentChartData' => $this->paymentChartData,
            'salesChartData' => $this->salesChartData,
            'chartRevenue' => $this->chartRevenue,
            'grossRevenue' => $grossRevenue,
            'discountTotal' => $discountTotal,
            'refundTotal' => $refundTotal,
            'netRevenue' => $netRevenue,
            'profit' => $profit,
            'productSalesPaginator' => $productSalesPaginator,
            'productSalesHeaders' => $productSalesHeaders,
            'monthlyReportsPaginator' => $monthlyReportsPaginator,
            'monthlyReportsHeaders' => $monthlyReportsHeaders,
        ]);
    }
}
