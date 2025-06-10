<?php

namespace App\Livewire\Transaction;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Livewire\Component;
use Livewire\WithFileUploads;

class BuatPesanan extends Component
{
    use \Jantinnerezo\LivewireAlert\LivewireAlert, WithFileUploads;
    public $transactionId;
    public $details = [];
    public $paymentMethod = '', $paymentTarget = '', $paymentAccount, $image;
    public $totalAmount = 0;
    public $paidAmount = 0;
    public $showItemModal = false;

    public $name, $phone, $date, $time, $note, $method;

    public function mount($id)
    {
        View::share('title', 'Buat Pesanan');
        $this->transactionId = $id;
        $transaction = \App\Models\Transaction::find($id);
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
            $this->paidAmount = $transaction->paid_amount;
            $this->method = $transaction->method;
        } else {
            session()->flash('error', 'Transaksi tidak ditemukan.');
            return redirect()->route('transaksi');
        }
    }


    public function incrementItem($itemId)
    {
        if (isset($this->details[$itemId])) {
            $this->details[$itemId]['quantity']++;
            // Jika quantity nya sudah sama dengan stock, tidak akan menambah quantity lagi
            if ($this->details[$itemId]['quantity'] >= $this->details[$itemId]['stock']) {
                $this->details[$itemId]['quantity'] = $this->details[$itemId]['stock'];
                $this->alert('warning', 'Kuantitas tidak dapat melebihi stok yang tersedia: ' . $this->details[$itemId]['stock']);
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
            $this->details[$productId] = [
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

    public function updatedPaymentTarget($value)
    {
        if ($value === 'BRI') {
            $this->paymentAccount = 'BRI - 0912389103';
        } elseif ($value === 'BCA') {
            $this->paymentAccount = 'BCA - 0912389103';
        } else {
            $this->paymentAccount = 'Mandiri - 0912389103';
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
            'paymentMethod' => 'nullable|string',
            'paymentTarget' => 'nullable|string',
            'paymentAccount' => 'nullable|string',
        ]);

        $transaction = \App\Models\Transaction::find($this->transactionId);
        if ($transaction) {
            $transaction->update([
                'name' => $this->name,
                'phone' => $this->phone,
                'date' => \Carbon\Carbon::createFromFormat('d-m-Y', $this->date)->format('Y-m-d'),
                'time' => $this->time,
                'start_date' => now(),
                'note' => $this->note,
                'method' => $this->method,
                'status' => 'Draft',
                'total_amount' => $this->getTotalProperty(),
                'paid_amount' => $this->paidAmount,
                'payment_method' => $this->paymentMethod,
                'payment_target' => $this->paymentTarget,
                'payment_account' => $this->paymentAccount,
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

            if ($this->image) {
                // hapuskan gambar lama jika ada
                if ($transaction->image) {
                    Storage::disk('public')->delete($transaction->image);
                }
                $path = $this->image->store('payments', 'public');
                $transaction->update(['image' => $path]);
            }

            session()->flash('success', 'Pesanan berhasil dibuat.');
        } else {
            session()->flash('error', 'Transaksi tidak ditemukan.');
        }
        return redirect()->route('transaksi.rincian-pesanan', ['id' => $this->transactionId]);
    }

    public function pay()
    {
        $this->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:15',
            'date' => 'nullable|date',
            'time' => 'nullable|date_format:H:i',
            'note' => 'nullable|string|max:500',
            'method' => 'nullable|string',
            'paymentMethod' => 'nullable|string',
            'paymentTarget' => 'nullable|string',
            'paymentAccount' => 'nullable|string',
        ]);

        $transaction = \App\Models\Transaction::find($this->transactionId);
        if ($transaction) {
            $transaction->update([
                'name' => $this->name,
                'phone' => $this->phone,
                'date' => \Carbon\Carbon::createFromFormat('d-m-Y', $this->date)->format('Y-m-d'),
                'time' => $this->time,
                'start_date' => now(),
                'note' => $this->note,
                'method' => $this->method,
                'status' => 'Belum Diproses',
                'total_amount' => $this->getTotalProperty(),
                'paid_amount' => $this->paidAmount,
                'payment_method' => $this->paymentMethod,
                'payment_target' => $this->paymentTarget,
                'payment_account' => $this->paymentAccount,
                'payment_status' => $this->paidAmount >= $this->getTotalProperty() ? 'Lunas' : 'Belum Lunas',
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

            if ($this->image) {
                // hapuskan gambar lama jika ada
                if ($transaction->image) {
                    Storage::disk('public')->delete($transaction->image);
                }
                $path = $this->image->store('payments', 'public');
                $transaction->update(['image' => $path]);
            }

            session()->flash('success', 'Pesanan berhasil dibuat.');
        } else {
            session()->flash('error', 'Transaksi tidak ditemukan.');
        }
        return redirect()->route('transaksi.rincian-pesanan', ['id' => $this->transactionId]);
    }
    public function render()
    {
        return view('livewire.transaction.buat-pesanan', [
            'products' => Product::with(['product_categories', 'product_compositions', 'reviews'])
                ->where('method', $this->method)->get(),
            'total' => $this->getTotalProperty(),
        ]);
    }
}