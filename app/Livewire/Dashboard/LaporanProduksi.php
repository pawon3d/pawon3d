<?php

namespace App\Livewire\Dashboard;

use App\Models\Product;
use App\Models\Production;
use App\Models\ProductionDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Livewire\Component;

class LaporanProduksi extends Component
{
    use \Livewire\WithPagination;
    public $currentPage = 1;
    public $perPage = 10;

    public $selectedYear;
    public $selectedMethod = 'semua';
    public $productions;
    public $prevProductions;
    public $details;

    public $diffStats = [];
    public $topProductionsChartData = [];
    public $productionChartData = [];

    protected $listeners = ['refreshCharts' => '$refresh', 'update-top-products'];
    protected $queryString = [
        'selectedMethod' => ['except' => 'semua'],
        'currentPage' => ['except' => 1],
    ];

    public function mount()
    {
        $this->selectedYear = $this->selectedYear ?? now()->year;
        View::share('title', 'Laporan Produksi');
        View::share('mainTitle', 'Dashboard');
    }

    public function updatedSelectedMethod()
    {
        $startDate = Carbon::create($this->selectedYear)->startOfYear();
        $endDate = Carbon::create($this->selectedYear)->endOfYear();

        $this->productions = Production::whereBetween('start_date', [$startDate, $endDate])
            ->when($this->selectedMethod !== 'semua', fn($q) => $q->where('method', $this->selectedMethod))
            ->where('is_finish', true)
            ->get();

        $productionIds = $this->productions->pluck('id');

        $this->details = ProductionDetail::with('product')
            ->whereIn('production_id', $productionIds)
            ->get();

        $this->updateChartData($this->productions, $this->details);

        $this->dispatch('update-charts', [
            'topProductionsChartData' => $this->topProductionsChartData,
            'productionChartData' => $this->productionChartData,
        ]);
    }

    public function updatedSelectedYear()
    {
        $this->updatedSelectedMethod();
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

        // Data untuk pie chart metode penjualan
        $methodCounts = $productions->groupBy('method')->map->count();
        $methodNames = $methodCounts->keys()->transform(function ($method) {
            return match ($method) {
                'pesanan-reguler' => 'Pesanan Reguler',
                'pesanan-kotak' => 'Pesanan Kotak',
                default => 'Siap Saji',
            };
        });
        $productionChartData = [
            'labels' => $methodNames,
            'data' => $methodCounts->values(),
        ];
        $this->topProductionsChartData = $topProductionsChartData ?? ['labels' => [], 'data' => []];
        $this->productionChartData = $productionChartData ?? ['labels' => [], 'data' => []];
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

        $this->productions = Production::whereBetween('start_date', [$startDate, $endDate])
            ->when($this->selectedMethod !== 'semua', fn($q) => $q->where('method', $this->selectedMethod))
            ->where('is_finish', true)
            ->get();
        $productions = $this->productions;

        $this->prevProductions = Production::whereBetween('start_date', [$prevStart, $prevEnd])
            ->when($this->selectedMethod !== 'semua', fn($q) => $q->where('method', $this->selectedMethod))
            ->where('is_finish', true)
            ->get();
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
            return (object)[
                'name' => $product->name,
                'total' => $total,
                'success' => $berhasil,
                'fail' => $gagal,
            ];
        })->sortByDesc('total')->values();

        $this->diffStats = [
            'successProduction' => $this->calculateDiff($successProduction, $prevSuccessProduction),
            'failedProduction' => $this->calculateDiff($failedProduction, $prevFailedProduction),
            'totalProduction' => $this->calculateDiff($totalProduction, $prevTotalProduction),
            'best' => $this->calculateDiff($best['total'] ?? 0, $prevBest['total'] ?? 0),
            'worst' => $this->calculateDiff($worst['total'] ?? 0, $prevWorst['total'] ?? 0),
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
            'productionProducts' => $productionProducts->slice(($this->currentPage - 1) * $this->perPage, $this->perPage),
            'totalProductSales' => $productionProducts->count(),
            'totalPages' => ceil($productionProducts->count() / $this->perPage),
        ]);
    }
}
