<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ProductSelect extends Component
{
    public $search = '';

    public $productId;

    public $isOpen = false;

    public $productName;

    protected $listeners = [
        'clearProduct' => 'clearProduct',
        'productSelectedEdit' => 'selectProduct',
    ];

    public function clearProduct()
    {
        $this->reset('productId', 'productName');
    }

    public function selectProduct($id)
    {
        $this->productId = $id;
        $this->isOpen = false;
        $this->productName = Product::find($id)->name;
        $this->dispatch('productSelected', $this->productId);
    }

    #[Layout('components.layouts.empty')]
    public function render()
    {
        return view('livewire.product-select', [
            'products' => Product::when($this->search, function ($query) {
                return $query->where('name', 'like', '%'.$this->search.'%');
            })
                ->take(10)
                ->get()
                ->toArray(),
        ]);
    }
}
