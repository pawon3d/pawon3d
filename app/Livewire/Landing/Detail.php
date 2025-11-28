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

        View::share('title', 'Pawon3D - '.$this->product->name);

        // Get related products (all other products, not filtered by method)
        $this->relatedProducts = Product::where('id', '!=', $id)
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
