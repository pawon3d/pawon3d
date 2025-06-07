<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\IngredientCategory;
use App\Models\Material;
use App\Models\MaterialDetail;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Transaction;
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
        $endDate   = $request->get('endDate');
        $search    = $request->get('search');
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
        } elseif (!$startDate && $endDate) {
            $earliest = Transaction::orderBy('created_at', 'asc')->value('created_at');
            $earliest = $earliest ? Carbon::parse($earliest)->translatedFormat('d F Y') : 'Tidak ada data';
            $range = Carbon::parse($earliest)->translatedFormat('d F Y') . ' sampai ' . Carbon::parse($endDate)->translatedFormat('d F Y');
        } elseif ($startDate && !$endDate) {
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
                ?->supply_quantity ?? "Tidak Tersedia";
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

    public function generatePadanPDF(Request $request)
    {
        $status = $request->status ?? 'all';
        if ($request->status === 'history') {
            $padans = \App\Models\Padan::with(['details'])
                ->where('is_finish', true)
                ->when($request->search, function ($query) use ($request) {
                    return $query->where('padan_number', 'like', '%' . $request->search . '%');
                })
                ->latest()
                ->get();
            $pdf = PDF::loadView('pdf.padan', compact('padans', 'status'));
            return $pdf->download('riwayat-padan-persediaan.pdf');
        } elseif ($request->status === 'all') {
            $padans = \App\Models\Padan::with(['details'])
                ->when($request->search, function ($query) use ($request) {
                    return $query->where('padan_number', 'like', '%' . $request->search . '%');
                })
                ->latest()
                ->get();
            $pdf = PDF::loadView('pdf.padan', compact('padans', 'status'));
            return $pdf->download('daftar-padan-persediaan.pdf');
        } else {
            return redirect()->route('padan')->with('error', 'Status tidak valid.');
        }
    }
    public function generatePadanDetailPDF($id)
    {
        $padan = \App\Models\Padan::with(['details.material', 'details.unit'])
            ->findOrFail($id);
        $logName = Activity::inLog('padans')->where('subject_id', $padan->id)->latest()->first()?->causer->name ?? '-';
        $status = $padan->status;
        $padanDetails = \App\Models\PadanDetail::where('padan_id', $padan->id)
            ->with(['material', 'unit'])
            ->get();

        $pdf = PDF::loadView('pdf.padan-detail', compact('padan', 'logName', 'status', 'padanDetails'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('rincian-padan-persediaan-' . $padan->padan_number . '.pdf');
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
}