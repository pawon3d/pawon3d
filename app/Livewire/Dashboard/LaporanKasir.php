<?php

namespace App\Livewire\Dashboard;

use App\Models\Payment;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Livewire\Component;

class LaporanKasir extends Component
{
    use \Livewire\WithPagination;

    public $currentPage = 1;

    public $perPage = 10;

    public $selectedYear;

    public $selectedMethod = 'semua';

    public $selectedChart = 'gross';

    public $transactions;

    public $prevTransactions;

    public $details;

    public $diffStats = [];

    public $topProductsChartData = [];

    public $paymentChartData = [];

    public $salesChartData = [];

    public $chartRevenue = [];

    protected $listeners = ['refreshCharts' => '$refresh', 'update-top-products'];

    protected $queryString = [
        'selectedMethod' => ['except' => 'semua'],
        'currentPage' => ['except' => 1],
    ];

    public function mount()
    {
        View::share('title', 'Laporan Kasir');
        View::share('mainTitle', 'Dashboard');
    }

    public function updatedSelectedMethod()
    {
        $startDate = Carbon::create($this->selectedYear)->startOfYear();
        $endDate = Carbon::create($this->selectedYear)->endOfYear();

        $this->transactions = Transaction::whereBetween('start_date', [$startDate, $endDate])
            ->when($this->selectedMethod !== 'semua', fn ($q) => $q->where('method', $this->selectedMethod))
            ->get();

        $transactionIds = $this->transactions->pluck('id');

        $this->details = TransactionDetail::with('product')
            ->whereIn('transaction_id', $transactionIds)
            ->get();

        $this->updateChartData($this->transactions, $this->details);

        $this->dispatch('update-charts', [
            'topProductsChartData' => $this->topProductsChartData,
            'paymentChartData' => $this->paymentChartData,
            'salesChartData' => $this->salesChartData,
            'chartRevenue' => $this->chartRevenue,
        ]);
    }

    public function updatedSelectedYear()
    {
        $this->updatedSelectedMethod();
    }

    public function updatedSelectedChart()
    {
        $this->dispatch('update-charts', [
            'chartRevenue' => $this->chartRevenue,
        ]);
    }

