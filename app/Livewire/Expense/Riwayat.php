<?php

namespace App\Livewire\Expense;

use Illuminate\Support\Facades\View;
use Livewire\Component;

class Riwayat extends Component
{
    public $search = '';
    public $filterStatus = '';
    public $sortField = 'expense_number';
    public $sortDirection = 'desc';

    protected $queryString = ['search', 'sortField', 'sortDirection'];


    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }
    public function mount()
    {
        View::share('title', 'Riwayat Belanja Persediaan');
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
        $query = \App\Models\Expense::with(['expenseDetails', 'supplier'])->where('expenses.expense_number', 'like', '%' . $this->search . '%')->where('is_finish', true);

        if ($this->sortField === 'supplier_name') {
            $query
                ->join('suppliers', 'expenses.supplier_id', '=', 'suppliers.id')
                ->orderBy('suppliers.name', $this->sortDirection);
        } else {
            $query->orderBy("expenses.{$this->sortField}", $this->sortDirection);
        }

        $expenses = $query->select('expenses.*')
            ->distinct()
            ->paginate(10);


        return view('livewire.expense.riwayat', [
            'expenses' => $expenses,
        ]);
    }
}
