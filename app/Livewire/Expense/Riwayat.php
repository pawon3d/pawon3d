<?php

namespace App\Livewire\Expense;

use Illuminate\Support\Facades\View;
use Livewire\Component;

class Riwayat extends Component
{
    public $search = '';
    public $filterStatus = '';
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
        return view('livewire.expense.riwayat', [
            'expenses' => \App\Models\Expense::with(['expenseDetails', 'supplier'])
                ->when($this->search, function ($query) {
                    $query->where('expense_number', 'like', '%' . $this->search . '%');
                })->where('is_finish', true)
                ->latest()
                ->paginate(10)
        ]);
    }
}