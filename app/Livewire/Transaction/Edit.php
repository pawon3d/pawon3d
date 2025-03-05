<?php

namespace App\Livewire\Transaction;

use Livewire\Component;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\Category;
use App\Models\TransactionDetail;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class Edit extends Component
{
    use LivewireAlert;

    public $transactionId;
    public $transaction;
    public $cart = [];
    public $searchQuery = '';
    public $activeCategory = null;
    public $paymentMethod;
    public $paymentStatus;
    public $activeTab; // menggantikan properti $type
    public $dp;
    public $schedule;
    public $originalStock = [];

    // Aturan validasi dinamis:
    protected function rules()
    {
        $rules = [
            'paymentMethod' => 'required|in:tunai,non tunai',
            'dp' => 'nullable|numeric|min:0',
            'schedule' => 'nullable|date',
            'cart' => 'required|array|min:1',
        ];
        // Jika transaksi termasuk order, maka paymentStatus wajib diisi
        if ($this->activeTab === 'order') {
            $rules['paymentStatus'] = 'required|in:lunas,belum lunas';
        }
        return $rules;
    }

    public function mount($id)
    {
        View::share('title', 'Edit Transaksi');

        $this->transaction = Transaction::with(['details.product'])
            ->findOrFail($id);

        $this->transactionId   = $id;
        $this->paymentMethod   = $this->transaction->payment_method;
        $this->paymentStatus   = $this->transaction->payment_status;
        // Konversi tipe transaksi menjadi activeTab
        $this->activeTab       = $this->transaction->type === 'siap beli' ? 'ready' : 'order';
        $this->dp              = $this->transaction->dp;
        $this->schedule        = $this->transaction->schedule;

        // Inisialisasi cart dan simpan stok asli untuk transaksi "ready"
        foreach ($this->transaction->details as $detail) {
            $product = $detail->product;
            $this->cart[] = [
                'product_id' => $product->id,
                'name'       => $product->name,
                'price'      => $detail->price,
                'quantity'   => $detail->quantity,
                'stock'      => $product->stock,
            ];

            if ($this->transaction->type === 'siap beli') {
                // Simpan stok asli: tambahkan kembali kuantitas yang sudah dipesan
                $this->originalStock[$product->id] = $product->stock + $detail->quantity;
            }
        }
    }

    public function render()
    {
        return view('livewire.transaction.edit', [
            'products'    => $this->getFilteredProducts(),
            'categories'  => Category::all(),
            'totalAmount' => $this->totalAmount,
        ]);
    }

    public function getFilteredProducts()
    {
        return Product::query()
            ->when($this->activeCategory, fn($q) => $q->where('category_id', $this->activeCategory))
            ->when($this->searchQuery, fn($q) => $q->where('name', 'like', '%' . $this->searchQuery . '%'))
            ->when($this->activeTab === 'ready', fn($q) => $q->where('is_ready', true))
            ->get();
    }

    public function getTotalAmountProperty()
    {
        return collect($this->cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    }

    public function addToCart($productId)
    {
        $product = Product::findOrFail($productId);

        if ($this->activeTab === 'ready' && $product->stock < 1) {
            $this->alert('error', 'Stok produk habis!');
            return;
        }

        $existing = collect($this->cart)->firstWhere('product_id', $productId);

        if ($existing) {
            $this->cart = collect($this->cart)->map(function ($item) use ($productId) {
                if ($item['product_id'] === $productId) {
                    $item['quantity']++;
                    if ($this->activeTab === 'ready') {
                        $product = Product::find($productId);
                        $product->decrement('stock');
                        $item['stock'] = $product->stock;
                    }
                }
                return $item;
            })->toArray();
        } else {
            $this->cart[] = [
                'product_id' => $product->id,
                'name'       => $product->name,
                'price'      => $product->price,
                'quantity'   => 1,
                'stock'      => $this->activeTab === 'ready' ? $product->stock - 1 : $product->stock
            ];

            if ($this->activeTab === 'ready') {
                $product->decrement('stock');
            }
        }
    }

    public function removeFromCart($productId)
    {
        $this->cart = collect($this->cart)->map(function ($item) use ($productId) {
            if ($item['product_id'] === $productId) {
                $item['quantity']--;
                if ($this->activeTab === 'ready') {
                    $product = Product::find($productId);
                    $product->increment('stock');
                    $item['stock'] = $product->stock;
                }
            }
            return $item;
        })->filter(function ($item) {
            return $item['quantity'] > 0;
        })->values()->toArray();
    }

    public function processPayment()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                $transaction = Transaction::find($this->transactionId);

                // Update transaksi dengan konversi activeTab ke tipe database
                $transaction->update([
                    'payment_method' => $this->paymentMethod,
                    'payment_status' => $this->activeTab === 'order' ? $this->paymentStatus : null,
                    'dp'             => $this->dp,
                    'schedule'       => $this->schedule,
                    'total_amount'   => $this->totalAmount,
                    'type'           => $this->activeTab === 'ready' ? 'siap beli' : 'pesanan',
                ]);

                // Hapus detail lama dan simpan detail baru
                $transaction->details()->delete();
                foreach ($this->cart as $item) {
                    TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'product_id'     => $item['product_id'],
                        'quantity'       => $item['quantity'],
                        'price'          => $item['price'],
                    ]);
                }

                // Jika transaksi tipe ready (siap beli), perbarui stok produk
                if ($this->activeTab === 'ready') {
                    foreach ($this->cart as $item) {
                        $product = Product::find($item['product_id']);
                        // Jika stok asli belum disimpan (misalnya karena perubahan tipe), hitung ulang dengan asumsi stok saat ini ditambah kuantitas yang dipesan
                        $original = $this->originalStock[$product->id] ?? ($product->stock + $item['quantity']);
                        $newStock = $original - $item['quantity'];
                        $product->update(['stock' => $newStock]);
                    }
                }
            });

            $this->alert('success', 'Transaksi berhasil diperbarui!');
            return redirect()->route('transaksi');
        } catch (\Exception $e) {
            $this->alert('error', 'Gagal memperbarui transaksi: ' . $e->getMessage());
        }
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        // Pada proses edit, jangan reset cart agar data transaksi tidak hilang.
        $this->reset(['searchQuery', 'activeCategory']);
    }
}
