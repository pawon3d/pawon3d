<?php

namespace App\Livewire\Transaction;

use Livewire\Component;
use Mike42\Escpos\Printer;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\DummyPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class Index extends Component
{
    use WithPagination, LivewireAlert;

    public $search = '';
    public $typeFilter = 'all';
    public $paymentStatusFilter = 'all';
    public $showDetailModal = false;
    public $selectedTransaction = null;
    public $delete_id;

    public $printTransaction = null;
    public $showPrintModal = false;

    protected $listeners = [
        'refreshTransactions' => '$refresh',
        'delete'
    ];

    public function mount()
    {
        View::share('title', 'Transaksi');
    }

    public function render()
    {
        $transactions = Transaction::with(['user', 'details.product.productions'])
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->typeFilter !== 'all', function ($query) {
                $query->where('type', $this->typeFilter);
            })
            ->when($this->paymentStatusFilter !== 'all', function ($query) {
                $query->where('payment_status', $this->paymentStatusFilter);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.transaction.index', [
            'transactions' => $transactions
        ]);
    }

    public function updatePaymentStatus($transactionId, $status)
    {
        $transaction = Transaction::find($transactionId);
        $transaction->update(['payment_status' => $status]);
        $this->alert('success', 'Status pembayaran berhasil diperbarui!');
    }

    public function showDetail($transactionId)
    {
        $this->selectedTransaction = Transaction::with(['user', 'details.product.productions'])
            ->find($transactionId);
        $this->showDetailModal = true;
    }

    public function deleteTransaction($transactionId)
    {
        $this->delete_id = $transactionId;
        $this->alert('warning', 'Apakah Anda yakin ingin menghapus transaksi ini?', [
            'showConfirmButton' => true,
            'showCancelButton' => true,
            'confirmButtonText' => 'Ya, hapus',
            'cancelButtonText' => 'Batal',
            'onConfirmed' => 'delete',
            'onCancelled' => 'cancelled',
            'toast' => false,
            'position' => 'center',
            'timer' => null,
        ]);
    }

    public function delete()
    {
        $transaction = Transaction::find($this->delete_id);
        $transaction->delete();
        $this->alert('success', 'Transaksi berhasil dihapus!');
        $this->reset('delete_id');
    }

    public function printReceipt($transactionId)
    {
        $this->printTransaction = Transaction::with(['user', 'details.product.productions'])
            ->find($transactionId);
        $this->showPrintModal = true;
    }

    public function print($id)
    {
        // Ambil data transaksi lengkap

        $transaction = \App\Models\Transaction::with(['user', 'details.product'])->find($id);

        // $connector = new  WindowsPrintConnector("Microsoft Print to PDF");
        // $printer = new Printer($connector);

        // // Set header
        // $printer->setJustification(Printer::JUSTIFY_CENTER);
        // $printer->text("Struk Transaksi\n");
        // $printer->text("Tanggal: " . now()->format('d-m-Y H:i') . "\n");
        // $printer->text("------------------------------\n");

        // // Set detail transaksi
        // $printer->setJustification(Printer::JUSTIFY_LEFT);
        // $printer->text("Total: Rp " . number_format($transaction->total_amount) . "\n");
        // $printer->text("Status Pembayaran: " . $transaction->payment_status . "\n");
        // $printer->text("Tipe: " . $transaction->type . "\n");
        // $printer->text("------------------------------\n");

        // // Cetak detail produk
        // foreach ($transaction->details as $detail) {
        //     $printer->text($detail->product->name . "\n");
        //     $printer->text("Qty: " . $detail->quantity . "  Harga: Rp " . number_format($detail->price) . "\n");
        // }
        // $printer->text("------------------------------\n");
        // $printer->text("Terima kasih telah berbelanja\n");

        // // Potong kertas
        // $printer->cut();
        // $printer->close();

        $pdf = Pdf::loadView('pdf.pdf', compact('transaction'))->setPaper([0, 0, 227, 400], 'portrait');


        return $pdf->stream('struk-transaksi.pdf');
    }
}