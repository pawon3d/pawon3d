<?php

namespace App\Livewire\Dashboard;

use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Livewire\Component;
use Livewire\WithPagination;

class RingkasanInventori extends Component
{
    use WithPagination;

    public $currentMonth;

    public $currentYear;

    public $selectedDate;

    public $calendar = [];

    public $todayExpenses = [];

    public $otherExpenses = [];

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    protected $queryString = [
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function mount()
    {
        $this->currentMonth = now()->startOfMonth();
        $this->currentYear = now()->year;
        $this->selectedDate = now()->toDateString();
        $this->generateCalendar();
        $this->fetchExpenses();
        View::share('title', 'Ringkasan Umum');
        View::share('mainTitle', 'Dashboard');
    }

    public function previousMonth()
    {
        $this->currentMonth = Carbon::parse($this->currentMonth)->subMonth();
        $this->generateCalendar();
        $this->fetchExpenses();
    }

    public function nextMonth()
    {
        $this->currentMonth = Carbon::parse($this->currentMonth)->addMonth();
        $this->generateCalendar();
        $this->fetchExpenses();
    }

    public function selectDate($date)
    {
        $this->selectedDate = $date;
        $carbonDate = \Carbon\Carbon::parse($date);
        $this->currentMonth = $carbonDate->copy()->startOfMonth(); // tetap Carbon
        $this->currentYear = $carbonDate->year;

        $this->generateCalendar();
        $this->fetchExpenses();
    }

    public function generateCalendar()
    {
        $monthStart = $this->currentMonth->copy()->startOfMonth();
        $start = Carbon::create($this->currentYear, Carbon::parse($this->currentMonth)->month, 1)->startOfWeek();
        $end = Carbon::create($this->currentYear, Carbon::parse($this->currentMonth)->month, 1)
            ->endOfMonth()
            ->endOfWeek();

        $expenseDates = Expense::whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
            ->pluck('expense_date')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->toArray();
        $calendar = [];

        while ($start <= $end) {
            $dateString = $start->toDateString();

            $calendar[$dateString] = [
                'isCurrentMonth' => $start->month === $monthStart->month,
                'date' => $start->copy(),
                'hasExpense' => in_array($dateString, $expenseDates),
            ];
            $start->addDay();
        }

        $this->calendar = $calendar;
    }

    public function fetchExpenses()
    {
        $this->todayExpenses = Expense::whereDate('expense_date', $this->selectedDate)->get();

        $this->otherExpenses = Expense::whereDate('expense_date', '!=', $this->selectedDate)
            ->whereMonth('expense_date', Carbon::parse($this->currentMonth)->month)
            ->whereYear('expense_date', Carbon::parse($this->currentMonth)->year)
            // ->whereNotIn('status', ['Selesai', 'Gagal'])
            ->get();
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

    public function redirectToMaterial($id)
    {
        return redirect()->route('bahan-baku.edit', ['id' => $id]);
    }

    public function render()
    {
        $query = \App\Models\Expense::with(['expenseDetails', 'supplier']);

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
        $materials = \App\Models\Material::with(['material_details', 'batches'])
            ->get();
        $filteredMaterials = $materials->filter(function ($material) {
            $total = $material->batches->sum('batch_quantity');

            return ($total >= $material->minimum) && ($total <= $material->minimum * 2);
        });

        return view('livewire.dashboard.ringkasan-inventori', [
            'expenses' => $expenses,
            'materials' => $filteredMaterials,
            'materialB' => $materials,
        ]);
    }
}
