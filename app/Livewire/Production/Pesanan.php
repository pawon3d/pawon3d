<?php

namespace App\Livewire\Production;

use Illuminate\Support\Facades\View;
use Livewire\Component;

class Pesanan extends Component
{
    public $search = '';

    public $methodName = '';

    public $sortField = 'invoice_number';

    public $sortDirection = 'desc';

    public $method = 'pesanan-reguler';

    protected $queryString = ['search', 'sortField', 'sortDirection'];

    public function sortBy($field)
    {
        // Handle date field based on method
        if ($this->method == 'siap-beli' && $field == 'date') {
            $field = 'start_date';
        } elseif ($this->method != 'siap-beli' && $field == 'date') {
            $field = 'date';
        }

        // Toggle sort direction if clicking same field
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    public function cetakInformasi() {}

    public function mount($method)
    {
        View::share('title', 'Daftar Pesanan');
        View::share('mainTitle', 'Produksi');
        if ($method == 'pesanan-reguler') {
            $this->methodName = 'Pesanan Reguler';
            $this->method = 'pesanan-reguler';
        } elseif ($method == 'pesanan-kotak') {
            $this->methodName = 'Pesanan Kotak';
            $this->method = 'pesanan-kotak';
        } elseif ($method == 'siap-beli') {
            $this->methodName = 'Siap Beli';
            $this->method = 'siap-beli';
        }
    }

    public function render()
    {
        $query = \App\Models\Transaction::query()
            ->where('invoice_number', 'like', '%'.$this->search.'%')
            ->where('method', $this->method)
            ->whereIn('status', ['temp', 'Draft', 'Belum Diproses']);

        // Apply sorting based on field
        if ($this->sortField === 'product_name') {
            $query->leftJoin('transaction_details', 'transactions.id', '=', 'transaction_details.transaction_id')
                ->leftJoin('products', 'transaction_details.product_id', '=', 'products.id')
                ->select('transactions.*')
                ->orderBy('products.name', $this->sortDirection);
        } elseif ($this->sortField === 'user_name') {
            $query->leftJoin('users', 'transactions.user_id', '=', 'users.id')
                ->select('transactions.*')
                ->orderBy('users.name', $this->sortDirection);
        } elseif ($this->sortField === 'customer_name') {
            $query->orderBy('name', $this->sortDirection);
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        $transactions = $query->with(['details.product', 'user'])->distinct()->paginate(10);

        return view('livewire.production.pesanan', [
            'transactions' => $transactions,
        ]);
    }
}
