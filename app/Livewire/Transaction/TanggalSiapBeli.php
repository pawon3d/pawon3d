<?php

namespace App\Livewire\Transaction;

use App\Models\Production;
use App\Models\Transaction;
use Illuminate\Support\Facades\View;
use Livewire\Component;
use Livewire\WithPagination;

class TanggalSiapBeli extends Component
{
    use WithPagination;

    public $date;

    public $search = '';

    public $sortField = 'product_name';

    public $sortDirection = 'asc';

    protected $queryString = ['search'];

    public function mount($date)
    {
        $this->date = $date;
        $formattedDate = \Carbon\Carbon::parse($date)->translatedFormat('d F Y');
        View::share('title', 'Siap Beli - ' . $formattedDate);
        View::share('mainTitle', 'Kasir');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        // Get all productions for this date
        $productions = Production::query()
            ->where('method', 'siap-beli')
            ->whereRaw('DATE(date) = ?', [$this->date])
            ->with(['details.product'])
            ->get();

        // Get unique products from all productions on this date
        $productData = collect();

        foreach ($productions as $production) {
            foreach ($production->details as $detail) {
                $product = $detail->product;

                // Calculate total production for this product on this date
                $totalProduction = $productions->flatMap(fn($p) => $p->details)
                    ->where('product_id', $product->id)
                    ->sum('quantity_get');

                // Calculate total sold for this product on this date
                $totalSold = Transaction::whereHas('details', function ($query) use ($product) {
                    $query->where('product_id', $product->id);
                })
                    ->where('method', 'siap-beli')
                    ->whereRaw('DATE(COALESCE(date, created_at)) = ?', [$this->date])
                    ->get()
                    ->flatMap(fn($t) => $t->details)
                    ->where('product_id', $product->id)
                    ->sum('quantity');

                // Only add if not already in collection
                if (! $productData->contains('product_id', $product->id)) {
                    $productData->push([
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'production_id' => $production->production_number,
                        'total_production' => $totalProduction,
                        'total_sold' => $totalSold,
                        'total_remaining' => $totalProduction - $totalSold,
                    ]);
                }
            }
        }

        // Apply search filter
        if ($this->search) {
            $productData = $productData->filter(function ($item) {
                return stripos($item['product_name'], $this->search) !== false;
            });
        }

        // Apply sorting
        $productData = $productData->sortBy([
            [$this->sortField, $this->sortDirection],
        ])->values();

        // Paginate
        $perPage = 10;
        $page = $this->getPage();
        $total = $productData->count();
        $paginatedData = $productData->slice(($page - 1) * $perPage, $perPage)->values();

        $products = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedData,
            $total,
            $perPage,
            $page,
            [
                'path' => url('/transaksi/siap-beli/' . $this->date),
                'query' => request()->query(),
            ]
        );

        return view('livewire.transaction.tanggal-siap-beli', [
            'products' => $products,
        ]);
    }
}
