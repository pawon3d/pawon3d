<?php

namespace App\Livewire\Transaction;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\PaymentChannel;
use App\Models\Product;
use App\Models\Transaction;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Livewire\Component;
use Livewire\WithFileUploads;

class BuatPesanan extends Component
{
    use \Jantinnerezo\LivewireAlert\LivewireAlert, WithFileUploads;

    public $transactionId;

    public $search = '';

    public $transaction;

    public $details = [];

    public $paymentChannels = [];

    public $paymentChannelId = '';

    public $paymentMethod = '';

    public $paymentBank = '';

    public $paymentAccount = '';

    public $paymentAccountNumber;

    public $paymentAccountName;

    public $image;

    public $totalAmount = 0;

    public $paidAmount = 0;

    public $showItemModal = false;

    public $customer;

    public $phoneCustomer;

    public $nameCustomer;

    public $customerModal = false;

    public $name;

    public $phone;

    public $date;

    public $time;

    public $note;

    public $method;

    public $pointsUsed = 0;

    public $availablePoints = 0;

    public $paymentMethods = [];

    public $paymentGroup;

    protected $messages = [
        'name.required' => 'Nama harus diisi.',
        'phone.required' => 'Nomor telepon harus diisi.',
        'date.required' => 'Tanggal harus diisi.',
        'time.required' => 'Jam harus diisi',
    ];

