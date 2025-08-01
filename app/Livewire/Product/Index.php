<?php

namespace App\Livewire\Product;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Product;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Spatie\Activitylog\Models\Activity;


class Index extends Component
{
    use WithPagination, WithFileUploads, LivewireAlert;

    public $activityLogs = [];
    public $filterStatus = '';
    public $search = '';
    public $showHistoryModal = false;
    public $viewMode = 'grid';
    public $method = 'pesanan-reguler';
    public $sortField = 'name';
    public $sortDirection = 'desc';

    protected $queryString = ['viewMode', 'method', 'search', 'sortField', 'sortDirection'];

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function riwayatPembaruan()
    {
        $this->activityLogs = Activity::inLog('products')
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function cetakInformasi()
    {
        return redirect()->route('produk.pdf', [
            'search' => $this->search,
        ]);
    }

    public function mount()
    {
        View::share('title', 'Produk');
        View::share('mainTitle', 'Inventori');


        if (session()->has('success')) {
            $this->alert('success', session('success'));
        }

        $this->viewMode = session('viewMode', 'grid');
        $this->method = session('method', 'pesanan-reguler');
    }

    public function updatedViewMode($value)
    {
        session()->put('viewMode', $value);
    }

    public function updatedMethod($value)
    {
        session()->put('method', $value);
    }

    public function render()
    {
        $products = Product::with(['product_categories', 'product_compositions', 'reviews'])
            ->when($this->method, function ($query) {
                $query->whereJsonContains('method', $this->method);
            })
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->withAvg('reviews', 'rating')->withCount('reviews')->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);


        return view('livewire.product.index', [
            'products' => $products,
        ]);
    }
}
