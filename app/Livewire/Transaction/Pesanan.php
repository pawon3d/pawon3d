<?php

namespace App\Livewire\Transaction;

use Illuminate\Support\Facades\View;
use Livewire\Component;

class Pesanan extends Component
{
    public $search = '';
    public $filterStatus = '';
    public $methodName = '';
    public $sortField = 'transaction_number';
    public $sortDirection = 'desc';
    public $method = 'pesanan-reguler';

    public function mount($method)
    {
        View::share('title', 'Daftar Pesanan');
        if ($method == 'pesanan-reguler') {
            $this->methodName = 'Pesanan Reguler';
            $this->method = 'pesanan-reguler';
        } else if ($method == 'pesanan-kotak') {
            $this->methodName = 'Pesanan Kotak';
            $this->method = 'pesanan-kotak';
        } elseif ($method == 'siap-beli') {
            $this->methodName = 'Siap Beli';
            $this->method = 'siap-beli';
        }
    }
    public function render()
    {
        return view('livewire.transaction.pesanan', [
            'transactions' => \App\Models\Transaction::with(['details.product', 'user'])
                ->where('invoice_number', 'like', '%' . $this->search . '%')
                ->where('method', $this->method)
                ->whereNotIn('status', ['Gagal', 'Selesai'])
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate(10),
        ]);
    }
}