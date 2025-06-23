<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Product;
use Livewire\Component;
use App\Models\Transaction;
use App\Models\Production;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
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
            ->latest()->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->start_date)->format('d M');
            });


        return view('dashboard', [
            'stats' => [
                'today_sales' => Transaction::whereDate('start_date', today())->sum('total_amount'),
                'monthly_revenue' => Transaction::whereMonth('start_date', now()->month)->sum('total_amount'),
                'pending_orders' => Transaction::where('status', 'pending')->count(),
                'completed_productions' => Production::where('status', 'Selesai')->count()
            ],
            'transactions' => Transaction::latest()
                ->with('user')
                ->paginate(5, pageName: 'transactions-page'),
            'transactions_chart' => $transactions->map(function ($item, $key) {
                return [
                    'date' => $key,
                    'total' => $item->sum('total_amount')
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
            'lowStockProducts' => Product::where('stock', '<', 10)
                ->get(),
            'latestOrders' => Transaction::orderBy('start_date', 'desc')
                ->take(5)
                ->get(),
            'totalSalesToday' => Transaction::whereDate('start_date', today())->sum('total_amount'),
        ]);
    }
}
