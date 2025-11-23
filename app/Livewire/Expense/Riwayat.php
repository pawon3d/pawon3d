<?php

namespace App\Livewire\Expense;

use Illuminate\Support\Facades\View;
use Livewire\Component;
use Livewire\WithPagination;

class Riwayat extends Component
{
    use WithPagination;

    public $search = '';

    public $filterStatus = '';

    public $sortField = 'expense_number';

    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'expense_number'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortByColumn($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
        $this->resetPage();
    }

    public function mount()
    {
        View::share('title', 'Riwayat Belanja Persediaan');
        View::share('mainTitle', 'Inventori');
    }

    public function cetakInformasi()
    {
        return redirect()->route('belanja.pdf', [
            'search' => $this->search,
            'status' => 'history',
        ]);
    }

    public function render()
    {
        $query = \App\Models\Expense::select('expenses.id', 'expenses.expense_number', 'expenses.expense_date', 'expenses.supplier_id', 'expenses.grand_total_expect', 'expenses.grand_total_actual', 'expenses.status', 'expenses.end_date')
            ->with(['expenseDetails:id,expense_id,quantity_expect,quantity_get', 'supplier:id,name'])
            ->when($this->search, function ($q) {
                $q->where('expenses.expense_number', 'like', '%'.$this->search.'%');
            })
            ->where('is_finish', true);

        if ($this->sortField === 'supplier_name') {
            $query->join('suppliers', 'expenses.supplier_id', '=', 'suppliers.id')
                ->orderBy('suppliers.name', $this->sortDirection)
                ->select('expenses.*');
        } else {
            $query->orderBy("expenses.{$this->sortField}", $this->sortDirection);
        }

        $expenses = $query->paginate(10);

        return view('livewire.expense.riwayat', [
            'expenses' => $expenses,
        ]);
    }
}
