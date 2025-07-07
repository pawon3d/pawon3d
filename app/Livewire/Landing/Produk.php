<?php

namespace App\Livewire\Landing;

use Illuminate\Support\Facades\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Produk extends Component
{
    public $search = '';
    public $method = 'pesanan-reguler';
    public $categorySelected = 'semua';
    protected $queryString = ['search', 'method', 'categorySelected'];

    public function mount()
    {
        View::share('title', 'Pawon3D - Produk');
    }
    public function updatingSearch()
    {
        $this->resetPage();
    }

    #[Layout('components.layouts.landing.layout')]
    public function render()
    {
        $categories = \App\Models\Category::all();
        $exploreProducts = \App\Models\Product::with('product_categories')->when($this->method, function ($query) {
            $query->whereJsonContains('method', $this->method);
        })
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->categorySelected && $this->categorySelected !== 'semua', function ($query) {
                $query->whereHas('product_categories', function ($q) {
                    $q->whereHas('category', function ($subQ) {
                        $subQ->where('name', $this->categorySelected);
                    });
                });
            })
            ->paginate(15);
        return view('livewire.landing.produk', [
            'categories' => $categories,
            'exploreProducts' => $exploreProducts
        ]);
    }
}
