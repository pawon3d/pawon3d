<?php

namespace App\Livewire\Expense;

use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

class Index extends Component
{

    use WithPagination, LivewireAlert;

    public $search = '';
    public $filterStatus = '';
    public $showHistoryModal = false;
    public $activityLogs = [];
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
        $this->resetPage();
    }

    public function riwayatPembaruan()
    {
        $this->activityLogs = Activity::inLog('expenses')
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function mount()
    {
        View::share('title', 'Daftar Belanja Persediaan');
        View::share('mainTitle', 'Inventori');
        if (session()->has('success')) {
            $this->alert('success', session('success'));
        }
    }

    public function cetakInformasi()
    {
        return redirect()->route('belanja.pdf', [
            'search' => $this->search,
            'status' => 'all',
        ]);
    }
    public function render()
    {
        $query = \App\Models\Expense::with(['expenseDetails', 'supplier'])->where('expenses.expense_number', 'like', '%' . $this->search . '%');

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

        return view('livewire.expense.index', [
            'expenses' => $expenses,
        ]);
    }
}
