<?php

namespace App\Livewire\Transaction;

use App\Models\Payment;
use App\Models\PaymentChannel;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class RincianPesanan extends Component
{
    use \Jantinnerezo\LivewireAlert\LivewireAlert, \Livewire\WithFileUploads;

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
    public $showImage = false;

    public $pembayaranPertama, $pembayaranKedua, $sisaPembayaranPertama, $kembalian;

    protected $listeners = [
        'deleteTransaction' => 'deleteTransaction',
    ];


    public function mount($id)
    {
        View::share('title', 'Rincian Pesanan');

        if (session()->has('success')) {
            $this->alert('success', session('success'));
            if (session()->has('print')) {
                $this->showPrintModal = true;
                session()->forget('print');
            }
        }
        if (session()->has('notif')) {
            $this->alert('success', 'Notifikasi', [
                'toast' => true,
                'position' => 'top-end',
                'timer' => null,
                'showConfirmButton' => false,
                'showCancelButton' => false,
                'showCloseButton' => true,
                'icon' => null,
                'text' => session('notif'),
                'background' => '#666666',
                'customClass' => [
                    'title' => 'text-color-white text-size-sm text-position-center',
                    'popup' => '',
                    'confirmButton' => '',
                    'cancelButton' => '',
                    'container' => '',
                    'htmlContainer' => 'text-color-white text-size-xs text-position-center',
                ],
            ]);
            session()->forget('notif');
        }
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

    public function updatedPaidAmount($value)
    {
        $this->paidAmount = $value;
    }

    public function updatedPaymentMethod($value)
    {
        if ($value == 'transfer') {
            $this->paymentChannels = PaymentChannel::where('type', $value)->where('is_active', true)->get();
        }
    }

    public function updatedPaymentChannelId($value)
    {
        $channel = PaymentChannel::find($value);
        if ($channel) {
            $this->paymentBank = $channel->bank_name;
            $this->paymentAccountNumber = $channel->account_number;
            $this->paymentAccountName = $channel->account_name;
            $this->paymentAccount = $channel->account_name . ' - ' . $channel->account_number;
        } else {
            $this->paymentBank = '';
            $this->paymentAccountNumber = '';
            $this->paymentAccountName = '';
        }
    }

    public function showImageModal($id)
    {
        $payment = Payment::find($id);
        if ($payment) {
            $this->paymentImage = $payment->image;
            $this->showImage = true;
        } else {
            $this->alert('warning', 'Bukti pembayaran tidak ditemukan.');
        }
    }

    public function pay()
    {
        if ($this->paymentMethod == '' && ($this->transaction->status == 'Draft' || $this->transaction->status == 'temp')) {
            $this->alert('warning', 'Metode pembayaran harus diisi.');
            return;
        } elseif ($this->paymentChannelId == '' && $this->paymentMethod == 'transfer') {
            $this->alert('warning', 'Bank Tujuan Belum Dipilih.');
            return;
            // } elseif ($this->image == null && $this->paymentMethod != 'tunai') {
            //     $this->alert('warning', 'Silakan unggah bukti pembayaran.');
            //     return;
            // }

            // sementara
        } elseif ($this->paymentMethod != '') {
            if ($this->image == null && $this->paymentMethod != 'tunai') {
                $this->alert('warning', 'Silakan unggah bukti pembayaran.');
                return;
            }
        }
        if ($this->transaction->status == 'Draft' || $this->transaction->status == 'temp') {
            if ($this->paidAmount < 0.5 * $this->transaction->total_amount) {
                $this->alert('warning', 'Jumlah pembayaran minimal 50% dari sisa.');
                return;
            } else {
                if ($this->paidAmount >= $this->totalAmount) {
                    $status = 'Lunas';
                } else {
                    $status = 'Belum Lunas';
                }
            }
        } else {
            if (!empty($this->payments) && ($this->paidAmount < ($this->totalAmount - $this->totalPayment))) {
                $this->alert('warning', 'Jumlah pembayaran harus lunas.');
                return;
            } else {
                $status = 'Lunas';
            }
        }
        $this->validate([
            'paymentMethod' => 'nullable|string',
            'paymentBank' => 'nullable|string',
            'paymentAccount' => 'nullable|string',
        ]);

        $transaction = \App\Models\Transaction::find($this->transactionId);
        if ($transaction) {
            $transaction->update([
                // 'status' => 'Sedang Diproses',
                'payment_status' => $status,
            ]);

            if ($this->paidAmount > 0 && $this->paymentMethod != '') {
                $payment = Payment::create([
                    'transaction_id' => $transaction->id,
                    'payment_channel_id' => $this->paymentChannelId != '' ? $this->paymentChannelId : null,
                    'payment_method' => $this->paymentMethod,
                    'paid_amount' => $this->paidAmount,
                    'paid_at' => now(),
                ]);

                if ($this->image instanceof \Illuminate\Http\UploadedFile) {
                    // if ($payment->image) {
                    //     Storage::disk('public')->delete($payment->image);
                    // }
                    $path = $this->image->store('payments', 'public');
                    $payment->update(['image' => $path]);
                }
            }

            session()->flash('success', 'Pesanan berhasil dibuat.');
            session()->flash('print', true);
        } else {
            session()->flash('error', 'Transaksi tidak ditemukan.');
        }

        return redirect()->route('transaksi.rincian-pesanan', ['id' => $this->transactionId]);
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
        $phone = $this->transaction->phone;
        if (str_starts_with($phone, '08')) {
            $phone = '62' . substr($phone, 1);
        }

        $phone = preg_replace('/[^0-9]/', '', $phone); // pastikan format internasional, misal 628123xxxx
        $waUrl = 'https://api.whatsapp.com/send/?phone=' . $phone . '&text=' . urlencode($message);

        $this->dispatch('open-wa', ['url' => $waUrl]);
    }

    public function strukPrint()
    {
        return redirect()->route('cetak-struk', ['id' => $this->transactionId]);
    }

    public function finish()
    {
        $transaction = \App\Models\Transaction::find($this->transactionId);
        if ($transaction->payment_status == 'Lunas') {
            $transaction->update([
                'status' => 'Selesai',
                'end_date' => now(),
            ]);
            session()->flash('success', 'Pesanan telah selesai.');
        } elseif ($transaction->payment_status == 'Belum Lunas') {
            $transaction->update([
                'status' => 'Gagal',
                'end_date' => now(),
            ]);
            session()->flash('error', 'Pesanan gagal.');
        }
        return redirect()->route('transaksi.rincian-pesanan', ['id' => $this->transactionId]);
    }

    public function delete()
    {
        $this->alert('warning', 'Apakah Anda yakin ingin menghapus transaksi ini?', [
            'showConfirmButton' => true,
            'showCancelButton' => true,
            'confirmButtonText' => 'Ya, hapus',
            'cancelButtonText' => 'Batal',
            'onConfirmed' => 'deleteTransaction',
            'onCancelled' => 'cancelled',
            'toast' => false,
            'position' => 'center',
            'timer' => null,
        ]);
    }

    public function deleteTransaction()
    {
        $transaction = Transaction::find($this->transactionId);
        if ($transaction) {
            $transaction->delete();
            if (!empty($transaction->payments)) {
                $payment = Payment::where('transaction_id', $this->transactionId)->get();
                $payment->each(function ($p) {
                    if ($p->image) {
                        Storage::disk('public')->delete($p->image);
                    }
                    $p->delete();
                });
            }
            session()->flash('success', 'Transaksi berhasil dihapus.');
            return redirect()->route('transaksi');
        } else {
            $this->alert('error', 'Transaksi tidak ditemukan.');
        }
    }

    public function render()
    {
        return view('livewire.transaction.rincian-pesanan', [
            'remainingAmount' => $this->getRemainingAmountProperty(),
            'changeAmount' => $this->getChangeAmountProperty(),
            'transactionStatus' => Transaction::where('id', $this->transactionId)->whereNotIn('status', ['Gagal', 'Selesai'])->first(),
        ]);
    }
}
