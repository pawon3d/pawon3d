<?php

namespace App\Livewire\Transaction;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\PaymentChannel;
use App\Models\PointsHistory;
use App\Models\Transaction;
use App\Services\NotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Livewire\Component;

class RincianPesanan extends Component
{
    use \Jantinnerezo\LivewireAlert\LivewireAlert, \Livewire\WithFileUploads;

    public $transactionId;

    public $paymentImage;

    public $details = [];

    public $paymentChannels = [];

    public $production;

    public $payments;

    public $totalPayment = 0;

    public $paymentChannelId = '';

    public $paymentMethod = '';

    public $paymentBank = '';

    public $paymentAccount = '';

    public $paymentAccountNumber;

    public $paymentAccountName;

    public $image;

    public $totalAmount = 0;

    public $paidAmount = 0;

    public $transaction;

    public $total_quantity_plan;

    public $total_quantity_get;

    public $percentage;

    public $showPrintModal = false;

    public $showImage = false;

    public $showStruk = false;

    public $phoneNumber = '';

    public $pembayaranPertama;

    public $pembayaranKedua;

    public $sisaPembayaranPertama;

    public $kembalian;

    public $uploadModal = false;

    public $uploadImage = null;

    public $paymentId;

    public $previewUploadImage = null;

    public $refundModal = false;

    public $refundReason = '';

    public $refundProofImage = null;

    public $refundMethod = '';

    public $refundPaymentChannel = '';

    public $refundAccountNumber = '';

    public $isRefundReadOnly = false;

    public $noteModal = false;

    public $note = '';

    public $pointsUsed = 0;

    public $availablePoints = 0;

    public $customer;

    protected $listeners = [
        'deleteTransaction' => 'deleteTransaction',
        'showStrukChanged',
    ];

    public function updatedShowStruk($value)
    {
        $this->dispatch('showStrukChanged', show: $value);
    }

