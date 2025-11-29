<?php

namespace App\Livewire\Dashboard;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Livewire\Component;
use Livewire\WithPagination;

class RingkasanKasir extends Component
{
    use WithPagination;

    public $currentMonth;

    public $currentYear;

    public $selectedDate;

    public $calendar = [];

    public $todayTransactions = [];

    public $otherTransactions = [];

    public $method = 'pesanan-reguler';

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    protected $queryString = [
        'method' => ['except' => 'pesanan-reguler'],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function mount()
    {
        $this->currentMonth = now()->startOfMonth();
        $this->currentYear = now()->year;
        $this->selectedDate = now()->toDateString();
        $this->generateCalendar();
        $this->fetchTransactions();
        View::share('title', 'Ringkasan Umum');
        View::share('mainTitle', 'Dashboard');
    }

    public function previousMonth()
    {
        $this->currentMonth = Carbon::parse($this->currentMonth)->subMonth();
        $this->generateCalendar();
        $this->fetchTransactions();
    }

    public function nextMonth()
    {
        $this->currentMonth = Carbon::parse($this->currentMonth)->addMonth();
        $this->generateCalendar();
        $this->fetchTransactions();
    }

    public function selectDate($date)
    {
        $this->selectedDate = $date;
        $carbonDate = \Carbon\Carbon::parse($date);
        $this->currentMonth = $carbonDate->copy()->startOfMonth(); // tetap Carbon
        $this->currentYear = $carbonDate->year;

        $this->generateCalendar();
        $this->fetchTransactions();
    }

    public function generateCalendar()
    {
        $monthStart = $this->currentMonth->copy()->startOfMonth();
        $start = Carbon::create($this->currentYear, Carbon::parse($this->currentMonth)->month, 1)->startOfWeek();
        $end = Carbon::create($this->currentYear, Carbon::parse($this->currentMonth)->month, 1)
            ->endOfMonth()
            ->endOfWeek();

        $transactionDates = Transaction::whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->pluck('date')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->toArray();
        $calendar = [];

        while ($start <= $end) {
            $dateString = $start->toDateString();

            $calendar[$dateString] = [
                'isCurrentMonth' => $start->month === $monthStart->month,
                'date' => $start->copy(),
                'hasTransaction' => in_array($dateString, $transactionDates),
            ];
            $start->addDay();
        }

        $this->calendar = $calendar;
    }

    public function fetchTransactions()
    {
        $this->todayTransactions = Transaction::whereDate('date', $this->selectedDate)->get();

        $this->otherTransactions = Transaction::whereDate('date', '!=', $this->selectedDate)
            ->whereMonth('date', Carbon::parse($this->currentMonth)->month)
            ->whereYear('date', Carbon::parse($this->currentMonth)->year)
            ->whereNotIn('status', ['Draft', 'temp', 'Selesai', 'Gagal'])
            ->get();
    }

    public function sortBy($field)
    {
        if ($this->method == 'siap-beli' && $field == 'date') {
            $field = 'start_date';
        } elseif ($this->method != 'siap-beli' && $field == 'date') {
            $field = 'date';
        }
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function render()
    {
        $query = \App\Models\Transaction::with(['details.product', 'user'])
            ->where('transactions.method', $this->method)
            ->whereNotIn('transactions.status', ['Gagal', 'Selesai', 'temp']);
        if ($this->method != 'siap-beli') {
            $query->where('transactions.date', '>=', now());
        } else {
            $query->where('transactions.start_date', '>=', now());
        }

        if ($this->sortField === 'product_name') {
            $query->join('transaction_details', 'transactions.id', '=', 'transaction_details.transaction_id')
                ->join('products', 'transaction_details.product_id', '=', 'products.id')
                ->orderBy('products.name', $this->sortDirection);
        } elseif ($this->sortField === 'user_name') {
            $query->join('users', 'transactions.user_id', '=', 'users.id')
                ->orderBy('users.name', $this->sortDirection);
        } else {
            $query->orderBy("transactions.{$this->sortField}", $this->sortDirection);
        }
        $transactions = $query->select('transactions.*')->distinct()->paginate(10);

        return view(
            'livewire.dashboard.ringkasan-kasir',
            [
                'transactions' => $transactions,
            ]
        );
    }
}
