<?php

namespace App\Livewire\Production;

use App\Models\Production;
use Illuminate\Support\Facades\View;
use Livewire\Component;

class Riwayat extends Component
{
    public $search = '';

    public $filterStatus = '';

    public $methodName = '';

    public $sortField = 'production_number';

    public $sortDirection = 'desc';

    public $method = 'pesanan-reguler';

    protected $queryString = ['search', 'sortField', 'sortDirection'];

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function mount($method)
    {
        $this->method = $method;
        if ($method == 'pesanan-reguler') {
            $this->methodName = 'Pesanan Reguler';
        } elseif ($method == 'pesanan-kotak') {
            $this->methodName = 'Pesanan Kotak';
        } elseif ($method == 'siap-beli') {
            $this->methodName = 'Siap Beli';
        }
        View::share('title', 'Riwayat Produksi ' . $this->methodName);
        View::share('mainTitle', 'Produksi');
    }

    public function render()
    {
        $query = Production::with(['details.product', 'workers'])
            ->where('productions.production_number', 'like', '%' . $this->search . '%')
            ->where('productions.is_finish', true)
            ->where('productions.method', $this->method);

        // Validasi sortField untuk mencegah query error
        if (empty($this->sortField)) {
            $this->sortField = 'production_number';
        }

        if ($this->sortField === 'product_name') {
            $query->join('production_details', 'productions.id', '=', 'production_details.production_id')
                ->join('products', 'production_details.product_id', '=', 'products.id')
                ->orderBy('products.name', $this->sortDirection);
        } elseif ($this->sortField === 'worker_name') {
            $query->join('production_workers', 'productions.id', '=', 'production_workers.production_id')
                ->join('users', 'production_workers.user_id', '=', 'users.id')
                ->orderBy('users.name', $this->sortDirection);
        } elseif ($this->sortField === 'end_date') {
            // Sort by end_date jika ada, fallback ke updated_at
            $query->orderBy('productions.end_date', $this->sortDirection);
        } else {
            $query->orderBy("productions.{$this->sortField}", $this->sortDirection);
        }

        $productions = $query->select('productions.*')->distinct()->paginate(10);

        return view('livewire.production.riwayat', [
            'productions' => $productions,
        ]);
    }
}
