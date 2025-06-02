<?php

namespace App\Livewire\Padan;

use Illuminate\Support\Facades\View;
use Livewire\Component;

class Riwayat extends Component
{
    public $search = '';
    public $filterStatus = '';
    public function mount()
    {
        View::share('title', 'Riwayat Hitung dan Padan Persediaan');
    }

    public function cetakInformasi()
    {
        return redirect()->route('padan.pdf', [
            'search' => $this->search,
            'status' => 'history',
        ]);
    }
    public function render()
    {
        return view('livewire.padan.riwayat', [
            'padans' => \App\Models\Padan::with(['details'])
                ->when($this->search, function ($query) {
                    $query->where('padan_number', 'like', '%' . $this->search . '%');
                })->where('is_finish', true)
                ->latest()
                ->paginate(10)
        ]);
    }
}
