<?php

namespace App\Livewire\Transaction;

use App\Models\Payment;
use App\Models\Shift;
use Illuminate\Support\Facades\View;
use Livewire\Component;
use Livewire\WithPagination;

class RiwayatSesiPenjualan extends Component
{
    use WithPagination;

    public $search = '';

    public $searchDate = '';

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    protected $queryString = ['search', 'searchDate', 'sortField', 'sortDirection'];

    public function mount()
    {
        View::share('title', 'Riwayat Sesi Penjualan');
        View::share('mainTitle', 'Kasir');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSearchDate()
    {
        $this->resetPage();
    }

    public function sortBy($field)
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
        $query = Shift::query()
            ->with(['openedBy', 'closedBy']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('shift_number', 'like', '%'.$this->search.'%')
                    ->orWhereHas('openedBy', function ($q) {
                        $q->where('name', 'like', '%'.$this->search.'%');
                    });
            });
        }

        if ($this->searchDate) {
            $query->whereDate('start_time', $this->searchDate);
        }

        $query->orderBy($this->sortField, $this->sortDirection);

        $shifts = $query->paginate(10);

        // Calculate received cash and non-cash for each shift
        foreach ($shifts as $shift) {
            $receivedCash = Payment::whereHas('transaction', function ($q) use ($shift) {
                $q->where('shift_id', $shift->id)
                    ->where('transaction_type', '!=', 'refund');
            })->where('payment_method', 'Tunai')->sum('amount');

            $receivedNonCash = Payment::whereHas('transaction', function ($q) use ($shift) {
                $q->where('shift_id', $shift->id)
                    ->where('transaction_type', '!=', 'refund');
            })->where('payment_method', '!=', 'Tunai')->sum('amount');

            $shift->received_cash = $receivedCash;
            $shift->received_non_cash = $receivedNonCash;
            $shift->total_received = $receivedCash + $receivedNonCash;
        }

        return view('livewire.transaction.riwayat-sesi-penjualan', [
            'shifts' => $shifts,
        ]);
    }
}
