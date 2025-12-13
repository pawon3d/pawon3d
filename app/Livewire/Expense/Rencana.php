<?php

namespace App\Livewire\Expense;

use App\Models\Expense;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class Rencana extends Component
{
    use WithPagination, LivewireAlert;

    public $search = '';

    public $sortField = 'expense_date';

    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'expense_date'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function mount()
    {
        View::share('title', 'Rencana Belanja');
        View::share('mainTitle', 'Inventori');
        if (session()->has('success')) {
            $this->alert('success', session('success'));
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortByColumn($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function render()
    {
        $plannedExpenses = Expense::select('id', 'expense_number', 'expense_date', 'supplier_id', 'grand_total_expect', 'note')
            ->with('supplier:id,name')
            ->where('status', 'Draft')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('expense_number', 'like', '%' . $this->search . '%')
                        ->orWhereHas('supplier', function ($q) {
                            $q->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.expense.rencana', [
            'plannedExpenses' => $plannedExpenses,
        ]);
    }
}
