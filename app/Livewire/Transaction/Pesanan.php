<?php

namespace App\Livewire\Transaction;

use Illuminate\Support\Facades\View;
use Livewire\Component;

class Pesanan extends Component
{
    public $search = '';

    public $filterStatus = '';

    public $methodName = '';

    public $sortField = 'invoice_number';

    public $sortDirection = 'desc';

    public $method = 'pesanan-reguler';

    protected $queryString = ['search', 'sortField', 'sortDirection'];

    public function sortBy($field)
    {
        if ($this->method == 'siap-beli' && $field == 'date') {
            $field = 'start_date';
        } elseif ($this->method != 'siap-beli' && $field == 'date') {
            $field = 'date';
        }
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function mount($method)
    {
        View::share('title', 'Daftar Pesanan');
        View::share('mainTitle', 'Kasir');
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
        $query = \App\Models\Transaction::with(['details.product', 'user'])
            ->where('transactions.invoice_number', 'like', '%'.$this->search.'%')
            ->where('transactions.method', $this->method)
            ->whereNotIn('transactions.status', ['Gagal', 'Selesai', 'temp']);

        if ($this->sortField === 'product_name') {
            $query->join('transaction_details', 'transactions.id', '=', 'transaction_details.transaction_id')
                ->join('products', 'transaction_details.product_id', '=', 'products.id')
                ->orderBy('products.name', $this->sortDirection);
        } elseif ($this->sortField === 'user_name') {
            $query->join('users', 'transactions.user_id', '=', 'users.id')
                ->orderBy('users.name', $this->sortDirection);
        } else {
            $query->orderBy("transactions.{$this->sortField}", $this->sortDirection);
        }

        $transactions = $query->select('transactions.*')->distinct()->paginate(10);

        return view('livewire.transaction.pesanan', [
            'transactions' => $transactions,
        ]);
    }
}
