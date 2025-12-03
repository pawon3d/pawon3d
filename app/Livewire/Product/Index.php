<?php

namespace App\Livewire\Product;

use App\Models\Product;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

class Index extends Component
{
    use LivewireAlert, WithFileUploads, WithPagination;

    public $activityLogs = [];

    public $filterStatus = '';

    public $search = '';

    public $showHistoryModal = false;

    public $viewMode = 'grid';

    public $method = 'pesanan-reguler';

    public $sortField = 'name';

    public $sortDirection = 'desc';

    public $perPage = 12;

    public $statusSummary = [
        'total' => 0,
        'active' => 0,
        'inactive' => 0,
        'recommended' => 0,
    ];

    protected $methodSummary = null;

    protected $queryString = [
        'viewMode' => ['except' => 'grid'],
        'method' => ['except' => 'pesanan-reguler'],
        'search' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'desc'],
        'filterStatus' => ['except' => ''],
    ];

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
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
        $this->resetPage();
    }

    public function render()
    {
        $baseQuery = $this->baseProductQuery();

        $this->statusSummary = $this->buildStatusSummary(clone $baseQuery);

        $productsQuery = $this->applyStatusFilter(clone $baseQuery);

        $products = $productsQuery
            ->with(['product_categories', 'product_compositions'])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.product.index', [
            'products' => $products,
            'statusSummary' => $this->statusSummary,
            'methodSummary' => $this->methodSummary ?? $this->buildMethodSummary(),
        ]);
    }

    protected function baseProductQuery()
    {
        return Product::query()
            ->when($this->method, function ($query) {
                $query->whereJsonContains('method', $this->method);
            })
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . trim($this->search) . '%');
            });
    }

    protected function applyStatusFilter($query)
    {
        if (! $this->filterStatus) {
            return $query;
        }

        return $query->when($this->filterStatus === 'active', fn($q) => $q->where('is_active', true))
            ->when($this->filterStatus === 'inactive', fn($q) => $q->where('is_active', false))
            ->when($this->filterStatus === 'recommended', fn($q) => $q->where('is_recommended', true));
    }

    protected function buildStatusSummary($query)
    {
        return [
            'total' => (clone $query)->count(),
            'active' => (clone $query)->where('is_active', true)->count(),
            'inactive' => (clone $query)->where('is_active', false)->count(),
            'recommended' => (clone $query)->where('is_recommended', true)->count(),
        ];
    }

    protected function buildMethodSummary()
    {
        if ($this->methodSummary !== null) {
            return $this->methodSummary;
        }

        $summary = [
            'pesanan-reguler' => 0,
            'pesanan-kotak' => 0,
            'siap-beli' => 0,
        ];

        Product::select('method')
            ->whereNotNull('method')
            ->orderBy('created_at')
            ->chunk(200, function ($products) use (&$summary) {
                foreach ($products as $product) {
                    foreach ((array) $product->method as $method) {
                        if (array_key_exists($method, $summary)) {
                            $summary[$method]++;
                        }
                    }
                }
            });

        return $this->methodSummary = $summary;
    }
}
