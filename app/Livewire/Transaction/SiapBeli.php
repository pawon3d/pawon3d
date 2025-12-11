<?php

namespace App\Livewire\Transaction;

use App\Models\Production;
use App\Models\Transaction;
use Illuminate\Support\Facades\View;
use Livewire\Component;
use Livewire\WithPagination;

class SiapBeli extends Component
{
    use WithPagination;

    public $search = '';

    protected $queryString = ['search'];

    public function mount()
    {
        View::share('title', 'Siap Beli - Daftar Produk');
        View::share('mainTitle', 'Kasir');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Get production dates with siap-beli method, grouped by DATE ONLY (not time)
        $productions = Production::query()
            ->where('method', 'siap-beli')
            ->whereNotNull('date')
            ->where('date', '!=', '')
            ->with('details.product')
            ->when($this->search, function ($query) {
                $query->whereRaw('DATE(date) like ?', ['%' . $this->search . '%']);
            })
            ->orderBy('date', 'desc')
            ->get()
            ->groupBy(fn($p) => \Carbon\Carbon::parse($p->date)->format('Y-m-d'))
            ->map(function ($productions, $dateStr) {
                return [
                    'date' => $dateStr,
                    'jenis_produk' => $productions->flatMap(fn($p) => $p->details)->unique('product_id')->count(),
                    'total_produksi' => $productions->flatMap(fn($p) => $p->details)->sum('quantity_get'),
                    'productions' => $productions,
                ];
            })
            ->values();

        // Calculate total terjual for each date
        $data = $productions->map(function ($item) {
            $totalTerjual = Transaction::whereHas('details', function ($query) use ($item) {
                $query->whereIn('product_id', $item['productions']->flatMap(fn($p) => $p->details->pluck('product_id')));
            })
                ->where('method', 'siap-beli')
                ->whereRaw('DATE(COALESCE(date, created_at)) = ?', [$item['date']])
                ->get()
                ->flatMap(fn($t) => $t->details)
                ->sum('quantity');

            return [
                'date' => $item['date'],
                'jenis_produk' => $item['jenis_produk'],
                'total_produksi' => $item['total_produksi'],
                'total_terjual' => $totalTerjual,
                'total_tersisa' => $item['total_produksi'] - $totalTerjual,
            ];
        });

        // Paginate the collection manually
        $perPage = 5;
        $page = $this->getPage();
        $total = $data->count();
        $paginatedData = $data->slice(($page - 1) * $perPage, $perPage)->values();

        $products = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedData,
            $total,
            $perPage,
            $page,
            [
                'path' => url('/transaksi/siap-beli'),
                'query' => request()->query(),
            ]
        );

        return view('livewire.transaction.siap-beli', [
            'products' => $products,
        ]);
    }
}
