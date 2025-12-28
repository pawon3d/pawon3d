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

    public function mount(Product $product)
    {
        $this->product = $product->load(['product_categories.category']);
        $this->productId = $this->product->id;

        View::share('title', 'Pawon3D - '.$this->product->name);
        View::share('metaDescription', \Illuminate\Support\Str::limit($this->product->description ?? 'Nikmati kelezatan ' . $this->product->name . ' dari Pawon3D. Dibuat dengan bahan berkualitas dan rasa yang otentik.', 160));

        // Get related products (all other products, not filtered by method)
        $this->relatedProducts = Product::where('id', '!=', $this->productId)
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
