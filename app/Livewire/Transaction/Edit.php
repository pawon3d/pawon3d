<?php

namespace App\Livewire\Transaction;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\PaymentChannel;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use \Jantinnerezo\LivewireAlert\LivewireAlert, WithFileUploads;

    public $transactionId;

    public $transaction;

    public $search = '';

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

    public function mount($id)
    {
        View::share('title', 'Ubah Pesanan');
        View::share('mainTitle', 'Kasir');
        $this->transactionId = $id;
        $transaction = \App\Models\Transaction::find($id);
        if (!in_array($transaction->status, ['Draft', 'temp'])) {
            return redirect()->route('transaksi.rincian-pesanan', ['id' => $transaction->id]);
        }
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
            $this->name = $transaction->name;
            $this->phone = $transaction->phone;
            $this->date = $transaction->date ? \Carbon\Carbon::parse($transaction->date)->format('d M Y') : '';
            $this->time = $transaction->time ? \Carbon\Carbon::parse($transaction->time)->format('H:i') : '';
            $this->note = $transaction->note;
            $this->totalAmount = $transaction->total_amount;
            $this->method = $transaction->method;
            $transaction->payment = $transaction->payments->first();
            $this->image = $transaction->payment ? $transaction->payment->image : null;
            $this->paymentMethod = $transaction->payment ? $transaction->payment->payment_method : '';
            $this->paidAmount = $transaction->payment ? $transaction->payment->paid_amount : 0;
            $this->paymentChannels = PaymentChannel::where('type', $this->paymentMethod)->where('is_active', true)->get();
            $this->paymentChannelId = $transaction->payment ? $transaction->payment->payment_channel_id : '';
            $this->paymentBank = $transaction->payment ? $transaction->payment->channel->bank_name : '';
            $this->paymentAccountNumber = $transaction->payment ? $transaction->payment->channel->account_number : '';
            $this->paymentAccountName = $transaction->payment ? $transaction->payment->channel->account_name : '';
            $this->paymentAccount = $this->paymentAccountName . ' - ' . $this->paymentAccountNumber;
            $customer = Customer::where('phone', $this->phone)->first();
            if ($customer) {
                $this->customer = $customer;
                $this->name = $customer->name;
            } else {
                $this->customer = null;
                $this->name = $transaction->name ?? '';
            }
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
            // Jika produk sudah ada di keranjang, tingkatkan kuantitasnya
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
        $this->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:15',
            'date' => 'nullable|date',
            'time' => 'nullable|date_format:H:i',
            'note' => 'nullable|string|max:500',
            'method' => 'nullable|string',
        ]);

        $transaction = \App\Models\Transaction::find($this->transactionId);
        if ($transaction->status == 'temp') {
            $transaction->update([
                'status' => 'Draft',
            ]);
        }
        if ($transaction) {
            $transaction->update([
                'name' => $this->name,
                'phone' => $this->phone,
                'date' => $this->date ? \Carbon\Carbon::createFromFormat('d M Y', $this->date)->format('Y-m-d') : null,
                'time' => $this->time,
                'note' => $this->note,
                'total_amount' => $this->getTotalProperty(),
            ]);

            foreach ($this->details as $detail) {
                $product = Product::find($detail['product_id']);
                $transaction->details()->updateOrCreate(
                    ['product_id' => $detail['product_id']],
                    [
                        'quantity' => $detail['quantity'],
                        'price' => $detail['price'],
                        'pcs_capital_snapshot' => $product->pcs_capital ?? 0,
                    ]
                );
            }

            // if ($this->paidAmount > 0 && $this->paymentMethod != '') {
            //     $payment = Payment::updateOrCreate(['transaction_id' => $transaction->id], [
            //         'payment_channel_id' => $this->paymentChannelId != '' ? $this->paymentChannelId : null,
            //         'payment_method' => $this->paymentMethod,
            //         'paid_amount' => $this->paidAmount >= $this->totalAmount ? $this->totalAmount : $this->paidAmount,
            //         'paid_at' => now(),
            //     ]);

            //     if ($this->image instanceof \Illuminate\Http\UploadedFile) {
            //         // hapuskan gambar lama jika ada
            //         if ($payment->image) {
            //             Storage::disk('public')->delete($payment->image);
            //         }
            //         $path = $this->image->store('payments', 'public');
            //         $payment->update(['image' => $path]);
            //     }
            // }

            session()->flash('success', 'Pesanan berhasil diubah.');
        } else {
            session()->flash('error', 'Transaksi tidak ditemukan.');
        }

        return redirect()->route('transaksi.rincian-pesanan', ['id' => $this->transactionId]);
    }

    public function render()
    {
        return view('livewire.transaction.edit', [
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
