<?php

namespace App\Livewire\Hitung;

use App\Models\Hitung;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class Rencana extends Component
{
    use LivewireAlert, WithPagination;

    public string $search = '';

    public string $sortField = 'hitung_date';

    public string $sortDirection = 'desc';

    protected $queryString = ['search', 'sortField', 'sortDirection'];

    public function mount(): void
    {
        View::share('title', 'Rencana Hitung dan Catat Persediaan');
        View::share('mainTitle', 'Inventori');
        if (session()->has('success')) {
            $this->alert('success', session('success'));
        }
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.hitung.rencana', [
            'hitungs' => Hitung::with(['details.material', 'user'])
                ->when($this->search, function ($query) {
                    $query->where('hitung_number', 'like', '%'.$this->search.'%')
                        ->orWhere('action', 'like', '%'.$this->search.'%');
                })->whereIn('status', ['Draft', 'Belum Diproses'])
                ->where('is_start', false)
                ->where('is_finish', false)
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate(10),
        ]);
    }
}
