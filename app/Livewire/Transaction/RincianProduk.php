<?php

namespace App\Livewire\Transaction;

use App\Models\Product;
use Illuminate\Support\Facades\View;
use Livewire\Component;

class RincianProduk extends Component
{
    public $productId;

    public $product;

    public $relatedProducts;

    public function mount($id)
    {
        $this->productId = $id;
        $this->product = Product::find($id);
        if (! $this->product) {
            abort(404, 'Product not found');
        }
        View::share('title', 'Rincian Produk: ' . $this->product->name);
        View::share('mainTitle', 'Kasir');

        $this->loadRelatedProducts();
    }

    public function loadRelatedProducts(): void
    {
        $productMethods = $this->product->method ?? [];

        if (empty($productMethods)) {
            $this->relatedProducts = collect();

            return;
        }

        $this->relatedProducts = Product::where('id', '!=', $this->product->id)
            ->where('is_active', true)
            ->where(function ($query) use ($productMethods) {
                foreach ($productMethods as $method) {
                    $query->orWhereJsonContains('method', $method);
                }
            })
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.transaction.rincian-produk');
    }
}
