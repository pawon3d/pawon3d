<?php

namespace App\Livewire\Transaction;

use Illuminate\Support\Facades\View;
use Livewire\Component;

class RincianProduk extends Component
{
    public $productId;
    public $product;

    public function mount($id)
    {
        $this->productId = $id;
        $this->product = \App\Models\Product::find($id);
        if (!$this->product) {
            abort(404, 'Product not found');
        }
        View::share('title', 'Rincian Produk: ' . $this->product->name);
    }
    public function render()
    {
        return view('livewire.transaction.rincian-produk');
    }
}
