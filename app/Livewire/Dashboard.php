<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Production;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Livewire\Component;
use Livewire\WithPagination;

class Dashboard extends Component
{
    use WithPagination;

    public function mount()
    {
        View::share('title', 'Dashboard');
    }

    public function render()
    {
        $transactions = Transaction::whereBetween('start_date', [now()->subDays(30), now()])
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->start_date)->format('d M');
            });
        $today_sales = Transaction::whereDate('start_date', now())
            ->where('payment_status', '=', 'Lunas')
            ->sum('total_amount');
        $today_sales_belum_lunas = Transaction::whereDate('start_date', now())
            ->where('payment_status', '!=', 'Lunas')
            ->whereNotIn('status', ['Draft', 'temp'])
            ->withSum('payments', 'paid_amount')
            ->get()
            ->sum('payments_sum_paid_amount');
        $monthly_revenue = Transaction::whereMonth('start_date', now()->month)
            ->where('payment_status', '=', 'Lunas')
            ->sum('total_amount');
        $monthly_revenue_belum_lunas = Transaction::whereMonth('start_date', now()->month)
            ->where('payment_status', '!=', 'Lunas')
            ->whereNotIn('status', ['Draft', 'temp'])
            ->withSum('payments', 'paid_amount')
            ->get()
            ->sum('payments_sum_paid_amount');

        return view('dashboard', [
            'stats' => [
                'today_sales' => $today_sales + $today_sales_belum_lunas,
                'monthly_revenue' => $monthly_revenue + $monthly_revenue_belum_lunas,
                'pending_orders' => Transaction::where('status', 'Belum Diproses')->count(),
                'completed_productions' => Production::where('status', 'Selesai')->count(),
            ],
            'transactions' => Transaction::latest()
                ->with('user')
                ->paginate(5, pageName: 'transactions-page'),
            'transactions_chart' => $transactions->map(function ($item, $key) {
                return [
                    'date' => $key,
                    'total' => $item->sum('total_amount'),
                ];
            })->values()->toJson(),
            'productions' => Production::with('details.product')
                ->whereIn('status', ['Sedang Diproses', 'pending'])
                ->latest()
                ->limit(5)
                ->get(),
            'topSellingProducts' => TransactionDetail::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
                ->groupBy('product_id')
                ->orderBy('total_quantity', 'desc')
                ->with('product')
                ->take(5)
                ->get(),
            'lowStockProducts' => Product::where('stock', '<', 10)->where('stock', '>', 0)
                ->get(),
            'latestOrders' => Transaction::orderBy('start_date', 'desc')
                ->take(5)
                ->get(),
            'totalSalesToday' => Transaction::whereDate('start_date', today())->sum('total_amount'),
        ]);
    }
}
