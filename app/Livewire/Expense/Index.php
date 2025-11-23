<?php

namespace App\Livewire\Expense;

use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

class Index extends Component
{
    use LivewireAlert, WithPagination;

    public $search = '';

    public $filterStatus = '';

    public $showHistoryModal = false;

    public $activityLogs = [];

    public $sortField = 'expense_number';

    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'expense_number'],
        'sortDirection' => ['except' => 'desc'],
    ];

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

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function riwayatPembaruan()
    {
        $this->activityLogs = Activity::inLog('expenses')
            ->with('causer:id,name')
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
        $query = \App\Models\Expense::select('expenses.id', 'expenses.expense_number', 'expenses.expense_date', 'expenses.supplier_id', 'expenses.grand_total_expect', 'expenses.grand_total_actual', 'expenses.status')
            ->with(['expenseDetails:id,expense_id,quantity_expect,quantity_get', 'supplier:id,name'])
            ->when($this->search, function ($q) {
                $q->where('expenses.expense_number', 'like', '%'.$this->search.'%');
            })
            ->whereIn('status', ['Dimulai', 'Lengkap', 'Hampir Lengkap', 'Separuh', 'Sedikit']);

        if ($this->sortField === 'supplier_name') {
            $query->join('suppliers', 'expenses.supplier_id', '=', 'suppliers.id')
                ->orderBy('suppliers.name', $this->sortDirection)
                ->select('expenses.*');
        } else {
            $query->orderBy("expenses.{$this->sortField}", $this->sortDirection);
        }

        $expenses = $query->paginate(10);

        return view('livewire.expense.index', [
            'expenses' => $expenses,
        ]);
    }
}
