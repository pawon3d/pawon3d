<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function print($id)
    {
        $transaction = \App\Models\Transaction::with(['user', 'details.product'])->find($id);

        $pdf = Pdf::loadView('pdf.pdf', compact('transaction'))->setPaper([0, 0, 227, 400], 'portrait');

        return $pdf->stream();
    }
}