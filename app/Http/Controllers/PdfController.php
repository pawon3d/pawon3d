<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
}