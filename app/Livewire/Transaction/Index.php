<?php

namespace App\Livewire\Transaction;

use App\Models\Product;
use Livewire\Component;
use Mike42\Escpos\Printer;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Spatie\Activitylog\Models\Activity;

class Index extends Component
{
    use WithPagination, LivewireAlert;

    public $activityLogs = [];
    public $filterStatus = '';
    public $search = '';
    public $showHistoryModal = false;
    public $viewMode = 'grid';
    public $method = 'pesanan-reguler';
    public array $cart = [];

    protected $queryString = ['viewMode', 'method'];

    protected $listeners = [
        'refreshTransactions' => '$refresh',
        'delete'
    ];

    public function mount()
    {
        View::share('title', 'Transaksi');
        if (session()->has('success')) {
            $this->alert('success', session('success'));
        }
        $this->viewMode = session('viewMode', 'grid');
        $this->method = session('method', 'pesanan-reguler');
    }

    public function riwayatPembaruan()
    {
        $this->activityLogs = Activity::inLog('transactions')
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function cetakInformasi()
    {
        return redirect()->route('produk.pdf', [
            'search' => $this->search,
        ]);
    }

    public function updatedViewMode($value)
    {
        session()->put('viewMode', $value);
    }

    public function updatedMethod($value)
    {
        session()->put('method', $value);
    }

    // Untuk handle increment/decrement
    public function incrementItem($itemId)
    {
        if (isset($this->cart[$itemId])) {
            $this->cart[$itemId]['quantity']++;
            // Jika quantity nya sudah sama dengan stock, tidak akan menambah quantity lagi
            if ($this->cart[$itemId]['quantity'] >= $this->cart[$itemId]['stock']) {
                $this->cart[$itemId]['quantity'] = $this->cart[$itemId]['stock'];
            }
        }
    }

    public function decrementItem($itemId)
    {
        if (isset($this->cart[$itemId]) && $this->cart[$itemId]['quantity'] > 1) {
            $this->cart[$itemId]['quantity']--;
        } else {
            unset($this->cart[$itemId]);
        }
    }

    // Update fungsi addToCart
    public function addToCart($productId)
    {
        $product = Product::find($productId);

        if (isset($this->cart[$productId])) {
            // Jika produk sudah ada di keranjang, tingkatkan kuantitasnya
            $this->cart[$productId]['quantity']++;
        } else {
            if ($product->stock <= 0) {
                $this->alert('warning', 'Stok produk ini sudah habis!');
                return;
            }
            $this->cart[$productId] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->pcs > 1 ? $product->pcs_price : $product->price,
                'quantity' => 1,
                'stock' => $product->stock,
            ];
        }
    }

    public function removeItem($productId)
    {
        if (isset($this->cart[$productId])) {
            unset($this->cart[$productId]);
        }
    }

    public function clearCart()
    {
        $this->cart = [];
    }

    // Perhitungan total
    protected function getTotalProperty()
    {
        return collect($this->cart)->reduce(fn($carry, $item) =>
        $carry + ($item['price'] * $item['quantity']), 0);
    }

    public function checkout()
    {
        if (empty($this->cart)) {
            $this->alert('warning', 'Keranjang belanja masih kosong!');
            return;
        }

        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'total_amount' => $this->getTotalProperty(),
            'method' => $this->method,
            'status' => 'temp',
        ]);

        foreach ($this->cart as $item) {
            $transaction->details()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        $this->cart = [];

        return redirect()->route('transaksi.buat-pesanan', ['id' => $transaction->id]);
    }

    public function render()
    {
        return view('livewire.transaction.index', [
            "products" => Product::with(['product_categories', 'product_compositions', 'reviews'])
                ->where('method', $this->method)
                ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                ->paginate(10),
        ]);
    }

    // public function printReceipt($transactionId)
    // {
    //     $this->printTransaction = Transaction::with(['user', 'details.product.productions'])
    //         ->find($transactionId);
    //     $this->showPrintModal = true;
    // }

    // public function printReport()
    // {
    //     // Susun URL dengan parameter filter
    //     $url = route('transaksi.laporan', [
    //         'startDate' => $this->startDate,
    //         'endDate' => $this->endDate,
    //         'search' => $this->search,
    //         'typeFilter' => $this->typeFilter,
    //         'paymentStatusFilter' => $this->paymentStatusFilter,
    //     ]);

    //     // Dispatch event untuk membuka URL PDF di tab baru
    //     $this->dispatch('open-pdf', ['url' => $url]);
    // }


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
