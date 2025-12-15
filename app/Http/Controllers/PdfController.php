<?php

namespace App\Http\Controllers;

use App\Exports\InventoriExport;
use App\Exports\KasirExport;
use App\Exports\ProduksiExport;
use App\Models\Category;
use App\Models\Expense;
use App\Models\IngredientCategory;
use App\Models\Material;
use App\Models\MaterialDetail;
use App\Models\Product;
use App\Models\Production;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Activitylog\Models\Activity;

class PdfController extends Controller
{
    public function print($id)
    {
        set_time_limit(120);
        $transaction = \App\Models\Transaction::with(['user', 'details.product'])->find($id);

        $pdf = Pdf::loadView('pdf.pdf', compact('transaction'))->setPaper([0, 0, 227, 400], 'portrait');

        return $pdf->stream();
    }

    public function printReport(Request $request)
    {
        set_time_limit(120);
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
        set_time_limit(120);
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
        set_time_limit(120);
        $categories = IngredientCategory::when($request->search, function ($query) use ($request) {
            return $query->where('name', 'like', '%' . $request->search . '%');
        })->get();

        $pdf = PDF::loadView('pdf.ingredient-category', compact('categories'));

        return $pdf->download('daftar-kategori-persediaan.pdf');
    }

    public function generateProductPDF(Request $request)
    {
        set_time_limit(120);
        $products = Product::when($request->search, function ($query) use ($request) {
            return $query->where('name', 'like', '%' . $request->search . '%');
        })->with('category')->get();

        $pdf = PDF::loadView('pdf.product', compact('products'));

        return $pdf->download('daftar-produk.pdf');
    }

    public function generateSupplierPDF(Request $request)
    {
        set_time_limit(120);
        $suppliers = Supplier::when($request->search, function ($query) use ($request) {
            return $query->where('name', 'like', '%' . $request->search . '%');
        })->get();

        $pdf = PDF::loadView('pdf.supplier', compact('suppliers'));

        return $pdf->download('daftar-toko-persediaan.pdf');
    }

    public function generateMaterialPDF(Request $request)
    {
        set_time_limit(120);
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
        set_time_limit(120);
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
        set_time_limit(120);
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
        set_time_limit(120);
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
        set_time_limit(120);
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
        set_time_limit(120);
        $users = \App\Models\User::when($request->search, function ($query) use ($request) {
            return $query->where('name', 'like', '%' . $request->search . '%');
        })->with('roles')->get();

        $pdf = PDF::loadView('pdf.user', compact('users'));

        return $pdf->download('daftar-pekerja.pdf');
    }

    public function generateRolePDF(Request $request)
    {
        set_time_limit(120);
        $roles = \App\Models\SpatieRole::when($request->search, function ($query) use ($request) {
            return $query->where('name', 'like', '%' . $request->search . '%');
        })->get();

        $pdf = PDF::loadView('pdf.role', compact('roles'));

        return $pdf->download('daftar-peran.pdf');
    }

    public function laporanKasir(Request $request)
    {
        set_time_limit(120);
        $reportContent = $request->get('reportContent', 'keuangan');
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
            $dateRange = Carbon::parse($customStartDate)->translatedFormat('d F Y') . ' - ' . Carbon::parse($customEndDate ?? $customStartDate)->translatedFormat('d F Y');
        } else {
            $selectedDateCarbon = Carbon::parse($selectedDate);

            switch ($filterPeriod) {
                case 'Hari':
                    $startDate = $selectedDateCarbon->copy()->startOfDay();
                    $endDate = $selectedDateCarbon->copy()->endOfDay();
                    $dateRange = $selectedDateCarbon->translatedFormat('d F Y');
                    break;
                case 'Minggu':
                    $startDate = $selectedDateCarbon->copy()->startOfWeek()->startOfDay();
                    $endDate = $selectedDateCarbon->copy()->endOfWeek()->endOfDay();
                    $dateRange = $startDate->translatedFormat('d F Y') . ' - ' . $endDate->translatedFormat('d F Y');
                    break;
                case 'Bulan':
                    $startDate = $selectedDateCarbon->copy()->startOfMonth()->startOfDay();
                    $endDate = $selectedDateCarbon->copy()->endOfMonth()->endOfDay();
                    $dateRange = $selectedDateCarbon->translatedFormat('F Y');
                    break;
                case 'Tahun':
                    $startDate = $selectedDateCarbon->copy()->startOfYear()->startOfDay();
                    $endDate = $selectedDateCarbon->copy()->endOfYear()->endOfDay();
                    $dateRange = $selectedDateCarbon->translatedFormat('Y');
                    break;
                default:
                    $startDate = $selectedDateCarbon->copy()->startOfDay();
                    $endDate = $selectedDateCarbon->copy()->endOfDay();
                    $dateRange = $selectedDateCarbon->translatedFormat('d F Y');
            }
        }

