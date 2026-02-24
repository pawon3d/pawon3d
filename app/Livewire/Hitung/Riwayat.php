<?php

namespace App\Livewire\Hitung;

use Illuminate\Support\Facades\View;
use Livewire\Component;

class Riwayat extends Component
{
    public string $search = '';

    public string $filterStatus = '';

    public string $sortField = 'hitung_number';

    public string $sortDirection = 'desc';

    protected $queryString = ['search', 'sortField', 'sortDirection'];

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function mount(): void
    {
        View::share('title', 'Riwayat Hitung dan Catat Persediaan');
        View::share('mainTitle', 'Inventori');
    }

    public function cetakInformasi(): mixed
    {
        return redirect()->route('hitung.pdf', [
            'search' => $this->search,
            'status' => 'history',
        ]);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.hitung.riwayat', [
            'hitungs' => \App\Models\Hitung::with(['details.material', 'user'])
                ->when($this->search, function ($query) {
                    $query->where('hitung_number', 'like', '%'.$this->search.'%');
                })->where('is_finish', true)
                ->orderBy("hitungs.{$this->sortField}", $this->sortDirection)
                ->paginate(10),
        ]);
    }
}
