<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use App\Models\ExpenseDetail;
use App\Models\IngredientCategory;
use App\Models\Material;
use App\Models\MaterialDetail;
use App\Models\Product;
use App\Models\Production;
use App\Models\ProductionDetail;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class PdfController extends Controller
{
    public function print($id)
    {
        $transaction = \App\Models\Transaction::with(['user', 'details.product'])->find($id);

        $pdf = Pdf::loadView('pdf.pdf', compact('transaction'))->setPaper([0, 0, 227, 400], 'portrait');

        return $pdf->stream();
    }

    public function printReport(Request $request)
    {
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="laporan-transaksi.pdf"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        // Ambil filter dari request
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');
        $search = $request->get('search');
        $typeFilter = $request->get('typeFilter');
        $paymentStatusFilter = $request->get('paymentStatusFilter');

        $query = Transaction::with(['user', 'details.product']);

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        if ($typeFilter && $typeFilter !== 'all') {
            $query->where('type', $typeFilter);
        }

        if ($paymentStatusFilter && $paymentStatusFilter !== 'all') {
            $query->where('payment_status', $paymentStatusFilter);
        }

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $transactions = $query->get();

        // Susun rentang tanggal untuk laporan
        if ($startDate && $endDate) {
            $range = Carbon::parse($startDate)->translatedFormat('d F Y') . ' sampai ' . Carbon::parse($endDate)->translatedFormat('d F Y');
        } elseif (! $startDate && $endDate) {
            $earliest = Transaction::orderBy('created_at', 'asc')->value('created_at');
            $earliest = $earliest ? Carbon::parse($earliest)->translatedFormat('d F Y') : 'Tidak ada data';
            $range = Carbon::parse($earliest)->translatedFormat('d F Y') . ' sampai ' . Carbon::parse($endDate)->translatedFormat('d F Y');
        } elseif ($startDate && ! $endDate) {
            $today = Carbon::now()->translatedFormat('d F Y');
            $range = Carbon::parse($startDate)->translatedFormat('d F Y') . ' sampai ' . $today;
        } else {
            // Jika tidak ada filter tanggal, tampilkan seluruh rentang berdasarkan data
            $earliest = Transaction::orderBy('created_at', 'asc')->value('created_at');
            $earliest = $earliest ? Carbon::parse($earliest)->translatedFormat('d F Y') : 'Tidak ada data';
            $today = Carbon::now()->translatedFormat('d F Y');
            $range = $earliest . ' sampai ' . $today;
        }

        // Data yang akan diteruskan ke view PDF
        $data = [
            'transactions' => $transactions,
            'dateRange' => $range,
        ];

        // Generate PDF dengan Dompdf
        $pdf = Pdf::loadView('pdf.transaction-report', $data);

        return $pdf->stream('laporan-transaksi.pdf');
    }

    public function generateCategoryPDF(Request $request)
    {
        $categories = Category::when($request->search, function ($query) use ($request) {
            return $query->where('name', 'like', '%' . $request->search . '%');
        })->with('products')
            ->withCount('products')
            ->orderBy('products_count', 'desc')
            ->get();

        $pdf = PDF::loadView('pdf.category', compact('categories'));

        return $pdf->download('daftar-kategori.pdf');
    }

    public function generateIngredientCategoryPDF(Request $request)
    {
        $categories = IngredientCategory::when($request->search, function ($query) use ($request) {
            return $query->where('name', 'like', '%' . $request->search . '%');
        })->get();

        $pdf = PDF::loadView('pdf.ingredient-category', compact('categories'));

        return $pdf->download('daftar-kategori-persediaan.pdf');
    }

    public function generateProductPDF(Request $request)
    {
        $products = Product::when($request->search, function ($query) use ($request) {
            return $query->where('name', 'like', '%' . $request->search . '%');
        })->with('category')->get();

        $pdf = PDF::loadView('pdf.product', compact('products'));

        return $pdf->download('daftar-produk.pdf');
    }

    public function generateSupplierPDF(Request $request)
    {
        $suppliers = Supplier::when($request->search, function ($query) use ($request) {
            return $query->where('name', 'like', '%' . $request->search . '%');
        })->get();

        $pdf = PDF::loadView('pdf.supplier', compact('suppliers'));

        return $pdf->download('daftar-toko-persediaan.pdf');
    }

    public function generateMaterialPDF(Request $request)
    {
        $materials = Material::when($request->search, function ($query) use ($request) {
            return $query->where('name', 'like', '%' . $request->search . '%');
        })->with('material_details')->get();

        foreach ($materials as $material) {
            $material->supply_quantity = MaterialDetail::where('material_id', $material->id)
                ->where('is_main', true)->with('unit')
                ->first()
                ?->supply_quantity ?? 'Tidak Tersedia';
            $material->unit_alias = MaterialDetail::where('material_id', $material->id)
                ->where('is_main', true)
                ->with('unit')
                ->first()
                ?->unit?->alias ?? '';
        }

        $pdf = PDF::loadView('pdf.material', compact('materials'));

        return $pdf->download('daftar-bahan-persediaan.pdf');
    }

    public function generateExpensePDF(Request $request)
    {
        $status = $request->status ?? 'all';
        if ($request->status === 'history') {
            $expenses = \App\Models\Expense::with(['expenseDetails', 'supplier'])
                ->where('is_finish', true)
                ->when($request->search, function ($query) use ($request) {
                    return $query->where('expense_number', 'like', '%' . $request->search . '%');
                })
                ->latest()
                ->get();
            $pdf = PDF::loadView('pdf.expense', compact('expenses', 'status'));

            return $pdf->download('riwayat-belanja-persediaan.pdf');
        } elseif ($request->status === 'all') {
            $expenses = \App\Models\Expense::with(['expenseDetails', 'supplier'])
                ->when($request->search, function ($query) use ($request) {
                    return $query->where('expense_number', 'like', '%' . $request->search . '%');
                })
                ->latest()
                ->get();
            $pdf = PDF::loadView('pdf.expense', compact('expenses', 'status'));

            return $pdf->download('daftar-belanja-persediaan.pdf');
        } else {
            return redirect()->route('belanja')->with('error', 'Status tidak valid.');
        }
    }

    public function generateExpenseDetailPDF($id)
    {
        $expense = \App\Models\Expense::with(['expenseDetails', 'supplier'])
            ->findOrFail($id);
        $logName = Activity::inLog('expenses')->where('subject_id', $expense->id)->latest()->first()?->causer->name ?? '-';
        $status = $expense->status;
        $total_quantity_expect = $expense->expenseDetails->sum('quantity_expect');
        $total_quantity_get = $expense->expenseDetails->sum('quantity_get');
        $percentage = $total_quantity_expect > 0 ? ($total_quantity_get / $total_quantity_expect) * 100 : 0;
        $expenseDetails = \App\Models\ExpenseDetail::where('expense_id', $expense->id)
            ->with(['material', 'unit'])
            ->get();

        $pdf = PDF::loadView('pdf.expense-detail', compact('expense', 'logName', 'status', 'total_quantity_expect', 'total_quantity_get', 'percentage', 'expenseDetails'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('rincian-belanja-persediaan-' . $expense->expense_number . '.pdf');
    }

    public function generateHitungPDF(Request $request)
    {
        $status = $request->status ?? 'all';
        if ($request->status === 'history') {
            $hitungs = \App\Models\Hitung::with(['details'])
                ->where('is_finish', true)
                ->when($request->search, function ($query) use ($request) {
                    return $query->where('hitung_number', 'like', '%' . $request->search . '%');
                })
                ->latest()
                ->get();
            $pdf = PDF::loadView('pdf.hitung', compact('hitungs', 'status'));

            return $pdf->download('riwayat-hitung-persediaan.pdf');
        } elseif ($request->status === 'all') {
            $hitungs = \App\Models\Hitung::with(['details'])
                ->when($request->search, function ($query) use ($request) {
                    return $query->where('hitung_number', 'like', '%' . $request->search . '%');
                })
                ->latest()
                ->get();
            $pdf = PDF::loadView('pdf.hitung', compact('hitungs', 'status'));

            return $pdf->download('daftar-hitung-persediaan.pdf');
        } else {
            return redirect()->route('hitung')->with('error', 'Status tidak valid.');
        }
    }

    public function generateHitungDetailPDF($id)
    {
        $hitung = \App\Models\Hitung::with(['details.material', 'details.unit'])
            ->findOrFail($id);
        $logName = Activity::inLog('hitungs')->where('subject_id', $hitung->id)->latest()->first()?->causer->name ?? '-';
        $status = $hitung->status;
        $hitungDetails = \App\Models\HitungDetail::where('hitung_id', $hitung->id)
            ->with(['material', 'unit'])
            ->get();

        $pdf = PDF::loadView('pdf.hitung-detail', compact('hitung', 'logName', 'status', 'hitungDetails'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('rincian-hitung-persediaan-' . $hitung->hitung_number . '.pdf');
    }

    public function generateUserPDF(Request $request)
    {
        $users = \App\Models\User::when($request->search, function ($query) use ($request) {
            return $query->where('name', 'like', '%' . $request->search . '%');
        })->with('roles')->get();

        $pdf = PDF::loadView('pdf.user', compact('users'));

        return $pdf->download('daftar-pekerja.pdf');
    }

    public function generateRolePDF(Request $request)
    {
        $roles = \App\Models\SpatieRole::when($request->search, function ($query) use ($request) {
            return $query->where('name', 'like', '%' . $request->search . '%');
        })->get();

        $pdf = PDF::loadView('pdf.role', compact('roles'));

        return $pdf->download('daftar-peran.pdf');
    }

    public function laporanKasir(Request $request)
    {
        $filterPeriod = $request->get('filterPeriod', 'Hari');
        $selectedDate = $request->get('selectedDate', now()->toDateString());
        $customStartDate = $request->get('customStartDate');
        $customEndDate = $request->get('customEndDate');
        $selectedWorker = $request->get('selectedWorker', 'semua');
        $selectedMethod = $request->get('selectedMethod', 'semua');

        // Determine date range based on filter period
        if ($filterPeriod === 'Custom' && $customStartDate) {
            $startDate = Carbon::parse($customStartDate)->startOfDay();
            $endDate = $customEndDate ? Carbon::parse($customEndDate)->endOfDay() : Carbon::parse($customStartDate)->endOfDay();
            $lengthDays = Carbon::parse($customStartDate)->diffInDays(Carbon::parse($customEndDate ?? $customStartDate)) + 1;
            $prevStart = Carbon::parse($customStartDate)->subDays($lengthDays)->startOfDay();
            $prevEnd = Carbon::parse($customStartDate)->subDay()->endOfDay();
            $dateRange = Carbon::parse($customStartDate)->translatedFormat('d F Y') . ' - ' . Carbon::parse($customEndDate ?? $customStartDate)->translatedFormat('d F Y');
        } else {
            $selectedDateCarbon = Carbon::parse($selectedDate);

            switch ($filterPeriod) {
                case 'Hari':
                    $startDate = $selectedDateCarbon->copy()->startOfDay();
                    $endDate = $selectedDateCarbon->copy()->endOfDay();
                    $prevStart = $selectedDateCarbon->copy()->subDay()->startOfDay();
                    $prevEnd = $selectedDateCarbon->copy()->subDay()->endOfDay();
                    $dateRange = $selectedDateCarbon->translatedFormat('d F Y');
                    break;
                case 'Minggu':
                    $startDate = $selectedDateCarbon->copy()->startOfWeek()->startOfDay();
                    $endDate = $selectedDateCarbon->copy()->endOfWeek()->endOfDay();
                    $prevStart = $selectedDateCarbon->copy()->subWeek()->startOfWeek()->startOfDay();
                    $prevEnd = $selectedDateCarbon->copy()->subWeek()->endOfWeek()->endOfDay();
                    $dateRange = $startDate->translatedFormat('d F Y') . ' - ' . $endDate->translatedFormat('d F Y');
                    break;
                case 'Bulan':
                    $startDate = $selectedDateCarbon->copy()->startOfMonth()->startOfDay();
                    $endDate = $selectedDateCarbon->copy()->endOfMonth()->endOfDay();
                    $prevStart = $selectedDateCarbon->copy()->subMonth()->startOfMonth()->startOfDay();
                    $prevEnd = $selectedDateCarbon->copy()->subMonth()->endOfMonth()->endOfDay();
                    $dateRange = $selectedDateCarbon->translatedFormat('F Y');
                    break;
                case 'Tahun':
                    $startDate = $selectedDateCarbon->copy()->startOfYear()->startOfDay();
                    $endDate = $selectedDateCarbon->copy()->endOfYear()->endOfDay();
                    $prevStart = $selectedDateCarbon->copy()->subYear()->startOfYear()->startOfDay();
                    $prevEnd = $selectedDateCarbon->copy()->subYear()->endOfYear()->endOfDay();
                    $dateRange = 'Tahun ' . $selectedDateCarbon->year;
                    break;
                default:
                    $startDate = $selectedDateCarbon->copy()->startOfDay();
                    $endDate = $selectedDateCarbon->copy()->endOfDay();
                    $prevStart = $selectedDateCarbon->copy()->subDay()->startOfDay();
                    $prevEnd = $selectedDateCarbon->copy()->subDay()->endOfDay();
                    $dateRange = $selectedDateCarbon->translatedFormat('d F Y');
            }
        }

        // Query transactions
        $transactionsQuery = Transaction::whereBetween('start_date', [$startDate, $endDate])
            ->where('status', 'Selesai');

        if ($selectedWorker !== 'semua') {
            $transactionsQuery->where('created_by', $selectedWorker);
        }

        if ($selectedMethod !== 'semua') {
            $transactionsQuery->where('method', $selectedMethod);
        }

        $transactions = $transactionsQuery->get();

        // Prev transactions
        $prevTransactionsQuery = Transaction::whereBetween('start_date', [$prevStart, $prevEnd])
            ->where('status', 'Selesai');

        if ($selectedWorker !== 'semua') {
            $prevTransactionsQuery->where('created_by', $selectedWorker);
        }

        if ($selectedMethod !== 'semua') {
            $prevTransactionsQuery->where('method', $selectedMethod);
        }

        $prevTransactions = $prevTransactionsQuery->get();

        $transactionIds = $transactions->pluck('id');
        $prevTransactionIds = $prevTransactions->pluck('id');

        $details = TransactionDetail::with('product')
            ->whereIn('transaction_id', $transactionIds)
            ->get();

        $prevDetails = TransactionDetail::with('product')
            ->whereIn('transaction_id', $prevTransactionIds)
            ->get();

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

        $grossRevenue = $transactions->sum('total_amount');
        $prevGrossRevenue = $prevTransactions->sum('total_amount');

        $discountTotal = $transactions->sum('points_discount');
        $prevDiscountTotal = $prevTransactions->sum('points_discount');

        $refundTotal = $transactions->sum('total_refund');
        $prevRefundTotal = $prevTransactions->sum('total_refund');

        $netRevenue = $grossRevenue - $refundTotal - $discountTotal;
        $prevNetRevenue = $prevGrossRevenue - $prevRefundTotal - $prevDiscountTotal;

        $capitalTotal = $details->sum(function ($d) {
            return ($d->product->pcs_capital ?? 0) * ($d->quantity - $d->refund_quantity);
        });
        $prevCapitalTotal = $prevDetails->sum(function ($d) {
            return ($d->product->pcs_capital ?? 0) * ($d->quantity - $d->refund_quantity);
        });
        $profit = $netRevenue - $capitalTotal;
        $prevProfit = $prevNetRevenue - $prevCapitalTotal;

        $diffStats = [
            'sessionCount' => $this->calculateDiff($sessionCount, $prevSessionCount),
            'transactionCount' => $this->calculateDiff($transactionCount, $prevTransactionCount),
            'customerCount' => $this->calculateDiff($customerCount, $prevCustomerCount),
            'productSold' => $this->calculateDiff($productSold, $prevProductSold),
            'bestProduct' => $this->calculateDiff($best['total'] ?? 0, $prevBest['total'] ?? 0),
            'worstProduct' => $this->calculateDiff($worst['total'] ?? 0, $prevWorst['total'] ?? 0),
            'grossRevenue' => $this->calculateDiff($grossRevenue, $prevGrossRevenue),
            'discountTotal' => $this->calculateDiff($discountTotal, $prevDiscountTotal),
            'refundTotal' => $this->calculateDiff($refundTotal, $prevRefundTotal),
            'netRevenue' => $this->calculateDiff($netRevenue, $prevNetRevenue),
            'capitalTotal' => $this->calculateDiff($capitalTotal, $prevCapitalTotal),
            'profit' => $this->calculateDiff($profit, $prevProfit),
        ];

        $workerName = $selectedWorker === 'semua' ? 'Semua Pekerja' : (User::find($selectedWorker)->name ?? 'Unknown');
        $methodName = $selectedMethod === 'semua' ? 'Semua Metode' : $selectedMethod;

        // Get product sales data
        $products = Product::with('category')->get();
        $productSales = [];
        foreach ($products as $product) {
            $sold = $details->where('product_id', $product->id)->sum(fn($d) => $d->quantity - $d->refund_quantity);
            if ($sold > 0) {
                $productSales[] = [
                    'name' => $product->name,
                    'category' => $product->category->name ?? '-',
                    'sold' => $sold,
                    'price' => $product->price,
                    'total' => $sold * $product->price,
                ];
            }
        }

        usort($productSales, fn($a, $b) => $b['sold'] - $a['sold']);

        $pdf = Pdf::loadView('pdf.laporan-kasir', [
            'dateRange' => $dateRange,
            'workerName' => $workerName,
            'methodName' => $methodName,
            'sessionCount' => $sessionCount,
            'transactionCount' => $transactionCount,
            'customerCount' => $customerCount,
            'productSold' => $productSold,
            'bestProduct' => $best,
            'worstProduct' => $worst,
            'grossRevenue' => $grossRevenue,
            'discountTotal' => $discountTotal,
            'refundTotal' => $refundTotal,
            'netRevenue' => $netRevenue,
            'profit' => $profit,
            'diffStats' => $diffStats,
            'topProducts' => $top10->toArray(),
            'productSales' => $productSales,
        ]);

        return $pdf->stream('laporan-kasir-' . now()->format('Y-m-d') . '.pdf');
    }

    public function laporanInventori(Request $request)
    {
        $filterPeriod = $request->get('filterPeriod', 'Hari');
        $selectedDate = $request->get('selectedDate', now()->toDateString());
        $customStartDate = $request->get('customStartDate');
        $customEndDate = $request->get('customEndDate');
        $selectedWorker = $request->get('selectedWorker', 'semua');

        // Determine date range based on filter period
        if ($filterPeriod === 'Custom' && $customStartDate) {
            $startDate = Carbon::parse($customStartDate)->startOfDay();
            $endDate = $customEndDate ? Carbon::parse($customEndDate)->endOfDay() : Carbon::parse($customStartDate)->endOfDay();
            $lengthDays = $startDate->diffInDays($endDate) + 1;
            $prevStart = $startDate->copy()->subDays($lengthDays);
            $prevEnd = $startDate->copy()->subDay();
            $dateRange = Carbon::parse($customStartDate)->translatedFormat('d F Y') . ' - ' . Carbon::parse($customEndDate ?? $customStartDate)->translatedFormat('d F Y');
        } else {
            $selectedDateCarbon = Carbon::parse($selectedDate);

            switch ($filterPeriod) {
                case 'Hari':
                    $startDate = $selectedDateCarbon->copy()->startOfDay();
                    $endDate = $selectedDateCarbon->copy()->endOfDay();
                    $prevStart = $startDate->copy()->subDay();
                    $prevEnd = $endDate->copy()->subDay();
                    $dateRange = $selectedDateCarbon->translatedFormat('d F Y');
                    break;
                case 'Minggu':
                    $startDate = $selectedDateCarbon->copy()->startOfWeek();
                    $endDate = $selectedDateCarbon->copy()->endOfWeek();
                    $prevStart = $startDate->copy()->subWeek();
                    $prevEnd = $endDate->copy()->subWeek();
                    $dateRange = $startDate->translatedFormat('d F Y') . ' - ' . $endDate->translatedFormat('d F Y');
                    break;
                case 'Bulan':
                    $startDate = $selectedDateCarbon->copy()->startOfMonth();
                    $endDate = $selectedDateCarbon->copy()->endOfMonth();
                    $prevStart = $startDate->copy()->subMonth();
                    $prevEnd = $endDate->copy()->subMonth();
                    $dateRange = $selectedDateCarbon->translatedFormat('F Y');
                    break;
                case 'Tahun':
                    $startDate = $selectedDateCarbon->copy()->startOfYear();
                    $endDate = $selectedDateCarbon->copy()->endOfYear();
                    $prevStart = $startDate->copy()->subYear();
                    $prevEnd = $endDate->copy()->subYear();
                    $dateRange = 'Tahun ' . $selectedDateCarbon->year;
                    break;
                default:
                    $startDate = $selectedDateCarbon->copy()->startOfDay();
                    $endDate = $selectedDateCarbon->copy()->endOfDay();
                    $prevStart = $startDate->copy()->subDay();
                    $prevEnd = $endDate->copy()->subDay();
                    $dateRange = $selectedDateCarbon->translatedFormat('d F Y');
            }
        }

        $expensesQuery = Expense::whereBetween('expense_date', [$startDate, $endDate]);

        if ($selectedWorker !== 'semua') {
            $expensesQuery->where('user_id', $selectedWorker);
        }

        $expenses = $expensesQuery->get();
        $prevExpenses = Expense::whereBetween('expense_date', [$prevStart, $prevEnd])->get();

        $expenseIds = $expenses->pluck('id');
        $prevExpenseIds = $prevExpenses->pluck('id');

        $details = ExpenseDetail::with('material')
            ->whereIn('expense_id', $expenseIds)
            ->get();

        $prevDetails = ExpenseDetail::with('material')
            ->whereIn('expense_id', $prevExpenseIds)
            ->get();

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
        $prevRemainGrandTotal = $remainGrandTotal;

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

        $totalPrice = [];
        $usedGrandTotal = 0;
        foreach ($groupProductByMaterial as $materialId => $compositions) {
            foreach ($compositions as $composition) {
                $productId = $composition['product_id'];

                $productionDetailsQuery = ProductionDetail::where('product_id', $productId)
                    ->whereHas('production', function ($query) use ($startDate, $endDate, $selectedWorker) {
                        $query->whereBetween('date', [$startDate, $endDate]);

                        if ($selectedWorker !== 'semua') {
                            $query->whereHas('workers', function ($workerQuery) use ($selectedWorker) {
                                $workerQuery->where('user_id', $selectedWorker);
                            });
                        }
                    });

                $productionDetails = $productionDetailsQuery->get();
                $totalProduction = $productionDetails->sum('quantity_get') + $productionDetails->sum('quantity_fail');
                $dividedQuantity = $composition['pcs'] > 0 ? $totalProduction / $composition['pcs'] : 0;
                $totalMaterialQuantity = $dividedQuantity * $composition['material_quantity'];
                $material = Material::find($materialId);
                $materialPrice = $material->material_details->where('unit_id', $composition['unit_id'])->first()->supply_price ?? 0;
                $priceValue = $totalMaterialQuantity * $materialPrice;
                $totalPrice[$materialId][] = $priceValue;
                $usedGrandTotal += $priceValue;
            }
        }
        $prevUsedGrandTotal = $usedGrandTotal;

        $expenseGrandTotal = $expenses->sum('grand_total_actual');
        $prevExpenseGrandTotal = $prevExpenses->sum('grand_total_actual');

        $groupedMaterials = $details->groupBy('material_id')->map(function ($items) {
            $total = $items->sum('total_price');

            return [
                'total' => $total,
                'name' => $items->first()->material->name ?? 'Unknown',
            ];
        });

        $sorted = $groupedMaterials->sortByDesc('total');
        $best = $sorted->first();
        $worst = $sorted->filter(fn($p) => $p['total'] > 0)->sortBy('total')->first();

        $prevBest = $prevDetails->groupBy('material_id')->map(function ($items) {
            return [
                'total' => $items->sum('total_price'),
                'name' => $items->first()->material->name ?? 'Unknown',
            ];
        })->sortByDesc('total')->first();

        $prevWorst = $prevDetails->groupBy('material_id')->map(function ($items) {
            return [
                'total' => $items->sum('total_price'),
                'name' => $items->first()->material->name ?? 'Unknown',
            ];
        })->filter(fn($p) => $p['total'] > 0)->sortBy('total')->first();

        $diffStats = [
            'expenseGrandTotal' => $this->calculateDiff($expenseGrandTotal, $prevExpenseGrandTotal),
            'usedGrandTotal' => $this->calculateDiff($usedGrandTotal, $prevUsedGrandTotal),
            'remainGrandTotal' => $this->calculateDiff($remainGrandTotal, $prevRemainGrandTotal),
            'totalExpense' => $this->calculateDiff($totalExpense, $prevTotalExpense),
            'bestMaterial' => $this->calculateDiff($best['total'] ?? 0, $prevBest['total'] ?? 0),
            'worstMaterial' => $this->calculateDiff($worst['total'] ?? 0, $prevWorst['total'] ?? 0),
        ];

        $workerName = $selectedWorker === 'semua' ? 'Semua Pekerja' : (User::find($selectedWorker)->name ?? 'Unknown');

        // Get top materials
        $topMaterials = $sorted->take(10)->toArray();

        // Get material table data
        $materialTables = [];
        $allMaterials = Material::with(['material_details.unit', 'batches'])->get();
        foreach ($allMaterials as $material) {
            foreach ($material->material_details as $detail) {
                $remainBatchQty = $material->batches->where('unit_id', $detail->unit_id)->sum('batch_quantity');
                $materialTables[] = [
                    'name' => $material->name,
                    'unit' => $detail->unit->name ?? '-',
                    'supply_price' => $detail->supply_price,
                    'sell_price' => $detail->sell_price,
                    'min_stock' => $detail->min_stock,
                    'remain' => $remainBatchQty,
                    'remain_value' => $detail->supply_price * $remainBatchQty,
                    'status' => $remainBatchQty <= $detail->min_stock ? 'Rendah' : 'Aman',
                ];
            }
        }

        $pdf = Pdf::loadView('pdf.laporan-inventori', [
            'dateRange' => $dateRange,
            'workerName' => $workerName,
            'expenseGrandTotal' => $expenseGrandTotal,
            'usedGrandTotal' => $usedGrandTotal,
            'remainGrandTotal' => $remainGrandTotal,
            'totalExpense' => $totalExpense,
            'bestMaterial' => $best,
            'worstMaterial' => $worst,
            'diffStats' => $diffStats,
            'topMaterials' => $topMaterials,
            'materialTables' => $materialTables,
        ]);

        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('laporan-inventori-' . now()->format('Y-m-d') . '.pdf');
    }

    public function laporanProduksi(Request $request)
    {
        $filterPeriod = $request->get('filterPeriod', 'Hari');
        $selectedDate = $request->get('selectedDate', now()->toDateString());
        $customStartDate = $request->get('customStartDate');
        $customEndDate = $request->get('customEndDate');
        $selectedWorker = $request->get('selectedWorker', 'semua');
        $selectedMethod = $request->get('selectedMethod', 'semua');

        // Determine date range based on filter period
        if ($filterPeriod === 'Custom' && $customStartDate) {
            $startDate = Carbon::parse($customStartDate)->toDateString();
            $endDate = $customEndDate ? Carbon::parse($customEndDate)->toDateString() : $startDate;
            $lengthDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
            $prevStart = Carbon::parse($startDate)->subDays($lengthDays)->toDateString();
            $prevEnd = Carbon::parse($startDate)->subDay()->toDateString();
            $dateRange = Carbon::parse($customStartDate)->translatedFormat('d F Y') . ' - ' . Carbon::parse($customEndDate ?? $customStartDate)->translatedFormat('d F Y');
        } else {
            $selectedDateCarbon = Carbon::parse($selectedDate);

            switch ($filterPeriod) {
                case 'Hari':
                    $startDate = $selectedDateCarbon->toDateString();
                    $endDate = $selectedDateCarbon->toDateString();
                    $prevStart = $selectedDateCarbon->copy()->subDay()->toDateString();
                    $prevEnd = $selectedDateCarbon->copy()->subDay()->toDateString();
                    $dateRange = $selectedDateCarbon->translatedFormat('d F Y');
                    break;
                case 'Minggu':
                    $startDate = $selectedDateCarbon->copy()->startOfWeek()->toDateString();
                    $endDate = $selectedDateCarbon->copy()->endOfWeek()->toDateString();
                    $prevStart = $selectedDateCarbon->copy()->subWeek()->startOfWeek()->toDateString();
                    $prevEnd = $selectedDateCarbon->copy()->subWeek()->endOfWeek()->toDateString();
                    $dateRange = Carbon::parse($startDate)->translatedFormat('d F Y') . ' - ' . Carbon::parse($endDate)->translatedFormat('d F Y');
                    break;
                case 'Bulan':
                    $startDate = $selectedDateCarbon->copy()->startOfMonth()->toDateString();
                    $endDate = $selectedDateCarbon->copy()->endOfMonth()->toDateString();
                    $prevStart = $selectedDateCarbon->copy()->subMonth()->startOfMonth()->toDateString();
                    $prevEnd = $selectedDateCarbon->copy()->subMonth()->endOfMonth()->toDateString();
                    $dateRange = $selectedDateCarbon->translatedFormat('F Y');
                    break;
                case 'Tahun':
                    $startDate = $selectedDateCarbon->copy()->startOfYear()->toDateString();
                    $endDate = $selectedDateCarbon->copy()->endOfYear()->toDateString();
                    $prevStart = $selectedDateCarbon->copy()->subYear()->startOfYear()->toDateString();
                    $prevEnd = $selectedDateCarbon->copy()->subYear()->endOfYear()->toDateString();
                    $dateRange = 'Tahun ' . $selectedDateCarbon->year;
                    break;
                default:
                    $startDate = $selectedDateCarbon->toDateString();
                    $endDate = $selectedDateCarbon->toDateString();
                    $prevStart = $selectedDateCarbon->copy()->subDay()->toDateString();
                    $prevEnd = $selectedDateCarbon->copy()->subDay()->toDateString();
                    $dateRange = $selectedDateCarbon->translatedFormat('d F Y');
            }
        }

        // Query productions
        $productionsQuery = Production::whereBetween('start_date', [$startDate, $endDate])
            ->where('is_finish', true);

        if ($selectedWorker !== 'semua') {
            $productionsQuery->whereHas('workers', fn($q) => $q->where('user_id', $selectedWorker));
        }

        if ($selectedMethod !== 'semua') {
            $productionsQuery->where('method', $selectedMethod);
        }

        $productions = $productionsQuery->get();

        // Prev productions
        $prevProductionsQuery = Production::whereBetween('start_date', [$prevStart, $prevEnd])
            ->where('is_finish', true);

        if ($selectedWorker !== 'semua') {
            $prevProductionsQuery->whereHas('workers', fn($q) => $q->where('user_id', $selectedWorker));
        }

        if ($selectedMethod !== 'semua') {
            $prevProductionsQuery->where('method', $selectedMethod);
        }

        $prevProductions = $prevProductionsQuery->get();

        $productionIds = $productions->pluck('id');
        $prevProductionIds = $prevProductions->pluck('id');

        $details = ProductionDetail::with('product')
            ->whereIn('production_id', $productionIds)
            ->get();

        $prevDetails = ProductionDetail::with('product')
            ->whereIn('production_id', $prevProductionIds)
            ->get();

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

        $failedProduction = $details->sum('quantity_fail');
        $prevFailedProduction = $prevDetails->sum('quantity_fail');

        $totalProduction = $successProduction + $failedProduction;
        $prevTotalProduction = $prevSuccessProduction + $prevFailedProduction;

        $diffStats = [
            'successProduction' => $this->calculateDiff($successProduction, $prevSuccessProduction),
            'failedProduction' => $this->calculateDiff($failedProduction, $prevFailedProduction),
            'totalProduction' => $this->calculateDiff($totalProduction, $prevTotalProduction),
            'bestProduction' => $this->calculateDiff($best['total'] ?? 0, $prevBest['total'] ?? 0),
            'worstProduction' => $this->calculateDiff($worst['total'] ?? 0, $prevWorst['total'] ?? 0),
        ];

        $workerName = $selectedWorker === 'semua' ? 'Semua Pekerja' : (User::find($selectedWorker)->name ?? 'Unknown');
        $methodName = $selectedMethod === 'semua' ? 'Semua Metode' : $selectedMethod;

        // Get production products data
        $products = Product::with('category')->get();
        $productionProducts = [];
        foreach ($products as $product) {
            $produced = $details->where('product_id', $product->id)->sum('quantity_get');
            $failed = $details->where('product_id', $product->id)->sum('quantity_fail');
            if ($produced > 0 || $failed > 0) {
                $productionProducts[] = [
                    'name' => $product->name,
                    'category' => $product->category->name ?? '-',
                    'produced' => $produced,
                    'failed' => $failed,
                    'total' => $produced + $failed,
                ];
            }
        }

        usort($productionProducts, fn($a, $b) => $b['produced'] - $a['produced']);

        $pdf = Pdf::loadView('pdf.laporan-produksi', [
            'dateRange' => $dateRange,
            'workerName' => $workerName,
            'methodName' => $methodName,
            'successProduction' => $successProduction,
            'failedProduction' => $failedProduction,
            'totalProduction' => $totalProduction,
            'bestProduction' => $best,
            'worstProduction' => $worst,
            'diffStats' => $diffStats,
            'topProductions' => $top10->toArray(),
            'productionProducts' => $productionProducts,
        ]);

        return $pdf->stream('laporan-produksi-' . now()->format('Y-m-d') . '.pdf');
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
}