        $workerName = $selectedWorker === 'semua' ? 'Semua Pekerja' : (User::find($selectedWorker)->name ?? 'Unknown');
        $methodName = $selectedMethod === 'semua' ? 'Semua Metode' : ucfirst($selectedMethod);

        // Route to appropriate export based on reportContent
        switch ($reportContent) {
            case 'sesi':
                return $this->exportSesi($startDate, $endDate, $dateRange, $selectedWorker, $workerName);
            case 'customer':
                return $this->exportCustomer($startDate, $endDate, $dateRange);
            case 'transaksi':
                return $this->exportTransaksi($startDate, $endDate, $dateRange, $selectedWorker, $selectedMethod, $workerName, $methodName);
            case 'terjual':
                return $this->exportTerjual($startDate, $endDate, $dateRange, $selectedWorker, $selectedMethod, $workerName, $methodName);
            case 'keuangan':
            default:
                return $this->exportKeuangan($startDate, $endDate, $dateRange, $selectedWorker, $selectedMethod, $workerName, $methodName);
        }
    }

    private function exportSesi($startDate, $endDate, $dateRange, $selectedWorker, $workerName)
    {
        $shiftsQuery = \App\Models\Shift::with('openedBy')
            ->whereBetween('start_time', [$startDate, $endDate])
            ->where('status', 'closed');

        if ($selectedWorker !== 'semua') {
            $shiftsQuery->where('opened_by', $selectedWorker);
        }

        $shifts = $shiftsQuery->orderBy('start_time', 'desc')->get();

        $sesiData = $shifts->map(function ($shift) {
            $duration = '-';
            if ($shift->start_time && $shift->end_time) {
                $start = Carbon::parse($shift->start_time);
                $end = Carbon::parse($shift->end_time);
                $diff = $start->diff($end);
                $duration = $diff->format('%H jam %I menit');
            }

            $totalTransaksi = Transaction::where('created_by_shift', $shift->id)->count();
            $totalPendapatan = Transaction::where('created_by_shift', $shift->id)->sum('total_amount') ?? 0;

            return [
                'tanggal' => Carbon::parse($shift->start_time)->translatedFormat('d F Y H:i'),
                'kasir' => $shift->openedBy->name ?? '-',
                'durasi' => $duration,
                'total_transaksi' => $totalTransaksi,
                'total_pendapatan' => $totalPendapatan,
            ];
        })->toArray();

        $pdf = Pdf::loadView('pdf.laporan-kasir-sesi', [
            'dateRange' => $dateRange,
            'workerName' => $workerName,
            'sesiData' => $sesiData,
        ]);

        return $pdf->stream('laporan-kasir-sesi-' . now()->format('Y-m-d') . '.pdf');
    }

    private function exportCustomer($startDate, $endDate, $dateRange)
    {
        $customers = Transaction::whereBetween('start_date', [$startDate, $endDate])
            ->where('status', 'Selesai')
            ->whereNotNull('phone')
            ->select('phone', 'name')
            ->selectRaw('MIN(start_date) as first_transaction')
            ->selectRaw('COUNT(*) as transaction_count')
            ->groupBy('phone', 'name')
            ->havingRaw('MIN(start_date) BETWEEN ? AND ?', [$startDate, $endDate])
            ->orderBy('first_transaction', 'desc')
            ->get();

        $customerData = $customers->map(function ($customer) {
            return [
                'phone' => $customer->phone,
                'name' => $customer->name ?? '-',
                'transaction_count' => $customer->transaction_count,
                'first_transaction' => Carbon::parse($customer->first_transaction)->translatedFormat('d F Y H:i'),
            ];
        })->toArray();

        $pdf = Pdf::loadView('pdf.laporan-kasir-customer', [
            'dateRange' => $dateRange,
            'customerData' => $customerData,
        ]);

        return $pdf->stream('laporan-kasir-customer-' . now()->format('Y-m-d') . '.pdf');
    }

    private function exportTransaksi($startDate, $endDate, $dateRange, $selectedWorker, $selectedMethod, $workerName, $methodName)
    {
        $transactionsQuery = Transaction::with(['details.product', 'user'])
            ->whereBetween('start_date', [$startDate, $endDate])
            ->where('status', 'Selesai');

        if ($selectedWorker !== 'semua') {
            $transactionsQuery->where('user_id', $selectedWorker);
        }

        if ($selectedMethod !== 'semua') {
            $transactionsQuery->where('method', $selectedMethod);
        }

        $transactions = $transactionsQuery->orderBy('start_date', 'desc')->get();

        $transaksiData = $transactions->map(function ($trx) {
            $products = $trx->details->map(function ($detail) {
                return $detail->product->name . ' (' . $detail->quantity . ')';
            })->implode(', ');

            return [
                'invoice' => $trx->invoice_number,
                'tanggal' => Carbon::parse($trx->start_date)->translatedFormat('d F Y H:i'),
                'kasir' => $trx->user->name ?? '-',
                'products' => $products,
                'total' => $trx->total_amount,
                'method' => $trx->method ? ucfirst(str_replace('-', ' ', $trx->method)) : 'Tidak Diketahui',
            ];
        })->toArray();

        $pdf = Pdf::loadView('pdf.laporan-kasir-transaksi', [
            'dateRange' => $dateRange,
            'workerName' => $workerName,
            'methodName' => $methodName,
            'transaksiData' => $transaksiData,
        ]);

        return $pdf->stream('laporan-kasir-transaksi-' . now()->format('Y-m-d') . '.pdf');
    }

    private function exportTerjual($startDate, $endDate, $dateRange, $selectedWorker, $selectedMethod, $workerName, $methodName)
    {
        $transactionsQuery = Transaction::whereBetween('start_date', [$startDate, $endDate])
            ->where('status', 'Selesai');

        if ($selectedWorker !== 'semua') {
            $transactionsQuery->where('user_id', $selectedWorker);
        }

        if ($selectedMethod !== 'semua') {
            $transactionsQuery->where('method', $selectedMethod);
        }

        $transactions = $transactionsQuery->get();
        $transactionIds = $transactions->pluck('id');

        $details = TransactionDetail::with('product')
            ->whereIn('transaction_id', $transactionIds)
            ->get();

        $products = Product::with('category')->get();
        $productSales = [];

        foreach ($products as $product) {
            $sold = $details->where('product_id', $product->id)->sum(fn($d) => $d->quantity - $d->refund_quantity);
            if ($sold > 0) {
                // Get production count from productions table using start_date
                $productionDetails = \App\Models\ProductionDetail::whereHas('production', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('start_date', [$startDate, $endDate]);
                })
                    ->where('product_id', $product->id)
                    ->sum('quantity_get');

                $productSales[] = [
                    'name' => $product->name,
                    'produksi' => $productionDetails,
                    'sold' => $sold,
                    'unsold' => max(0, $productionDetails - $sold),
                ];
            }
        }

        usort($productSales, fn($a, $b) => $b['sold'] - $a['sold']);

        $pdf = Pdf::loadView('pdf.laporan-kasir-terjual', [
            'dateRange' => $dateRange,
            'workerName' => $workerName,
            'methodName' => $methodName,
            'productSales' => $productSales,
        ]);

        return $pdf->stream('laporan-kasir-terjual-' . now()->format('Y-m-d') . '.pdf');
    }

    private function exportKeuangan($startDate, $endDate, $dateRange, $selectedWorker, $selectedMethod, $workerName, $methodName)
    {
        $transactionsQuery = Transaction::whereBetween('start_date', [$startDate, $endDate])
            ->where('status', 'Selesai');

        if ($selectedWorker !== 'semua') {
            $transactionsQuery->where('user_id', $selectedWorker);
        }

        if ($selectedMethod !== 'semua') {
            $transactionsQuery->where('method', $selectedMethod);
        }

        $transactions = $transactionsQuery->get();
        $transactionIds = $transactions->pluck('id');

        $details = TransactionDetail::with('product')
            ->whereIn('transaction_id', $transactionIds)
            ->get();

        // Build financial reports
        $keuanganData = [];
        $year = Carbon::parse($startDate)->year;

        foreach (range(1, 12) as $month) {
            $monthTransactions = $transactions->filter(fn($trx) => Carbon::parse($trx->start_date)->month === $month);
            $monthDetails = $details->filter(function ($d) use ($month, $transactions) {
                $trx = $transactions->firstWhere('id', $d->transaction_id);

                return $trx && Carbon::parse($trx->start_date)->month === $month;
            });

            $pendapatanKotor = $monthTransactions->sum('total_amount');
            $refund = $monthTransactions->sum('total_refund');
            $potonganHarga = $monthTransactions->sum('points_discount');
            $pendapatanBersih = $pendapatanKotor - $refund - $potonganHarga;
            $modal = $monthDetails->sum(function ($d) {
                return ($d->pcs_capital_snapshot ?? 0) * ($d->quantity - $d->refund_quantity);
            });
            $keuntungan = $pendapatanBersih - $modal;

            if ($pendapatanKotor > 0 || $refund > 0) {
                $monthName = Carbon::create($year, $month, 1)->translatedFormat('F Y');
                $keuanganData[] = [
                    'waktu' => $monthName,
                    'pendapatanKotor' => $pendapatanKotor,
                    'refund' => $refund,
                    'potonganHarga' => $potonganHarga,
                    'pendapatanBersih' => $pendapatanBersih,
                    'modal' => $modal,
                    'keuntungan' => $keuntungan,
                ];
            }
        }

        $pdf = Pdf::loadView('pdf.laporan-kasir-keuangan', [
            'dateRange' => $dateRange,
            'workerName' => $workerName,
            'methodName' => $methodName,
            'keuanganData' => $keuanganData,
        ]);

        return $pdf->stream('laporan-kasir-keuangan-' . now()->format('Y-m-d') . '.pdf');
    }

    public function laporanInventori(Request $request)
    {
        set_time_limit(120);
        $reportContent = $request->get('reportContent', '');
        $filterPeriod = $request->get('filterPeriod', 'Hari');
        $selectedDate = $request->get('selectedDate', now()->toDateString());
        $customStartDate = $request->get('customStartDate');
        $customEndDate = $request->get('customEndDate');
        $selectedWorker = $request->get('selectedWorker', 'semua');

        // Determine date range based on filter period
        if ($filterPeriod === 'Custom' && $customStartDate) {
            $startDate = Carbon::parse($customStartDate)->startOfDay();
            $endDate = $customEndDate ? Carbon::parse($customEndDate)->endOfDay() : Carbon::parse($customStartDate)->endOfDay();
            $dateRange = Carbon::parse($customStartDate)->translatedFormat('d F Y') . ' - ' . Carbon::parse($customEndDate ?? $customStartDate)->translatedFormat('d F Y');
        } else {
            $selectedDateCarbon = Carbon::parse($selectedDate);

            switch ($filterPeriod) {
                case 'Hari':
                    $startDate = $selectedDateCarbon->copy()->startOfDay();
                    $endDate = $selectedDateCarbon->copy()->endOfDay();
                    $dateRange = $selectedDateCarbon->translatedFormat('d F Y');
                    break;
                case 'Minggu':
                    $startDate = $selectedDateCarbon->copy()->startOfWeek();
                    $endDate = $selectedDateCarbon->copy()->endOfWeek();
                    $dateRange = $startDate->translatedFormat('d F Y') . ' - ' . $endDate->translatedFormat('d F Y');
                    break;
                case 'Bulan':
                    $startDate = $selectedDateCarbon->copy()->startOfMonth();
                    $endDate = $selectedDateCarbon->copy()->endOfMonth();
                    $dateRange = $selectedDateCarbon->translatedFormat('F Y');
                    break;
                case 'Tahun':
                    $startDate = $selectedDateCarbon->copy()->startOfYear();
                    $endDate = $selectedDateCarbon->copy()->endOfYear();
                    $dateRange = 'Tahun ' . $selectedDateCarbon->year;
                    break;
                default:
                    $startDate = $selectedDateCarbon->copy()->startOfDay();
                    $endDate = $selectedDateCarbon->copy()->endOfDay();
                    $dateRange = $selectedDateCarbon->translatedFormat('d F Y');
            }
        }

        $workerName = $selectedWorker === 'semua' ? 'Semua Pekerja' : (User::find($selectedWorker)->name ?? 'Unknown');

        $viewData = [
            'reportContent' => $reportContent,
            'dateRange' => $dateRange,
            'workerName' => $workerName,
        ];

        // Load data based on reportContent
        if ($reportContent === 'belanja') {
            // Belanja Report
            $expensesQuery = \App\Models\Expense::with(['supplier', 'expenseDetails.material', 'expenseDetails.unit'])
                ->whereBetween('expense_date', [$startDate->toDateString(), $endDate->toDateString()]);

            if ($selectedWorker !== 'semua') {
                $expenseIds = \Spatie\Activitylog\Models\Activity::inLog('expenses')
                    ->where('causer_id', $selectedWorker)
                    ->pluck('subject_id')
                    ->unique();

                $expensesQuery->whereIn('id', $expenseIds);
            }

            $expenses = $expensesQuery->get();

            $expenseData = $expenses->map(function ($expense) {
                $workerName = \Spatie\Activitylog\Models\Activity::inLog('expenses')
                    ->where('subject_id', $expense->id)
                    ->latest()
                    ->first()?->causer->name ?? '-';

                return [
                    'expense_number' => $expense->expense_number,
                    'expense_date' => Carbon::parse($expense->expense_date)->translatedFormat('d F Y'),
                    'supplier_name' => $expense->supplier->name ?? '-',
                    'worker_name' => $workerName,
                    'status' => $expense->status,
                    'grand_total' => $expense->grand_total_actual,
                    'details' => $expense->expenseDetails->map(function ($detail) {
                        return [
                            'material_name' => $detail->material->name ?? '-',
                            'unit_name' => $detail->unit->name ?? '-',
                            'quantity_expect' => $detail->quantity_expect,
                            'quantity_get' => $detail->quantity_get,
                            'price_expect' => $detail->price_expect,
                            'price_get' => $detail->price_get,
                            'total_expect' => $detail->total_expect,
                            'total_actual' => $detail->total_actual,
                        ];
                    }),
                ];
            });

            $viewData['expenseData'] = $expenseData;
        } elseif ($reportContent === 'persediaan') {
            // Persediaan Report
            // Calculate cumulative expense details until end date
            $cumulativeExpenseDetails = \App\Models\ExpenseDetail::with('material')
                ->whereHas('expense', function ($query) use ($endDate) {
                    $query->where('expense_date', '<=', $endDate->toDateString());
                })
                ->get();

            $grandTotal = 0;
            foreach ($cumulativeExpenseDetails as $detail) {
                $grandTotal += $detail->total_actual;
            }

            // Calculate cumulative production material usage until end date
            $usedGrandTotal = 0;
            $materialUsage = [];

            $products = \App\Models\Product::with(['product_compositions.material'])->get();
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

            foreach ($groupProductByMaterial as $materialId => $compositions) {
                foreach ($compositions as $composition) {
                    $productId = $composition['product_id'];

                    $productionDetailsQuery = \App\Models\ProductionDetail::where('product_id', $productId)
                        ->whereHas('production', function ($query) use ($endDate, $selectedWorker) {
                            $query->where('start_date', '<=', $endDate->toDateString());

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
                    $material = \App\Models\Material::find($materialId);
                    $materialPrice = $material->material_details->where('unit_id', $composition['unit_id'])->first()->supply_price ?? 0;
                    $priceValue = $totalMaterialQuantity * $materialPrice;

                    if (! isset($materialUsage[$materialId])) {
                        $materialUsage[$materialId] = [
                            'material_name' => $composition['material_name'],
                            'quantity_used' => 0,
                            'value_used' => 0,
                        ];
                    }

                    $materialUsage[$materialId]['quantity_used'] += $totalMaterialQuantity;
                    $materialUsage[$materialId]['value_used'] += $priceValue;
                    $usedGrandTotal += $priceValue;
                }
            }

            $remainGrandTotal = $grandTotal - $usedGrandTotal;

            $viewData['grandTotal'] = $grandTotal;
            $viewData['usedGrandTotal'] = $usedGrandTotal;
            $viewData['remainGrandTotal'] = $remainGrandTotal;
            $viewData['materialUsage'] = array_values($materialUsage);
        } elseif ($reportContent === 'alur') {
            // Alur Report
            $flowQuery = \App\Models\InventoryLog::with(['material', 'materialBatch.unit', 'user'])
                ->whereBetween('created_at', [$startDate, $endDate]);

            if ($selectedWorker !== 'semua') {
                $flowQuery->where('user_id', $selectedWorker);
            }

            $flowData = $flowQuery->orderBy('created_at', 'desc')->get()->map(function ($log) {
                return [
                    'created_at' => Carbon::parse($log->created_at)->translatedFormat('d F Y H:i'),
                    'material_name' => $log->material->name ?? '-',
                    'batch_number' => $log->materialBatch->batch_number ?? '-',
                    'unit_name' => $log->materialBatch->unit->name ?? '-',
                    'action' => $log->action,
                    'quantity_change' => $log->quantity_change,
                    'quantity_after' => $log->quantity_after,
                    'user_name' => $log->user->name ?? '-',
                    'note' => $log->note ?? '-',
                ];
            });

            $viewData['flowData'] = $flowData;
        }

        $pdf = Pdf::loadView('pdf.export-inventori', $viewData);

        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('export-inventori-' . $reportContent . '-' . now()->format('Y-m-d') . '.pdf');
    }

    public function laporanProduksi(Request $request)
    {
        set_time_limit(120);
        $reportContent = $request->get('reportContent', 'produksi');
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
            $dateRange = Carbon::parse($customStartDate)->translatedFormat('d F Y') . ' - ' . Carbon::parse($customEndDate ?? $customStartDate)->translatedFormat('d F Y');
        } else {
            $selectedDateCarbon = Carbon::parse($selectedDate);

            switch ($filterPeriod) {
                case 'Hari':
                    $startDate = $selectedDateCarbon->toDateString();
                    $endDate = $selectedDateCarbon->toDateString();
                    $dateRange = $selectedDateCarbon->translatedFormat('d F Y');
                    break;
                case 'Minggu':
                    $startDate = $selectedDateCarbon->copy()->startOfWeek()->toDateString();
                    $endDate = $selectedDateCarbon->copy()->endOfWeek()->toDateString();
                    $dateRange = Carbon::parse($startDate)->translatedFormat('d F Y') . ' - ' . Carbon::parse($endDate)->translatedFormat('d F Y');
                    break;
                case 'Bulan':
                    $startDate = $selectedDateCarbon->copy()->startOfMonth()->toDateString();
                    $endDate = $selectedDateCarbon->copy()->endOfMonth()->toDateString();
                    $dateRange = $selectedDateCarbon->translatedFormat('F Y');
                    break;
                case 'Tahun':
                    $startDate = $selectedDateCarbon->copy()->startOfYear()->toDateString();
                    $endDate = $selectedDateCarbon->copy()->endOfYear()->toDateString();
                    $dateRange = 'Tahun ' . $selectedDateCarbon->year;
                    break;
                default:
                    $startDate = $selectedDateCarbon->toDateString();
                    $endDate = $selectedDateCarbon->toDateString();
                    $dateRange = $selectedDateCarbon->translatedFormat('d F Y');
            }
        }

        $workerName = $selectedWorker === 'semua' ? 'Semua Pekerja' : (User::find($selectedWorker)?->name ?? 'Unknown');
        $methodName = match ($selectedMethod) {
            'semua' => 'Semua Metode',
            'pesanan-reguler' => 'Pesanan Reguler',
            'pesanan-kotak' => 'Pesanan Kotak',
            'siap-beli' => 'Siap Saji',
            default => 'Semua Metode',
        };

        // Route to appropriate export method based on reportContent
        switch ($reportContent) {
            case 'produksi':
                return $this->exportProduksi($startDate, $endDate, $dateRange, $selectedWorker, $selectedMethod, $workerName, $methodName);
            case 'berhasil':
                return $this->exportBerhasil($startDate, $endDate, $dateRange, $selectedWorker, $selectedMethod, $workerName, $methodName);
            case 'gagal':
                return $this->exportGagal($startDate, $endDate, $dateRange, $selectedWorker, $selectedMethod, $workerName, $methodName);
            default:
                return $this->exportProduksi($startDate, $endDate, $dateRange, $selectedWorker, $selectedMethod, $workerName, $methodName);
        }
    }

    private function exportProduksi($startDate, $endDate, $dateRange, $selectedWorker, $selectedMethod, $workerName, $methodName)
    {
        $productionsQuery = Production::with(['details.product', 'workers.worker'])
            ->whereBetween('start_date', [$startDate, $endDate])
            ->where('is_finish', true);

        if ($selectedWorker !== 'semua') {
            $productionsQuery->whereHas('workers', fn($q) => $q->where('user_id', $selectedWorker));
        }

        if ($selectedMethod !== 'semua') {
            $productionsQuery->where('method', $selectedMethod);
        }

        $productions = $productionsQuery->orderBy('start_date', 'desc')->get();

        $productionData = [];
        foreach ($productions as $production) {
            foreach ($production->details as $detail) {
                $productionData[] = [
                    'date' => Carbon::parse($production->start_date)->translatedFormat('d F Y'),
                    'product' => $detail->product->name ?? '-',
                    'total' => $detail->quantity_get + $detail->quantity_fail,
                    'success' => $detail->quantity_get,
                    'fail' => $detail->quantity_fail,
                    'workers' => $production->workers->map(fn($w) => $w->worker->name ?? '-')->join(', '),
                    'method' => $production->method ? ucfirst(str_replace('-', ' ', $production->method)) : 'Tidak Diketahui',
                ];
            }
        }

        $pdf = Pdf::loadView('pdf.laporan-produksi-produksi', [
            'dateRange' => $dateRange,
            'workerName' => $workerName,
            'methodName' => $methodName,
            'productionData' => $productionData,
        ]);

        return $pdf->stream('laporan-produksi-' . now()->format('Y-m-d') . '.pdf');
    }

    private function exportBerhasil($startDate, $endDate, $dateRange, $selectedWorker, $selectedMethod, $workerName, $methodName)
    {
        $productionsQuery = Production::with(['details.product', 'workers.worker'])
            ->whereBetween('start_date', [$startDate, $endDate])
            ->where('is_finish', true);

        if ($selectedWorker !== 'semua') {
            $productionsQuery->whereHas('workers', fn($q) => $q->where('user_id', $selectedWorker));
        }

        if ($selectedMethod !== 'semua') {
            $productionsQuery->where('method', $selectedMethod);
        }

        $productions = $productionsQuery->orderBy('start_date', 'desc')->get();

        $productionData = [];
        foreach ($productions as $production) {
            foreach ($production->details as $detail) {
                if ($detail->quantity_get > 0) {
                    $productionData[] = [
                        'date' => Carbon::parse($production->start_date)->translatedFormat('d F Y'),
                        'product' => $detail->product->name ?? '-',
                        'success' => $detail->quantity_get,
                        'workers' => $production->workers->map(fn($w) => $w->worker->name ?? '-')->join(', '),
                        'method' => $production->method ? ucfirst(str_replace('-', ' ', $production->method)) : 'Tidak Diketahui',
                    ];
                }
            }
        }

        $pdf = Pdf::loadView('pdf.laporan-produksi-berhasil', [
            'dateRange' => $dateRange,
            'workerName' => $workerName,
            'methodName' => $methodName,
            'productionData' => $productionData,
        ]);

        return $pdf->stream('laporan-produksi-berhasil-' . now()->format('Y-m-d') . '.pdf');
    }

    private function exportGagal($startDate, $endDate, $dateRange, $selectedWorker, $selectedMethod, $workerName, $methodName)
    {
        $productionsQuery = Production::with(['details.product', 'workers.worker'])
            ->whereBetween('start_date', [$startDate, $endDate])
            ->where('is_finish', true);

        if ($selectedWorker !== 'semua') {
            $productionsQuery->whereHas('workers', fn($q) => $q->where('user_id', $selectedWorker));
        }

        if ($selectedMethod !== 'semua') {
            $productionsQuery->where('method', $selectedMethod);
        }

        $productions = $productionsQuery->orderBy('start_date', 'desc')->get();

        $productionData = [];
        foreach ($productions as $production) {
            foreach ($production->details as $detail) {
                if ($detail->quantity_fail > 0) {
                    $productionData[] = [
                        'date' => Carbon::parse($production->start_date)->translatedFormat('d F Y'),
                        'product' => $detail->product->name ?? '-',
                        'fail' => $detail->quantity_fail,
                        'workers' => $production->workers->map(fn($w) => $w->worker->name ?? '-')->join(', '),
                        'method' => $production->method ? ucfirst(str_replace('-', ' ', $production->method)) : 'Tidak Diketahui',
                    ];
                }
            }
        }

        $pdf = Pdf::loadView('pdf.laporan-produksi-gagal', [
            'dateRange' => $dateRange,
            'workerName' => $workerName,
            'methodName' => $methodName,
            'productionData' => $productionData,
        ]);

        return $pdf->stream('laporan-produksi-gagal-' . now()->format('Y-m-d') . '.pdf');
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

    public function kasirExcel(Request $request)
    {
        set_time_limit(120);
        $reportContent = $request->get('reportContent', 'keuangan');
        $filterPeriod = $request->get('filterPeriod', 'Hari');
        $selectedDate = $request->get('selectedDate', now()->toDateString());
        $customStartDate = $request->get('customStartDate');
        $customEndDate = $request->get('customEndDate');
        $selectedWorker = $request->get('selectedWorker', 'semua');
        $selectedMethod = $request->get('selectedMethod', 'semua');

        $filename = 'Laporan_Kasir_' . ucfirst($reportContent) . '_' . Carbon::parse($selectedDate)->format('d-M-Y') . '.xlsx';

        return Excel::download(
            new KasirExport($reportContent, $filterPeriod, $selectedDate, $customStartDate, $customEndDate, $selectedWorker, $selectedMethod),
            $filename
        );
    }

    public function produksiExcel(Request $request)
    {
        set_time_limit(120);
        $reportContent = $request->get('reportContent', 'produksi');
        $filterPeriod = $request->get('filterPeriod', 'Hari');
        $selectedDate = $request->get('selectedDate', now()->toDateString());
        $customStartDate = $request->get('customStartDate');
        $customEndDate = $request->get('customEndDate');
        $selectedWorker = $request->get('selectedWorker', 'semua');
        $selectedMethod = $request->get('selectedMethod', 'semua');

        $filename = 'Laporan_Produksi_' . ucfirst($reportContent) . '_' . Carbon::parse($selectedDate)->format('d-M-Y') . '.xlsx';

        return Excel::download(
            new ProduksiExport($reportContent, $filterPeriod, $selectedDate, $customStartDate, $customEndDate, $selectedWorker, $selectedMethod),
            $filename
        );
    }

    public function inventoriExcel(Request $request)
    {
        set_time_limit(120);
        $reportContent = $request->get('reportContent', '');
        $filterPeriod = $request->get('filterPeriod', 'Hari');
        $selectedDate = $request->get('selectedDate', now()->toDateString());
        $customStartDate = $request->get('customStartDate');
        $customEndDate = $request->get('customEndDate');
        $selectedWorker = $request->get('selectedWorker', 'semua');

        $filename = 'Export_Inventori_' . ucfirst($reportContent) . '_' . Carbon::parse($selectedDate)->format('d-M-Y') . '.xlsx';

        return Excel::download(
            new InventoriExport($reportContent, $filterPeriod, $selectedDate, $customStartDate, $customEndDate, $selectedWorker),
            $filename
        );
    }

    public function generateStrukPDF($id)
    {
        set_time_limit(120);

        $transaction = Transaction::with([
            'user',
            'details.product',
            'payments.channel',
            'refund.channel',
        ])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.struk', [
            'transaction' => $transaction,
        ])->setPaper([0, 0, 227, 841.89], 'portrait'); // A4 height untuk auto-adjust

        // Stream PDF untuk ditampilkan di browser (bukan download)
        return $pdf->stream('struk-' . $transaction->invoice_number . '.pdf');
    }
}