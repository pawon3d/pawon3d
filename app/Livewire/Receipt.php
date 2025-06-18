<?php

namespace App\Livewire;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Receipt extends Component
{
    public $transactionId;
    public $paymentImage;
    public $details = [];
    public $paymentChannels = [];
    public $production;
    public $payments, $totalPayment = 0;
    public $paymentChannelId = '';
    public $paymentMethod = '', $paymentBank = '', $paymentAccount = '', $paymentAccountNumber, $paymentAccountName, $image;
    public $totalAmount = 0;
    public $paidAmount = 0;
    public $transaction;
    public $total_quantity_plan, $total_quantity_get, $percentage;
    public $showPrintModal = false;
    public $showStruk = false;
    public $showImage = false;
    public $phoneNumber = '';

    public $pembayaranPertama, $pembayaranKedua, $sisaPembayaranPertama, $kembalian;
    public function mount($id)
    {
        View::share('title', 'Cetak Struk Transaksi');
        $this->transactionId = $id;
        $transaction = \App\Models\Transaction::with(['details', 'user', 'payments', 'payments.channel', 'production'])->find($id);
        $this->totalAmount = $transaction->total_amount;
        $this->totalPayment = $transaction->payments->sum('paid_amount');
        $this->payments = \App\Models\Payment::where('transaction_id', $id)->latest()->get();
        if ($this->payments->count() > 1) {
            $this->pembayaranKedua = $this->payments->first();
            $this->pembayaranPertama = $this->payments->last();
            $this->sisaPembayaranPertama = $this->totalAmount - $this->pembayaranPertama->paid_amount;
            if ($this->pembayaranKedua->paid_amount > $this->sisaPembayaranPertama) {
                $this->kembalian = $this->pembayaranKedua->paid_amount - $this->sisaPembayaranPertama;
            } else {
                $this->kembalian = 0;
            }
        } elseif ($this->payments->count() == 1) {
            $this->pembayaranPertama = $this->payments->first();
            $this->kembalian = $this->pembayaranPertama->paid_amount - $this->totalAmount;
        }

        $this->transaction = $transaction;
        $this->production = !empty($transaction->production) ? $transaction->production : null;
        if ($transaction) {
            $this->details = $transaction->details->mapWithKeys(function ($detail) {
                return [
                    (string) $detail->product_id => [
                        'product_id' => $detail->product_id,
                        'quantity' => $detail->quantity,
                        'price' => $detail->price,
                        'name' => $detail->product->name,
                        'stock' => $detail->product->stock,
                    ],
                ];
            })->toArray();
            $this->total_quantity_plan = $this->transaction->details->sum('quantity');

            // Hitung total quantity_get dari production
            $this->total_quantity_get = 0;

            if ($this->production && $this->production->details) {
                foreach ($this->transaction->details as $detail) {
                    $productId = $detail->product_id;
                    $planQty = $detail->quantity;

                    $prodDetail = $this->production->details->firstWhere('product_id', $productId);

                    if ($prodDetail) {
                        // Ambil nilai minimum agar tidak melebihi rencana
                        $qtyGet = min($prodDetail->quantity_get, $planQty);
                        $this->total_quantity_get += $qtyGet;
                    }
                }
            }

            // Hitung persentase progres produksi
            $this->percentage = $this->total_quantity_plan > 0
                ? ($this->total_quantity_get / $this->total_quantity_plan) * 100
                : 0;

            if ($this->percentage > 100) {
                $this->percentage = 100;
            }
        } else {
            session()->flash('error', 'Transaksi tidak ditemukan.');
            return redirect()->route('transaksi');
        }
    }
    public function getRemainingAmountProperty()
    {
        if ($this->totalAmount <= 0) return 0;
        return max(0, $this->totalAmount - ($this->totalPayment ?? 0));
    }
    public function getChangeAmountProperty()
    {
        if ($this->paymentMethod !== 'tunai') return 0;

        $sisaTagihan = $this->remainingAmount;
        return max(0, $this->paidAmount - $sisaTagihan);
    }
    public function send()
    {
        // 1. Generate PDF dan simpan ke public storage
        $pdf = Pdf::loadView('pdf.struk', [
            'transaction' => $this->transaction,
        ])->setPaper([0, 0, 227, 400], 'portrait');

        // 2. Simpan PDF ke storage
        $fileName = 'struk-' . $this->transaction->id . '.pdf';
        Storage::disk('public')->put('struk/' . $fileName, $pdf->output());
        $pdfUrl = asset('storage/struk/' . $fileName);


        // 3. Format pesan WhatsApp
        $message = "🧾 *Struk Transaksi*\n"; // 🧾
        $message .= "\u{1F4C5} Tanggal: " . now()->format('d-m-Y H:i') . "\n\n"; // 📅
        $message .= "\u{1F6D2} *Detail Pesanan:*\n"; // 🛒


        foreach ($this->transaction->details as $detail) {
            $message .= "- {$detail->product->name} x{$detail->quantity} - Rp " . number_format($detail->price) . "\n";
        }

        $message .= "\n\u{1F4B0} *Total:* Rp " . number_format($this->transaction->total_amount) . "\n"; // 💰
        $message .= "\u{1F4B3} *Status:* {$this->transaction->payment_status}\n"; // 💳

        $tipe = match ($this->transaction->method) {
            'pesanan-reguler' => 'Pesanan Reguler',
            'pesanan-kotak' => 'Pesanan Kotak',
            default => 'Siap Saji',
        };

        $message .= "\u{1F4E6} *Tipe:* {$tipe}\n\n"; // 📦
        $message .= "\u{1F64F} Terima kasih telah berbelanja!\n\n"; // 🙏
        $message .= "\u{1F4C4} *Download Struk (PDF):*\n{$pdfUrl}"; // 📄


        // 4. Kirim ke WhatsApp
        $phone = $this->phoneNumber;
        if (str_starts_with($phone, '08')) {
            $phone = '62' . substr($phone, 1);
        }

        $phone = preg_replace('/[^0-9]/', '', $phone); // pastikan format internasional, misal 628123xxxx
        $waUrl = 'https://api.whatsapp.com/send/?phone=' . $phone . '&text=' . urlencode($message);

        $this->dispatch('open-wa', ['url' => $waUrl]);

        return redirect()->route('transaksi.rincian-pesanan', ['id' => $this->transactionId])->with('notif', 'Struk berhasil dikirim!');
    }

    public function kembali()
    {
        return redirect()->route('transaksi.rincian-pesanan', ['id' => $this->transactionId]);
    }
    #[Layout('components.layouts.empty')]
    public function render()
    {
        return view('livewire.receipt', [
            'remainingAmount' => $this->getRemainingAmountProperty(),
            'changeAmount' => $this->getChangeAmountProperty(),
        ]);
    }
}
