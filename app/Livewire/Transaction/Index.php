<?php

namespace App\Livewire\Transaction;

use App\Models\Product;
use App\Models\Shift;
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
    public $method = 'pesanan-reguler';
    public array $cart = [];
    public $todayShiftId;
    public $todayShiftNumber;
    public $todayShiftStatus;
    public $todayShiftStartTime;
    public $todayShiftEndTime;
    public $todayShiftOpenedBy;
    public $todayShiftClosedBy;
    public $initialCash = 0;
    public $finalCash = 0;
    public $receivedCash = 0;
    public $discountToday = 0;
    public $expectedCash = 0;
    public $openShiftModal = false;
    public $closeShiftModal = false;
    public $finishShiftModal = false;

    public $historyShifts = [];
    public $showHistoryShiftModal = false;
    public $searchHistoryShift = '';
    public $showDetailHistoryShiftModal = false;
    public $selectedShiftId = null;
    public $selectedShift;

    protected $queryString = ['method'];

    protected $listeners = [
        'refreshTransactions' => '$refresh',
        'delete'
    ];

    public function mount()
    {
        View::share('title', 'Transaksi');
        View::share('mainTitle', 'Kasir');
        if (session()->has('success')) {
            $this->alert('success', session('success'));
        }
        $this->method = session('method', 'pesanan-reguler');
        $todayShift = \App\Models\Shift::whereDate('start_time', now())
            ->where('status', 'open')
            ->latest()->first();
        $transaction = \App\Models\Transaction::whereDate('start_date', now())
            ->whereHas('payments', function ($query) {
                $query->where('payment_method', 'tunai');
            })
            ->sum('total_amount');
        if ($todayShift) {
            $this->todayShiftId = $todayShift->id;
            $this->todayShiftNumber = $todayShift->shift_number;
            $this->todayShiftStatus = $todayShift->status;
            $this->todayShiftStartTime = $todayShift->start_time;
            $this->todayShiftOpenedBy = $todayShift->opened_by ? $todayShift->openedBy->name : 'System';
            $this->initialCash = $todayShift->initial_cash;
            $this->finalCash = $todayShift->final_cash;
            $this->receivedCash = $transaction ?? 0;
            $this->discountToday = 0;
            $this->expectedCash = $todayShift->initial_cash + $this->receivedCash - $this->discountToday;
        } else {
            $this->todayShiftId = null;
            $this->todayShiftNumber = null;
            $this->todayShiftStatus = 'closed';
            $this->todayShiftStartTime = null;
            $this->todayShiftOpenedBy = 'System';
        }
    }

    public function openShift()
    {
        if ($this->todayShiftId) {
            $this->alert('warning', 'Shift hari ini sudah dibuka!');
            return;
        }

        $shift = \App\Models\Shift::create([
            'opened_by' => Auth::id(),
            'start_time' => now(),
            'status' => 'open',
            'initial_cash' => $this->initialCash,
        ]);
        $shift->refresh();
        $this->todayShiftId = $shift->id;
        $this->todayShiftNumber = $shift->shift_number;
        $this->todayShiftStatus = $shift->status;
        $this->todayShiftStartTime = $shift->start_time;
        $this->todayShiftOpenedBy = $shift->openedBy ? $shift->openedBy->name : 'System';
        $this->initialCash = $shift->initial_cash;
        $this->finalCash = 0;
        $this->receivedCash = 0;
        $this->discountToday = 0;
        $this->expectedCash = $this->initialCash + $this->receivedCash - $this->discountToday;
        $this->openShiftModal = false;

        $this->alert('success', 'Sesi berhasil dibuka!');
    }

    public function closeShift()
    {
        if (!$this->todayShiftId) {
            $this->alert('warning', 'Tidak ada sesi yang dibuka hari ini!');
            return;
        }

        $shift = \App\Models\Shift::find($this->todayShiftId);
        if (!$shift) {
            $this->alert('warning', 'Sesi tidak ditemukan!');
            return;
        }

        $shift->update([
            'closed_by' => Auth::id(),
            'end_time' => now(),
            'status' => 'closed',
            'final_cash' => $this->finalCash,
        ]);
        $shift->refresh();
        $this->todayShiftClosedBy = $shift->closedBy ? $shift->closedBy->name : 'System';
        $this->todayShiftEndTime = $shift->end_time;
        $this->todayShiftId = null;
        $this->todayShiftNumber = null;
        $this->todayShiftStatus = 'closed';
        $this->todayShiftStartTime = null;
        $this->closeShiftModal = false;
        $this->finishShiftModal = true;

        $this->alert('success', 'Sesi berhasil ditutup!');
    }

    public function openHistoryShiftModal()
    {
        $this->historyShifts = \App\Models\Shift::with(['openedBy', 'closedBy'])
            ->orderBy('shift_number')
            ->get();
        $this->searchHistoryShift = '';

        $this->showHistoryShiftModal = true;
    }

    public function updatedSearchHistoryShift($value)
    {
        $this->historyShifts = \App\Models\Shift::with(['openedBy', 'closedBy'])
            ->when($value, function ($query) use ($value) {
                $query->where('shift_number', 'like', '%' . $value . '%')
                    ->orWhere('status', 'like', '%' . $value . '%');
            })
            ->orderBy('shift_number')
            ->get();
    }

    public function viewShift($id)
    {
        $this->selectedShiftId = $id;
        $shift = Shift::findOrFail($this->selectedShiftId);
        $this->selectedShift = $shift;
        $this->showDetailHistoryShiftModal = true;
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

    public function updatedMethod($value)
    {
        session()->put('method', $value);
        $this->cart = [];
    }

    // Untuk handle increment/decrement
    public function incrementItem($itemId)
    {
        if (isset($this->cart[$itemId])) {
            $this->cart[$itemId]['quantity']++;
            // Jika quantity nya sudah sama dengan stock, tidak akan menambah quantity lagi
            if ($this->method == 'siap-beli') {
                if ($this->cart[$itemId]['quantity'] >= $this->cart[$itemId]['stock']) {
                    $this->cart[$itemId]['quantity'] = $this->cart[$itemId]['stock'];
                }
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
            if ($this->method == 'siap-beli') {
                if ($product->stock <= 0) {
                    $this->alert('warning', 'Stok produk ini sudah habis!');
                    return;
                }
            }
            $this->cart[$productId] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
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
                ->when($this->method, function ($query) {
                    $query->whereJsonContains('method', $this->method);
                })
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