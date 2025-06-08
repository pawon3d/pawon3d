<?php

namespace App\Livewire\Hitung;

use Illuminate\Support\Facades\View;
use Livewire\Component;

class Riwayat extends Component
{
    public $search = '';
    public $filterStatus = '';
    public function mount()
    {
        View::share('title', 'Riwayat Hitung dan Catat Persediaan');
    }

    public function cetakInformasi()
    {
        return redirect()->route('hitung.pdf', [
            'search' => $this->search,
            'status' => 'history',
        ]);
    }
    public function render()
    {
        return view('livewire.hitung.riwayat', [
            'hitungs' => \App\Models\Hitung::with(['details'])
                ->when($this->search, function ($query) {
                    $query->where('hitung_number', 'like', '%' . $this->search . '%');
                })->where('is_finish', true)
                ->latest()
                ->paginate(10)
        ]);
    }
}
