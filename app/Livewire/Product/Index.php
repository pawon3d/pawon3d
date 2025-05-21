<?php

namespace App\Livewire\Product;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\Category;
use App\Models\Material;
use App\Models\ProcessedMaterial;
use Illuminate\Support\Facades\Storage;
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

    protected $queryString = ['viewMode', 'method'];

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
        return view('livewire.product.index', [
            'products' => Product::with(['product_categories', 'product_compositions', 'reviews'])
                ->where('method', $this->method)
                ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                ->paginate(10),
            'categories' => Category::all(),
            'materials' => Material::all(),
            'processedMaterials' => ProcessedMaterial::all()
        ]);
    }
}
