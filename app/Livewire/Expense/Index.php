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
        return view('livewire.expense.index', [
            'expenses' => \App\Models\Expense::with(['expenseDetails', 'supplier'])
                ->when($this->search, function ($query) {
                    $query->where('expense_number', 'like', '%' . $this->search . '%');
                })
                ->latest()
                ->paginate(10)
        ]);
    }
}