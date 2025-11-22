<?php

namespace App\Livewire\Landing;

use App\Models\Product;
use Illuminate\Support\Facades\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Detail extends Component
{
    public $productId;
    public $product;
    public $relatedProducts;

    public function mount($id)
    {
        $this->productId = $id;
        $this->product = Product::with(['product_categories.category'])->findOrFail($id);

        View::share('title', 'Pawon3D - ' . $this->product->name);

        // Get related products (same method, different product)
        $this->relatedProducts = Product::where('id', '!=', $id)
            ->whereJsonContains('method', $this->product->method[0] ?? 'pesanan-reguler')
            ->inRandomOrder()
            ->limit(10)
            ->get();
    }

    #[Layout('components.layouts.landing.layout')]
    public function render()
    {
        return view('livewire.landing.detail');
    }
}
