<?php

namespace App\Livewire\Hitung;

use App\Models\Hitung;
use Illuminate\Support\Facades\View;
use Livewire\Component;
use Livewire\WithPagination;

class Rencana extends Component
{
    use WithPagination;

    public $search = '';

    public $sortField = 'hitung_date';

    public $sortDirection = 'desc';

    protected $queryString = ['search', 'sortField', 'sortDirection'];

    public function mount()
    {
        View::share('title', 'Rencana Hitung dan Catat Persediaan');
        View::share('mainTitle', 'Inventori');
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
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
