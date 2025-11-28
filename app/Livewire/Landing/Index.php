<?php

namespace App\Livewire\Landing;

use Illuminate\Support\Facades\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $method = 'pesanan-reguler';

    public $categorySelected = 'semua';

    public $caraPesan = 'whatsapp';

    protected $queryString = ['search', 'method', 'categorySelected'];

    public function mount()
    {
        View::share('title', 'Pawon3D');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    #[Layout('components.layouts.landing.layout')]
    public function render()
    {
        $categories = \App\Models\Category::all();
        $products = \App\Models\Product::with('category')
            ->where('is_recommended', true)
            ->get()
            ->sortByDesc(function ($product) {
                return $product->transactions->count();
            })
            ->take(4);
        $exploreProducts = \App\Models\Product::with('product_categories')->when($this->method, function ($query) {
            $query->whereJsonContains('method', $this->method);
        })
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%');
            })
            ->when($this->categorySelected && $this->categorySelected !== 'semua', function ($query) {
                $query->whereHas('product_categories', function ($q) {
                    $q->whereHas('category', function ($subQ) {
                        $subQ->where('name', $this->categorySelected);
                    });
                });
            })
            ->limit(10)->get();

        return view('livewire.landing.index', [
            'categories' => $categories,
            'products' => $products,
            'exploreProducts' => $exploreProducts,
        ]);
    }
}
