<?php

namespace App\Livewire\Landing;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Produk extends Component
{
    public $search = '';

    public $method = 'pesanan-reguler';

    public $categorySelected = 'semua';

    public $perPage = 5;

    protected $queryString = ['search', 'method', 'categorySelected'];

    public function mount()
    {
        View::share('title', 'Pawon3D - Produk');
    }

    public function updatingSearch()
    {
        $this->perPage = 5;
    }

    public function updatingMethod()
    {
        $this->perPage = 5;
    }

    public function updatingCategorySelected()
    {
        $this->perPage = 5;
    }

    public function loadMore()
    {
        $this->perPage += 5;
    }

    #[Layout('components.layouts.landing.layout')]
    public function render()
    {
        $categories = Category::all();
        $query = Product::with('product_categories')
            ->when($this->method, function ($query) {
                $query->whereJsonContains('method', $this->method);
            })
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%');
            });

        $totalProducts = $query->count();
        $exploreProducts = $query->take($this->perPage)->get();
        $hasMore = $totalProducts > $this->perPage;

        return view('livewire.landing.produk', [
            'categories' => $categories,
            'exploreProducts' => $exploreProducts,
            'hasMore' => $hasMore,
            'totalProducts' => $totalProducts,
        ]);
    }
}