    public function mount($id)
    {
        View::share('title', 'Buat Pesanan');
        View::share('mainTitle', 'Kasir');
        $this->transactionId = $id;
        $transaction = \App\Models\Transaction::find($id);
        $this->transaction = $transaction;
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
            $this->totalAmount = $transaction->total_amount;
            $this->method = $transaction->method;
            $this->paidAmount = 0.5 * $this->totalAmount;
        } else {
            session()->flash('error', 'Transaksi tidak ditemukan.');

            return redirect()->route('transaksi');
        }
    }

    public function showCustomerModal()
    {
        $this->customerModal = true;
        $this->phoneCustomer = $this->phone;
    }

    public function addCustomer()
    {
        $customer = Customer::create([
            'name' => $this->nameCustomer,
            'phone' => $this->phoneCustomer,
        ]);
        $this->customer = $customer;
        $this->name = $customer->name;
        $this->customerModal = false;
    }

    public function updatedPhone($value)
    {
        $customer = Customer::where('phone', $value)->first();
        if ($customer) {
            $this->customer = $customer;
            $this->name = $customer->name;
            $this->availablePoints = $customer->points;
            $this->pointsUsed = 0;
        } else {
            $this->customer = null;
            $this->availablePoints = 0;
            $this->pointsUsed = 0;
        }
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

        // Validasi: maksimal poin yang bisa dipakai = total tagihan / 100 (1 poin = Rp 100)
        $maxPoints = floor($this->getTotalProperty() / 100);
        if ($value > $maxPoints) {
            $this->pointsUsed = floor($maxPoints / 10) * 10;
            $this->alert('warning', 'Poin yang digunakan tidak boleh melebihi total tagihan.');

            return;
        }

        $this->pointsUsed = $value;
    }

    public function incrementItem($itemId)
    {
        if (isset($this->details[$itemId])) {
            $this->details[$itemId]['quantity']++;
            // Jika quantity nya sudah sama dengan stock, tidak akan menambah quantity lagi
            if ($this->method == 'siap-beli') {
                if ($this->details[$itemId]['quantity'] >= $this->details[$itemId]['stock']) {
                    $this->details[$itemId]['quantity'] = $this->details[$itemId]['stock'];
                    $this->alert('warning', 'Kuantitas tidak dapat melebihi stok yang tersedia: ' . $this->details[$itemId]['stock']);
                }
            }
        }
    }

    public function decrementItem($itemId)
    {
        if (isset($this->details[$itemId]) && $this->details[$itemId]['quantity'] > 1) {
            $this->details[$itemId]['quantity']--;
        } else {
            unset($this->details[$itemId]);
        }
    }

    // Update fungsi addToCart
    public function addToCart($productId)
    {
        $product = Product::find($productId);

        if (isset($this->details[$productId])) {
            $this->details[$productId]['quantity']++;
        } else {
            if ($this->method == 'siap-beli') {
                if ($product->stock <= 0) {
                    $this->alert('warning', 'Stok produk ini sudah habis!');

                    return;
                }
            }
            $this->details[$productId] = [
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
        if (isset($this->details[$productId])) {
            unset($this->details[$productId]);
        }
    }

    protected function getTotalProperty()
    {
        return collect($this->details)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    }

    public function updatedPaidAmount($value)
    {
        $this->paidAmount = $value;
    }

    public function updatedPaymentGroup($value)
    {
        if ($value == 'non-tunai') {
            $this->paymentMethods = PaymentChannel::where('group', $value)
                ->where('is_active', true)
                ->get()
                ->unique('type')
                ->values();
        }
    }

    public function updatedPaymentMethod($value)
    {
        if ($value != 'tunai') {
            $this->paymentChannels = [];
            $this->paymentChannelId = '';
            $this->paymentBank = '';
            $this->paymentAccountNumber = '';
            $this->paymentAccountName = '';
            $this->paymentAccount = '';
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
            $this->paymentAccount = $channel->account_number . ' - ' . $channel->account_name;
        } else {
            $this->paymentBank = '';
            $this->paymentAccountNumber = '';
            $this->paymentAccountName = '';
        }
    }

    public function save()
    {
        if ($this->method != 'siap-beli') {
            $this->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:15',
                'date' => 'required|date',
                'time' => 'required|date_format:H:i',
                'note' => 'nullable|string|max:500',
                'method' => 'nullable|string',
                'paymentMethod' => 'nullable|string',
                'paymentBank' => 'nullable|string',
                'paymentAccount' => 'nullable|string',
            ]);
        } else {
            $this->validate([
                'name' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:15',
                'date' => 'nullable|date',
                'time' => 'nullable|date_format:H:i',
                'note' => 'nullable|string|max:500',
                'method' => 'nullable|string',
                'paymentMethod' => 'nullable|string',
                'paymentBank' => 'nullable|string',
                'paymentAccount' => 'nullable|string',
            ]);
        }

        $transaction = \App\Models\Transaction::find($this->transactionId);
        if ($transaction) {
            $transaction->update([
                'customer_id' => $this->customer?->id,
                'name' => $this->name,
                'phone' => $this->phone,
                'date' => $this->date ? \Carbon\Carbon::createFromFormat('d M Y', $this->date)->format('Y-m-d') : null,
                'time' => $this->time,
                'start_date' => now(),
                'note' => $this->note,
                'method' => $this->method,
                'status' => 'Draft',
                'total_amount' => $this->getTotalProperty(),
            ]);

            foreach ($this->details as $detail) {
                $transaction->details()->updateOrCreate(
                    ['product_id' => $detail['product_id']],
                    [
                        'quantity' => $detail['quantity'],
                        'price' => $detail['price'],
                    ]
                );
            }

            // if ($this->paidAmount > 0 && $this->paymentMethod != '') {
            //     $payment = Payment::create([
            //         'transaction_id' => $transaction->id,
            //         'payment_channel_id' => $this->paymentChannelId != '' ? $this->paymentChannelId : null,
            //         'payment_method' => $this->paymentMethod,
            //         'paid_amount' => $this->paidAmount,
            //         'paid_at' => now(),
            //     ]);

            //     if ($this->image) {
            //         // hapuskan gambar lama jika ada
            //         if ($payment->image) {
            //             Storage::disk('public')->delete($payment->image);
            //         }
            //         $path = $this->image->store('payments', 'public');
            //         $payment->update(['image' => $path]);
            //     }
            // }

            session()->flash('success', 'Pesanan berhasil dibuat.');
        } else {
            session()->flash('error', 'Transaksi tidak ditemukan.');
        }

        return redirect()->route('transaksi.rincian-pesanan', ['id' => $this->transactionId]);
    }

    public function pay()
    {
        if ($this->method != 'siap-beli') {
            $this->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:15',
                'date' => 'required|date',
                'time' => 'required|date_format:H:i',
                'note' => 'nullable|string|max:500',
                'method' => 'nullable|string',
                'paymentMethod' => 'nullable|string',
                'paymentBank' => 'nullable|string',
                'paymentAccount' => 'nullable|string',
                'paymentGroup' => 'nullable|string',
            ]);
        } else {
            $this->validate([
                'name' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:15',
                'date' => 'nullable|date',
                'time' => 'nullable|date_format:H:i',
                'note' => 'nullable|string|max:500',
                'method' => 'nullable|string',
                'paymentMethod' => 'nullable|string',
                'paymentBank' => 'nullable|string',
                'paymentAccount' => 'nullable|string',
                'paymentGroup' => 'nullable|string',
            ]);
        }

        // Set default status
        $status = 'Belum Lunas';

        if ($this->paymentMethod == '' && ($this->transaction->status == 'Draft' || $this->transaction->status == 'temp')) {
            $this->alert('warning', 'Metode pembayaran harus diisi.');

            return;
        } elseif ($this->paymentChannelId == '' && $this->paymentMethod != 'tunai') {
            $this->alert('warning', 'Bank Tujuan Belum Dipilih.');

            return;
            // } elseif ($this->image == null && $this->paymentMethod != 'tunai') {
            //     $this->alert('warning', 'Silakan unggah bukti pembayaran.');
            //     return;
            // }

            // sementara
        }
        if ($this->transaction->status == 'Draft' || $this->transaction->status == 'temp') {
            $totalAfterPoints = $this->getTotalProperty() - ($this->pointsUsed * 100);
            if ($this->paidAmount < 0.5 * $totalAfterPoints) {
                $this->alert('warning', 'Jumlah pembayaran minimal 50% dari total setelah diskon poin.');

                return;
            }
        }

        $transaction = \App\Models\Transaction::find($this->transactionId);
        if ($transaction) {
            // Hitung total setelah diskon poin (1 poin = Rp 100)
            $totalAfterPoints = $this->getTotalProperty() - ($this->pointsUsed * 100);

            $transaction->update([
                'customer_id' => $this->customer?->id,
                'name' => $this->name,
                'phone' => $this->phone,
                'date' => $this->date ? \Carbon\Carbon::createFromFormat('d M Y', $this->date)->format('Y-m-d') : null,
                'time' => $this->time,
                'start_date' => now(),
                'note' => $this->note,
                'method' => $this->method,
                'status' => 'Belum Diproses',
                'total_amount' => $this->getTotalProperty(),
                'points_used' => $this->pointsUsed,
                'points_discount' => $this->pointsUsed * 100, // 1 poin = Rp 100
                'payment_status' => $this->paidAmount >= $totalAfterPoints ? 'Lunas' : 'Belum Lunas',
            ]);

            // Kurangi poin customer jika menggunakan poin
            if ($this->pointsUsed > 0 && $this->customer) {
                $this->customer->decrement('points', $this->pointsUsed);
            }

            if ($transaction->payment_status == 'Lunas' && $transaction->method == 'siap-beli') {
                $transaction->update([
                    'status' => 'Selesai',
                ]);
            }

            foreach ($this->details as $detail) {
                $transaction->details()->updateOrCreate(
                    ['product_id' => $detail['product_id']],
                    [
                        'quantity' => $detail['quantity'],
                        'price' => $detail['price'],
                    ]
                );
                $product = Product::find($detail['product_id']);
                if ($product) {
                    // Update stok produk jika metode adalah 'siap-beli'
                    if ($this->method == 'siap-beli') {
                        $product->decrement('stock', $detail['quantity']);
                    }
                }
            }

            if ($this->paidAmount > 0 && $this->paymentMethod != '') {
                $payment = Payment::create([
                    'transaction_id' => $transaction->id,
                    'payment_channel_id' => $this->paymentChannelId != '' ? $this->paymentChannelId : null,
                    'payment_method' => $this->paymentMethod,
                    'payment_group' => $this->paymentGroup,
                    'paid_amount' => $this->paidAmount >= $this->getTotalProperty() ? $this->getTotalProperty() : $this->paidAmount,
                    'paid_at' => now(),
                ]);

                if ($this->image) {
                    // hapuskan gambar lama jika ada
                    if ($payment->image) {
                        Storage::disk('public')->delete($payment->image);
                    }
                    $path = $this->image->store('payments', 'public');
                    $payment->update(['image' => $path]);
                }

                // Kirim notifikasi pembayaran
                if ($transaction->payment_status === 'Lunas') {
                    NotificationService::paymentCompleted($transaction->invoice_number, $this->paidAmount);
                } else {
                    NotificationService::paymentDownPayment($transaction->invoice_number, $this->paidAmount);
                }
            }

            // Kirim notifikasi pesanan masuk antrian
            NotificationService::orderQueued($transaction->invoice_number);

            session()->flash('success', 'Pesanan berhasil dibuat.');
            session()->flash('print', true);
        } else {
            session()->flash('error', 'Transaksi tidak ditemukan.');
        }

        return redirect()->route('transaksi.rincian-pesanan', ['id' => $this->transactionId]);
    }

    public function delete()
    {
        $transaction = Transaction::find($this->transactionId);
        if ($transaction) {
            $invoiceNumber = $transaction->invoice_number;
            $paymentStatus = $transaction->payment_status ?? 'Belum Lunas';
            $transaction->delete();

            // Kirim notifikasi pesanan dibatalkan
            NotificationService::orderCancelled($invoiceNumber, $paymentStatus);

            session()->flash('success', 'Transaksi berhasil dibatalkan.');

            return redirect()->route('transaksi');
        } else {
            $this->alert('error', 'Transaksi tidak ditemukan.');
        }
    }

    public function render()
    {
        return view('livewire.transaction.buat-pesanan', [
            'products' => Product::with(['product_categories', 'product_compositions'])
                ->when($this->method, function ($query) {
                    $query->whereJsonContains('method', $this->method);
                })->when($this->search, function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%');
                })
                ->get(),
            'total' => $this->getTotalProperty(),
        ]);
    }
}
