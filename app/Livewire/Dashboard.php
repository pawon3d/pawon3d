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
        $transactions = Transaction::whereBetween('created_at', [now()->subDays(30), now()])
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('d M');
            });


        return view('dashboard', [
            'stats' => [
                'today_sales' => Transaction::whereDate('created_at', today())->sum('total_amount'),
                'monthly_revenue' => Transaction::whereMonth('created_at', now()->month)->sum('total_amount'),
                'pending_orders' => Transaction::where('status', 'pending')->count(),
                'completed_productions' => Production::where('status', 'completed')->count()
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
            'productions' => Production::with('product')
                ->whereIn('status', ['processing', 'pending'])
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
            'latestOrders' => Transaction::orderBy('created_at', 'desc')
                ->take(5)
                ->get(),
            'totalSalesToday' => Transaction::whereDate('created_at', today())->sum('total_amount'),
        ]);
    }
}