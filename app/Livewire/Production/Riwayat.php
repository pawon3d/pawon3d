<?php

namespace App\Livewire\Production;

use App\Models\Production;
use Livewire\Component;

class Riwayat extends Component
{

    public $search = '';
    public $filterStatus = '';
    public $methodName = '';
    public $sortField = 'production_number';
    public $sortDirection = 'desc';
    public $method = 'pesanan-reguler';

    public function mount($method)
    {
        if ($method == 'pesanan-reguler') {
            $this->methodName = 'Pesanan Reguler';
        } else if ($method == 'pesanan-kotak') {
            $this->methodName = 'Pesanan Kotak';
        } elseif ($method == 'siap-beli') {
            $this->methodName = 'Siap Beli';
        }
    }
    public function render()
    {
        $productions = Production::with(['details.product', 'workers.worker'])
            ->where('production_number', 'like', '%' . $this->search . '%')
            ->where('method', $this->method)
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
        return view('livewire.production.riwayat', [
            'productions' => $productions,
        ]);
    }
}
