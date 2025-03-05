<?php

namespace App\Livewire\Transaction;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class Pos extends Component
{
    use WithPagination, LivewireAlert;

    public $activeTab = 'ready';
    public $cart = [];
    public $searchQuery = '';
    public $activeCategory = null;
    public $paymentMethod = '';
    public $paymentStatus = '';
    public $schedule;
    public $dp = 0;
    public $type = 'siap beli';

    protected $listeners = ['refreshProducts' => '$refresh'];

    public function mount()
    {
        View::share('title', 'Point of Sale');
        $this->schedule = now()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.transaction.pos', [
            'products' => $this->filteredProducts,
            'categories' => Category::all(),
            'totalAmount' => $this->totalAmount,
        ]);
    }

    public function getFilteredProductsProperty()
    {
        return Product::query()
            ->when($this->activeCategory, function ($query) {
                $query->where('category_id', $this->activeCategory);
            })
            ->when($this->searchQuery, function ($query) {
                $query->where('name', 'like', '%' . $this->searchQuery . '%');
            })
            ->when($this->activeTab === 'ready', function ($query) {
                $query->where('is_ready', true);
            })
            ->get();
    }

    public function getTotalAmountProperty()
    {
        return array_reduce($this->cart, function ($sum, $item) {
            return $sum + ($item['price'] * $item['quantity']);
        }, 0);
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);

        if ($this->activeTab === 'ready' && $product->stock < 1) return;

        $existingItem = collect($this->cart)->firstWhere('product_id', $productId);

        if ($existingItem) {
            $this->cart = collect($this->cart)->map(function ($item) use ($productId) {
                if ($item['product_id'] === $productId) {
                    $item['quantity']++;
                    if ($this->activeTab === 'ready') {
                        $item['stock']--;
                    }
                }
                return $item;
            })->toArray();
        } else {
            $this->cart[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'stock' => $this->activeTab === 'ready' ? $product->stock - 1 : $product->stock
            ];
        }
    }

    public function removeFromCart($productId)
    {
        $this->cart = collect($this->cart)->map(function ($item) use ($productId) {
            if ($item['product_id'] === $productId) {
                $item['quantity']--;
                if ($this->activeTab === 'ready') {
                    $item['stock']++;
                }
            }
            return $item;
        })->filter(function ($item) {
            return $item['quantity'] > 0;
        })->values()->toArray();
    }

    public function clearCart()
    {
        $this->cart = [];
    }

    public function processPayment()
    {
        $this->validate([
            'paymentMethod' => 'required|in:tunai,non tunai',
            'cart' => 'required|array|min:1',
            'schedule' => $this->activeTab === 'order' ? 'required|date' : '',
            'paymentStatus' => $this->activeTab === 'order' ? 'required' : '',
        ]);

        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'total_amount' => $this->totalAmount,
            'payment_method' => $this->paymentMethod,
            'payment_status' => $this->paymentStatus,
            'dp' => $this->dp,
            'type' => $this->type,
            'schedule' => $this->schedule,
            'status' => $this->activeTab === 'ready' ? 'selesai' : 'pending',
        ]);

        foreach ($this->cart as $item) {
            TransactionDetail::create([
                'transaction_id' => $transaction->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);

            if ($this->activeTab === 'ready') {
                Product::find($item['product_id'])->decrement('stock', $item['quantity']);
            }
        }

        $this->clearCart();
        $this->alert('success', 'Transaksi berhasil disimpan!');
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->type = $tab === 'ready' ? 'siap beli' : 'pesanan';
        $this->clearCart();
        $this->reset(['paymentMethod', 'paymentStatus', 'dp', 'schedule']);
    }
}
