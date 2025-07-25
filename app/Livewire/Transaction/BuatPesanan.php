<?php

namespace App\Livewire\Transaction;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\PaymentChannel;
use App\Models\Product;
use App\Models\Transaction;
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
    public $paymentMethod = '', $paymentBank = '', $paymentAccount = '', $paymentAccountNumber, $paymentAccountName, $image;
    public $totalAmount = 0;
    public $paidAmount = 0;
    public $showItemModal = false;
    public $customer;

    public $phoneCustomer, $nameCustomer;
    public $customerModal = false;


    public $name, $phone, $date, $time, $note, $method;

    protected $messages = [
        'name.required' => 'Nama harus diisi.',
        'phone.required' => 'Nomor telepon harus diisi.',
        'date.required' => 'Tanggal harus diisi.',
        'time.required' => 'Jam harus diisi'
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
            'phone' => $this->phoneCustomer
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
        } else {
            $this->customer = null;
            $this->name = null;
        }
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
                'name' => $this->name,
                'phone' => $this->phone,
                'date' => $this->date ? \Carbon\Carbon::createFromFormat('d-m-Y', $this->date)->format('Y-m-d') : null,
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

        // Set default status
        $status = 'Belum Lunas';

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
            if ($this->paidAmount < 0.5 * $this->getTotalProperty()) {
                $this->alert('warning', 'Jumlah pembayaran minimal 50% dari sisa.');
                return;
            }
        }



        $transaction = \App\Models\Transaction::find($this->transactionId);
        if ($transaction) {
            $transaction->update([
                'name' => $this->name,
                'phone' => $this->phone,
                'date' => $this->date ? \Carbon\Carbon::createFromFormat('d-m-Y', $this->date)->format('Y-m-d') : null,
                'time' => $this->time,
                'start_date' => now(),
                'note' => $this->note,
                'method' => $this->method,
                'status' => 'Belum Diproses',
                'total_amount' => $this->getTotalProperty(),
                'payment_status' => $this->paidAmount >= $this->getTotalProperty() ? 'Lunas' : 'Belum Lunas',
            ]);

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
            }

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
            $transaction->delete();
            session()->flash('success', 'Transaksi berhasil dibatalkan.');
            return redirect()->route('transaksi');
        } else {
            $this->alert('error', 'Transaksi tidak ditemukan.');
        }
    }
    public function render()
    {
        return view('livewire.transaction.buat-pesanan', [
            'products' => Product::with(['product_categories', 'product_compositions', 'reviews'])
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