    public function mount($id)
    {
        View::share('title', 'Rincian Pesanan');
        View::share('mainTitle', 'Kasir');

        if (session()->has('success')) {
            $this->alert('success', session('success'));
            if (session()->has('print')) {
                $this->showStruk = true;
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
        $transaction = \App\Models\Transaction::with(['details', 'user', 'payments', 'payments.channel', 'production', 'refund', 'refund.channel'])->find($id);
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
        $this->phoneNumber = $transaction->phone ?? '';
        $this->production = ! empty($transaction->production) ? $transaction->production : null;

        // Load customer and points
        if ($transaction->customer_id) {
            $this->customer = \App\Models\Customer::find($transaction->customer_id);
            $this->availablePoints = $this->customer ? $this->customer->points : 0;
        }
        $this->pointsUsed = $transaction->points_used ?? 0;

        if ($transaction) {
            $this->details = $transaction->details->mapWithKeys(function ($detail) {
                return [
                    (string) $detail->product_id => [
                        'product_id' => $detail->product_id,
                        'quantity' => $detail->quantity,
                        'price' => $detail->price,
                        'name' => $detail->product->name,
                        'stock' => $detail->product->stock,
                        'refund_quantity' => $detail->refund_quantity ?? 0,
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

    public function incrementItem($itemId)
    {
        if (isset($this->details[$itemId])) {
            $this->details[$itemId]['refund_quantity']++;
            if ($this->details[$itemId]['refund_quantity'] > $this->details[$itemId]['quantity']) {
                $this->details[$itemId]['refund_quantity'] = $this->details[$itemId]['quantity'];
                $this->alert('warning', 'Kuantitas tidak dapat melebihi jumlah yang dibeli: '.$this->details[$itemId]['quantity']);
            }
        }
    }

    public function decrementItem($itemId)
    {
        if (isset($this->details[$itemId]) && $this->details[$itemId]['refund_quantity'] > 0) {
            $this->details[$itemId]['refund_quantity']--;
        } else {
            $this->details[$itemId]['refund_quantity'] = 0;
        }
    }

    protected function getRefundTotalProperty()
    {
        return collect($this->details)->sum(function ($item) {
            return $item['price'] * $item['refund_quantity'];
        });
    }

    public function getRemainingAmountProperty()
    {
        if ($this->totalAmount <= 0) {
            return 0;
        }

        // Total tagihan setelah diskon poin
        $totalAfterPoints = $this->totalAmount - ($this->transaction->points_discount ?? 0);

        return max(0, $totalAfterPoints - ($this->totalPayment ?? 0));
    }

    public function getChangeAmountProperty()
    {
        if ($this->paymentMethod !== 'tunai') {
            return 0;
        }

        $sisaTagihan = $this->remainingAmount;

        return max(0, $this->paidAmount - $sisaTagihan);
    }

    public function updatedPaidAmount($value)
    {
        $this->paidAmount = $value;
    }

    public function updatedPointsUsed($value)
    {
        // Pastikan nilai adalah angka
        $value = (int) $value;

        // Validasi: tidak boleh negatif
        if ($value < 0) {
            $this->pointsUsed = 0;
            $this->alert('warning', 'Poin tidak boleh negatif.');

            return;
        }

        // Validasi: tidak boleh melebihi poin yang tersedia
        if ($value > $this->availablePoints) {
            $this->pointsUsed = $this->availablePoints;
            $this->alert('warning', 'Poin yang digunakan melebihi poin tersedia.');

            return;
        }

        // Validasi: harus kelipatan 10
        if ($value % 10 != 0) {
            $this->pointsUsed = floor($value / 10) * 10;
            $this->alert('warning', 'Poin harus kelipatan 10.');

            return;
        }

        // Validasi: maksimal poin yang bisa dipakai = sisa tagihan / 100 (1 poin = Rp 100)
        $sisaTagihan = $this->remainingAmount + ($this->transaction->points_discount ?? 0);
        $maxPoints = floor($sisaTagihan / 100);
        if ($value > $maxPoints) {
            $this->pointsUsed = floor($maxPoints / 10) * 10;
            $this->alert('warning', 'Poin yang digunakan tidak boleh melebihi sisa tagihan.');

            return;
        }

        $this->pointsUsed = $value;
    }

    public function applyPoints()
    {
        if (! $this->customer) {
            $this->alert('warning', 'Customer tidak ditemukan.');

            return;
        }

        $pointsChange = $this->pointsUsed - ($this->transaction->points_used ?? 0);

        // Jika mengurangi poin yang digunakan, kembalikan poin ke customer
        if ($pointsChange < 0) {
            $this->customer->increment('points', abs($pointsChange));
        }
        // Jika menambah poin yang digunakan, kurangi poin customer
        elseif ($pointsChange > 0) {
            if ($this->customer->points < $pointsChange) {
                $this->alert('warning', 'Poin customer tidak mencukupi.');

                return;
            }
            $this->customer->decrement('points', $pointsChange);
        }

        // Update transaction
        $this->transaction->update([
            'points_used' => $this->pointsUsed,
            'points_discount' => $this->pointsUsed * 100,
        ]);

        $this->availablePoints = $this->customer->points;
        $this->alert('success', 'Poin berhasil diperbarui.');

        return redirect()->route('transaksi.rincian-pesanan', ['id' => $this->transactionId]);
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
            $this->paymentAccount = $channel->account_name.' - '.$channel->account_number;
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
        }
        if ($this->transaction->status == 'Draft' || $this->transaction->status == 'temp') {
            if ($this->paidAmount < 0.5 * $this->transaction->total_amount) {
                $this->alert('warning', 'Jumlah pembayaran minimal 50% dari sisa.');

                return;
            } else {
                if ($this->paidAmount >= $this->totalAmount) {
                    $status = 'Lunas';
                    $this->paidAmount = $this->totalAmount;
                } else {
                    $status = 'Belum Lunas';
                }
            }
        } else {
            if (! empty($this->payments) && ($this->paidAmount < ($this->totalAmount - $this->totalPayment))) {
                $this->alert('warning', 'Jumlah pembayaran harus lunas.');

                return;
            } else {
                $status = 'Lunas';
                $this->paidAmount = $this->totalAmount - $this->totalPayment;
            }
        }
        $this->validate([
            'paymentMethod' => 'nullable|string',
            'paymentBank' => 'nullable|string',
            'paymentAccount' => 'nullable|string',
        ]);

        $transaction = \App\Models\Transaction::find($this->transactionId);
        if ($transaction) {
            if ($transaction->method == 'siap-beli' && ($this->transaction->status == 'Draft' || $this->transaction->status == 'temp')) {
                $transaction->details()->each(function ($detail) {
                    // jika produk kurang dari quantity yang dibeli, tampilkan pesan error
                    if ($detail->product->stock < $detail->quantity) {
                        $this->alert('warning', 'Stok produk '.$detail->product->name.' tidak mencukupi untuk quantity yang dibeli.');

                        return;
                    }
                    // Kurangi stok produk sesuai quantity yang dibeli
                    $product = $detail->product;
                    if ($product) {
                        $product->decrement('stock', $detail->quantity);
                    }
                });
            }
            $transaction->update([
                'status' => $this->transaction->status == 'Draft' || $this->transaction->status == 'temp' ? 'Belum Diproses' : $this->transaction->status,
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

                // Kirim notifikasi pembayaran
                if ($status === 'Lunas') {
                    NotificationService::paymentCompleted($transaction->invoice_number, $this->paidAmount);
                } else {
                    NotificationService::paymentDownPayment($transaction->invoice_number, $this->paidAmount);
                }
            }

            // Kirim notifikasi pesanan masuk antrian
            if ($this->transaction->status === 'Draft' || $this->transaction->status === 'temp') {
                NotificationService::orderQueued($transaction->invoice_number);
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
        $fileName = 'struk-'.$this->transaction->id.'.pdf';
        Storage::disk('public')->put('struk/'.$fileName, $pdf->output());
        $pdfUrl = asset('storage/struk/'.$fileName);

        // 3. Format pesan WhatsApp
        $message = "🧾 *Struk Transaksi*\n"; // 🧾
        $message .= "\u{1F4C5} Tanggal: ".now()->format('d-m-Y H:i')."\n\n"; // 📅
        $message .= "\u{1F6D2} *Detail Pesanan:*\n"; // 🛒

        foreach ($this->transaction->details as $detail) {
            $message .= "- {$detail->product->name} x{$detail->quantity} - Rp ".number_format($detail->price)."\n";
        }

        $message .= "\n\u{1F4B0} *Total:* Rp ".number_format($this->transaction->total_amount)."\n"; // 💰
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
            $phone = '62'.substr($phone, 1);
        }

        $phone = preg_replace('/[^0-9]/', '', $phone); // pastikan format internasional, misal 628123xxxx
        $waUrl = 'https://api.whatsapp.com/send/?phone='.$phone.'&text='.urlencode($message);

        $this->dispatch('open-wa', ['url' => $waUrl]);

        return redirect()->route('transaksi.rincian-pesanan', ['id' => $this->transactionId])->with('notif', 'Struk berhasil dikirim!');
    }

    public function strukPrint()
    {
        return redirect()->route('cetak-struk', ['id' => $this->transactionId]);
    }

    public function finish()
    {
        $transaction = Transaction::find($this->transactionId);
        if ($transaction->payment_status == 'Lunas') {
            $transaction->update([
                'status' => 'Selesai',
                'end_date' => now(),
            ]);

            // Tambahkan poin ke customer yang terdaftar
            $this->addPointsToCustomer($transaction);

            // Kirim notifikasi pesanan selesai
            NotificationService::orderCompleted($transaction->invoice_number);

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

    /**
     * Tambahkan poin ke customer berdasarkan total belanja.
     * 1 poin untuk setiap kelipatan Rp 10.000.
     */
    private function addPointsToCustomer(Transaction $transaction): void
    {
        // Cek apakah customer terdaftar (memiliki customer_id atau phone yang cocok dengan customer)
        $customer = null;

        if ($transaction->customer_id) {
            $customer = Customer::find($transaction->customer_id);
        } elseif ($transaction->phone) {
            $customer = Customer::where('phone', $transaction->phone)->first();
        }

        if (! $customer) {
            return;
        }

        // Hitung total belanja (setelah diskon poin jika ada)
        $totalAmount = $transaction->total_amount - ($transaction->points_discount ?? 0);

        // Hitung poin: 1 poin per kelipatan Rp 10.000
        $pointsEarned = (int) floor($totalAmount / 10000);

        if ($pointsEarned <= 0) {
            return;
        }

        // Tentukan action berdasarkan method transaksi
        $actionMap = [
            'pesanan-reguler' => 'Pesanan Reguler',
            'pesanan-kotak' => 'Pesanan Kotak',
            'siap-beli' => 'Siap Saji',
        ];
        $action = $actionMap[$transaction->method] ?? 'Pesanan Reguler';

        // Buat history poin
        PointsHistory::create([
            'phone' => $customer->phone,
            'action' => $action,
            'points' => $pointsEarned,
            'transaction_id' => $transaction->id,
        ]);

        // Update total poin customer
        $customer->increment('points', $pointsEarned);
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
            $invoiceNumber = $transaction->invoice_number;
            $paymentStatus = $transaction->payment_status;

            $transaction->delete();
            if (! empty($transaction->payments)) {
                $payment = Payment::where('transaction_id', $this->transactionId)->get();
                $payment->each(function ($p) {
                    if ($p->image) {
                        Storage::disk('public')->delete($p->image);
                    }
                    $p->delete();
                });
            }

            // Kirim notifikasi pesanan dibatalkan
            NotificationService::orderCancelled($invoiceNumber, $paymentStatus ?? 'Belum Lunas');

            session()->flash('success', 'Transaksi berhasil dihapus.');

            return redirect()->route('transaksi');
        } else {
            $this->alert('error', 'Transaksi tidak ditemukan.');
        }
    }

    public function kembali()
    {
        return redirect()->route('transaksi.rincian-pesanan', ['id' => $this->transactionId]);
    }

    public function updatedUploadImage()
    {
        $this->validate([
            'uploadImage' => 'image|max:2048|mimes:jpg,jpeg,png',
        ]);

        // Untuk preview langsung setelah upload
        $this->previewUploadImage = $this->uploadImage->temporaryUrl();
    }

    public function showUploadModal($id)
    {
        $this->reset(['uploadImage', 'previewUploadImage', 'paymentId']);
        $payment = Payment::find($id);
        $this->paymentId = $payment->id;
        $this->uploadModal = true;
    }

    public function uploadImageStore()
    {
        $payment = Payment::find($this->paymentId);
        if ($this->uploadImage) {
            if ($payment->image) {
                Storage::disk('public')->delete($payment->image);
            }
            $path = $this->uploadImage->store('payments', 'public');
            $payment->update(['image' => $path]);
        }
        $this->uploadModal = false;

        return redirect()->route('transaksi.rincian-pesanan', ['id' => $this->transactionId])->with('success', 'Bukti Pembayaran Berhasil Diupload!');
    }

    public function downloadImage($id)
    {
        $payment = Payment::findOrFail($id);

        $path = storage_path('app/public/'.$payment->image);

        if (! file_exists($path)) {
            $this->alert('error', 'Bukti pembayaran tidak ditemukan.');
        }
        $date = \Carbon\Carbon::parse($payment->paid_at)->format('dmY');
        $customName = 'bukti-pembayaran-'.$payment->transaction->name.'-'.$payment->channel->bank_name.'-'.$date.'.'.pathinfo($path, PATHINFO_EXTENSION);

        return response()->download($path, $customName);
    }

    public function showRefundModal()
    {
        $this->refundModal = true;

        // Check if transaction already has refund
        if ($this->transaction->refund) {
            $this->isRefundReadOnly = true;
            $refund = $this->transaction->refund;
            $this->refundReason = $refund->reason;
            $this->refundMethod = $refund->refund_method;
            $this->refundPaymentChannel = $refund->payment_channel_id ?? '';
            $this->refundAccountNumber = $refund->account_number ?? '';

            // Load payment channels if transfer
            if ($refund->refund_method == 'transfer') {
                $this->paymentChannels = \App\Models\PaymentChannel::where('type', 'transfer')->where('is_active', true)->get();
            }

            // Load details with existing refund quantities
            $this->details = $this->transaction->details->mapWithKeys(function ($detail) {
                return [
                    (string) $detail->product_id => [
                        'product_id' => $detail->product_id,
                        'quantity' => $detail->quantity,
                        'price' => $detail->price,
                        'name' => $detail->product->name,
                        'stock' => $detail->product->stock,
                        'refund_quantity' => $detail->refund_quantity ?? 0,
                    ],
                ];
            })->toArray();
        } else {
            $this->isRefundReadOnly = false;
            $this->reset(['refundReason', 'refundProofImage', 'refundMethod', 'refundPaymentChannel', 'refundAccountNumber']);
            $this->details = $this->transaction->details->mapWithKeys(function ($detail) {
                return [
                    (string) $detail->product_id => [
                        'product_id' => $detail->product_id,
                        'quantity' => $detail->quantity,
                        'price' => $detail->price,
                        'name' => $detail->product->name,
                        'stock' => $detail->product->stock,
                        'refund_quantity' => 0,
                    ],
                ];
            })->toArray();
        }
    }

    public function updatedRefundProofImage()
    {
        $this->validate([
            'refundProofImage' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
        ]);
    }

    public function updatedRefundMethod($value)
    {
        if ($value == 'transfer') {
            $this->paymentChannels = \App\Models\PaymentChannel::where('type', 'transfer')->where('is_active', true)->get();
        }
    }

    public function refundStore()
    {
        // Don't process if read-only mode
        if ($this->isRefundReadOnly) {
            return;
        }

        // Validate all fields
        $this->validate([
            'refundReason' => 'required|string',
            'refundProofImage' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
            'refundMethod' => 'required|in:tunai,transfer',
            'refundPaymentChannel' => $this->refundMethod == 'transfer' ? 'required' : 'nullable',
            'refundAccountNumber' => $this->refundMethod == 'transfer' ? 'required|string' : 'nullable',
        ]);

        // Validate that at least one product has refund quantity
        $hasRefund = collect($this->details)->sum('refund_quantity') > 0;
        if (! $hasRefund) {
            $this->alert('warning', 'Pilih minimal 1 produk untuk refund.');

            return;
        }

        // Calculate total refund amount
        $totalRefund = collect($this->details)->sum(function ($item) {
            return $item['price'] * $item['refund_quantity'];
        });

        // Store refund proof image if provided
        $proofImagePath = null;
        if ($this->refundProofImage instanceof \Illuminate\Http\UploadedFile) {
            $proofImagePath = $this->refundProofImage->store('refunds', 'public');
        }

        // Create refund record
        $refund = \App\Models\Refund::create([
            'transaction_id' => $this->transactionId,
            'reason' => $this->refundReason,
            'proof_image' => $proofImagePath,
            'refund_method' => $this->refundMethod,
            'payment_channel_id' => $this->refundMethod == 'transfer' ? $this->refundPaymentChannel : null,
            'account_number' => $this->refundMethod == 'transfer' ? $this->refundAccountNumber : null,
            'total_amount' => $totalRefund,
            'refund_by_shift' => \App\Models\Shift::where('status', 'open')->latest()->first()->id ?? null,
            'refunded_at' => now(),
        ]);

        // Update transaction details with refund quantities
        foreach ($this->details as $detail) {
            if ($detail['refund_quantity'] > 0) {
                \App\Models\TransactionDetail::where('transaction_id', $this->transactionId)
                    ->where('product_id', $detail['product_id'])
                    ->update(['refund_quantity' => $detail['refund_quantity']]);
            }
        }

        // Update transaction
        $currentShift = \App\Models\Shift::where('status', 'open')->latest()->first();
        \App\Models\Transaction::where('id', $this->transactionId)->update([
            'payment_status' => 'Refund',
            'total_refund' => $totalRefund,
            'refund_by_shift' => $currentShift?->id,
        ]);

        // Kirim notifikasi refund
        NotificationService::refundProcessed($this->transaction->invoice_number, $totalRefund);

        $this->refundModal = false;
        $this->alert('success', 'Refund berhasil diproses.');

        return redirect()->route('transaksi.rincian-pesanan', ['id' => $this->transactionId]);
    }

    public function showNoteModal()
    {
        $this->note = $this->transaction->note ?? '';
        $this->noteModal = true;
    }

    public function saveNote()
    {
        $this->validate([
            'note' => 'nullable|string|max:500',
        ]);

        $this->transaction->update([
            'note' => $this->note,
        ]);

        $this->noteModal = false;
        $this->alert('success', 'Catatan pesanan berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.transaction.rincian-pesanan', [
            'remainingAmount' => $this->getRemainingAmountProperty(),
            'changeAmount' => $this->getChangeAmountProperty(),
            'transactionStatus' => Transaction::where('id', $this->transactionId)->whereNotIn('status', ['Gagal', 'Selesai'])->first(),
            'refundTotal' => $this->getRefundTotalProperty(),
        ]);
    }
}