    protected function updateChartData($transactions, $details)
    {

        $groupedProducts = $details->groupBy('product_id')->map(function ($items) {
            $total = $items->sum(fn ($d) => $d->quantity - $d->refund_quantity);

            return [
                'total' => $total,
                'name' => $items->first()->product->name ?? 'Unknown',
            ];
        });

        $sorted = $groupedProducts->sortByDesc('total');

        $top10 = $sorted->take(10);
        // Data untuk chart topProductsChart
        $topProductsChartData = [
            'labels' => $top10->pluck('name')->values(),
            'data' => $top10->pluck('total')->values(),
        ];

        // Data untuk pie chart metode pembayaran
        $paymentMethodCounts = Payment::whereIn('transaction_id', $transactions->pluck('id'))
            ->groupBy('payment_method')
            ->selectRaw('payment_method, count(*) as count')
            ->pluck('count', 'payment_method');
        $paymentMethodCounts = $paymentMethodCounts->mapWithKeys(function ($count, $method) {
            return [
                match ($method) {
                    'tunai' => 'Tunai',
                    'transfer' => 'Non Tunai',
                    'qris' => 'QRIS',
                    default => 'Lainnya',
                } => $count,
            ];
        });
        $paymentChartData = [
            'labels' => $paymentMethodCounts->keys(),
            'data' => $paymentMethodCounts->values(),
        ];

        // Data untuk pie chart metode penjualan
        $salesMethodCounts = $transactions->groupBy('method')->map->count();
        $salesMethodNames = $salesMethodCounts->keys()->transform(function ($method) {
            return match ($method) {
                'pesanan-reguler' => 'Pesanan Reguler',
                'pesanan-kotak' => 'Pesanan Kotak',
                'siap-beli' => 'Siap Saji',
                default => 'Siap Saji',
            };
        });
        $salesChartData = [
            'labels' => $salesMethodNames,
            'data' => $salesMethodCounts->values(),
        ];

        // === Data Bulanan ===
        $monthlyReports = [];
        $chartRevenue = [];
        foreach (range(1, 12) as $month) {
            $monthTransactions = $transactions->filter(fn ($trx) => Carbon::parse($trx->date)->month === $month);
            $monthDetails = $details->filter(function ($d) use ($month, $transactions) {
                $trx = $transactions->firstWhere('id', $d->transaction_id);

                return $trx && Carbon::parse($trx->date)->month === $month;
            });

            $penjualan = $monthTransactions->sum('total_amount');
            $refund = $monthTransactions->sum('total_refund');
            $diskon = $monthDetails->sum(function ($d) {
                return $d->price * $d->refund_quantity; // asumsi diskon dari quantity yang direfund
            });
            $modal = $monthDetails->sum(function ($d) {
                return ($d->product->pcs_capital ?? 0) * ($d->quantity - $d->refund_quantity);
            });

            $keuntungan = $penjualan - $modal;

            $monthName = Carbon::create()->month($month)->year(Carbon::now()->year)->translatedFormat('M Y');
            $monthlyReports[$monthName] = compact('penjualan', 'refund', 'diskon', 'modal', 'keuntungan');
            $chartRevenue[] = $penjualan;
        }
        $this->topProductsChartData = $topProductsChartData ?? ['labels' => [], 'data' => []];
        $this->paymentChartData = $paymentChartData ?? ['labels' => [], 'data' => []];
        $this->salesChartData = $salesChartData ?? ['labels' => [], 'data' => []];
        $this->chartRevenue = $chartRevenue ?? [];
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

    public function render()
    {
        $startDate = Carbon::create($this->selectedYear)->startOfYear();
        $endDate = Carbon::create($this->selectedYear)->endOfYear();

        $prevStart = $startDate->copy()->subYear();
        $prevEnd = $endDate->copy()->subYear();

        $this->transactions = Transaction::whereBetween('start_date', [$startDate, $endDate])
            ->when($this->selectedMethod !== 'semua', fn ($q) => $q->where('method', $this->selectedMethod))
            ->get();
        $transactions = $this->transactions;

        $this->prevTransactions = Transaction::whereBetween('start_date', [$prevStart, $prevEnd])
            ->when($this->selectedMethod !== 'semua', fn ($q) => $q->where('method', $this->selectedMethod))
            ->get();
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
        // $this->dispatch('update-top-products', [
        //     'labels' => $this->topProductsChartData['labels'],
        //     'data' => $this->topProductsChartData['data'],
        // ]);
        // $this->dispatch('update-top-products', $this->topProductsChartData);

        $groupedProducts = $details->groupBy('product_id')->map(function ($items) {
            $total = $items->sum(fn ($d) => $d->quantity - $d->refund_quantity);

            return [
                'total' => $total,
                'name' => $items->first()->product->name ?? 'Unknown',
            ];
        });

        $sorted = $groupedProducts->sortByDesc('total');

        $top10 = $sorted->take(10);
        $best = $sorted->first();

        $prevBest = $prevDetails->groupBy('product_id')->map(function ($items) {
            $total = $items->sum(fn ($d) => $d->quantity - $d->refund_quantity);

            return [
                'total' => $total,
                'name' => $items->first()->product->name ?? 'Unknown',
            ];
        })->sortByDesc('total')->first();

        $worst = $sorted->filter(fn ($p) => $p['total'] > 0)->sortBy('total')->first();

        $prevWorst = $prevDetails->groupBy('product_id')->map(function ($items) {
            $total = $items->sum(fn ($d) => $d->quantity - $d->refund_quantity);

            return [
                'total' => $total,
                'name' => $items->first()->product->name ?? 'Unknown',
            ];
        })->filter(fn ($p) => $p['total'] > 0)->sortBy('total')->first();

        $sessionCount = $transactions->unique('created_by_shift')->count();
        $prevSessionCount = $prevTransactions->unique('created_by_shift')->count();

        $transactionCount = $transactions->count();
        $prevTransactionCount = $prevTransactions->count();

        $customerCount = $transactions->unique('phone')->count();
        $prevCustomerCount = $prevTransactions->unique('phone')->count();

        $productSold = $details->sum(fn ($d) => $d->quantity - $d->refund_quantity);
        $prevProductSold = $prevDetails->sum(fn ($d) => $d->quantity - $d->refund_quantity);

        // Data untuk chart topProductsChart
        $topProductsChartData = [
            'labels' => $top10->pluck('name')->values(),
            'data' => $top10->pluck('total')->values(),
        ];

        // Data untuk pie chart metode pembayaran
        $paymentMethodCounts = Payment::whereIn('transaction_id', $transactions->pluck('id'))
            ->groupBy('payment_method')
            ->selectRaw('payment_method, count(*) as count')
            ->pluck('count', 'payment_method');
        $paymentMethodCounts = $paymentMethodCounts->mapWithKeys(function ($count, $method) {
            return [
                match ($method) {
                    'tunai' => 'Tunai',
                    'transfer' => 'Non Tunai',
                    'qris' => 'QRIS',
                    default => 'Lainnya',
                } => $count,
            ];
        });
        $paymentChartData = [
            'labels' => $paymentMethodCounts->keys(),
            'data' => $paymentMethodCounts->values(),
        ];

        // Data untuk pie chart metode penjualan
        $salesMethodCounts = $transactions->groupBy('method')->map->count();
        $salesMethodNames = $salesMethodCounts->keys()->transform(function ($method) {
            return match ($method) {
                'pesanan-reguler' => 'Pesanan Reguler',
                'pesanan-kotak' => 'Pesanan Kotak',
                'siap-beli' => 'Siap Saji',
                default => 'Siap Saji',
            };
        });
        $salesChartData = [
            'labels' => $salesMethodNames,
            'data' => $salesMethodCounts->values(),
        ];

        // === Pendapatan dan Statistik Penjualan ===
        $grossRevenue = $transactions->sum('total_amount');
        $prevGrossRevenue = $prevTransactions->sum('total_amount');

        $discountTotal = $details->sum(function ($d) {
            return ($d->price * ($d->quantity ?? 0)) - ($d->price * max(0, ($d->quantity ?? 0) - ($d->refund_quantity ?? 0)));
        });

        $prevDiscountTotal = $prevDetails->sum(function ($d) {
            return ($d->price * ($d->quantity ?? 0)) - ($d->price * max(0, ($d->quantity ?? 0) - ($d->refund_quantity ?? 0)));
        });

        $refundTotal = $transactions->sum('total_refund');
        $prevRefundTotal = $prevTransactions->sum('total_refund');

        $netRevenue = $grossRevenue - $refundTotal;
        $prevNetRevenue = $prevGrossRevenue - $prevRefundTotal;

        // === Data Bulanan ===
        $monthlyReports = [];
        $chartRevenue = [];
        $modalTemp = [];
        foreach (range(1, 12) as $month) {
            $monthTransactions = $transactions->filter(fn ($trx) => Carbon::parse($trx->date)->month === $month);
            $monthDetails = $details->filter(function ($d) use ($month, $transactions) {
                $trx = $transactions->firstWhere('id', $d->transaction_id);

                return $trx && Carbon::parse($trx->date)->month === $month;
            });

            $penjualan = $monthTransactions->sum('total_amount');
            $refund = $monthTransactions->sum('total_refund');
            $diskon = $monthDetails->sum(function ($d) {
                return $d->price * $d->refund_quantity; // asumsi diskon dari quantity yang direfund
            });
            $modal = $monthDetails->sum(function ($d) {
                return ($d->product->pcs_capital ?? 0) * ($d->quantity - $d->refund_quantity);
            });
            // $modalTemp[$month] = $monthDetails;

            $keuntungan = $penjualan - $modal;

            $monthName = Carbon::create()->month($month)->year(Carbon::now()->year)->translatedFormat('M Y');
            $monthlyReports[$monthName] = compact('penjualan', 'refund', 'diskon', 'modal', 'keuntungan');
            $chartRevenue[] = $penjualan;
        }
        // dd($monthlyReports, $modalTemp);
        $products = Product::all();
        $productSales = $products->map(function ($product) use ($details) {
            $terjual = $details->where('product_id', $product->id)->sum(fn ($d) => $d->quantity - $d->refund_quantity);
            $tidakTerjual = $product->pcs > 0 ? max(0, $product->pcs - $terjual) : 0;

            return (object) [
                'name' => $product->name,
                'sold' => $terjual,
                'unsold' => $tidakTerjual,
            ];
        })->sortByDesc('sold')->values();

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
            'monthlyReports' => $monthlyReports,
            'productSales' => $productSales->slice(($this->currentPage - 1) * $this->perPage, $this->perPage),
            'totalProductSales' => $productSales->count(),
            'totalPages' => ceil($productSales->count() / $this->perPage),
        ]);
    }
}
