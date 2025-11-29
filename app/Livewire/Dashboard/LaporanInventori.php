<?php

namespace App\Livewire\Dashboard;

use App\Models\Expense;
use App\Models\ExpenseDetail;
use App\Models\Material;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Livewire\Component;

class LaporanInventori extends Component
{
    use \Livewire\WithPagination;

    public $currentPage = 1;

    public $perPage = 10;

    public $selectedYear;

    public $selectedMethod = 'semua';

    public $expenses;

    public $prevExpenses;

    public $details;

    public $diffStats = [];

    public $topMaterialChartData = [];

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

        $this->expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->get();
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
        $totalPrice = [];
        $usedGrandTotal = 0;
        foreach ($groupProductByMaterial as $materialId => $compositions) {
            foreach ($compositions as $composition) {
                $productId = $composition['product_id'];
                $productionDetails = \App\Models\ProductionDetail::where('product_id', $productId)->get();
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
            $material = Material::find($materialId);
            $label = $material?->name ?? 'Unknown';

            $topMaterialChartData['labels'][] = $label;
            $topMaterialChartData['data'][] = $sumPrice;
        }

        $this->topMaterialChartData = $topMaterialChartData;

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

        $this->diffStats = [
            'totalExpense' => $this->calculateDiff($totalExpense, $prevTotalExpense),
            'grandTotal' => $this->calculateDiff($grandTotal, $grandTotal),
            'usedGrandTotal' => $this->calculateDiff($usedGrandTotal, $usedGrandTotal),
            'remainGrandTotal' => $this->calculateDiff($remainGrandTotal, $prevRemainGrandTotal),
            'best' => $this->calculateDiff($best['total'] ?? 0, $prevBest['total'] ?? 0),
            'worst' => $this->calculateDiff($worst['total'] ?? 0, $prevWorst['total'] ?? 0),
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
            'materialTables' => $materialTables->slice(($this->currentPage - 1) * $this->perPage, $this->perPage),
            'totalProductSales' => $materialTables->count(),
            'totalPages' => ceil($materialTables->count() / $this->perPage),
        ]);
    }
}
