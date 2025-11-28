<?php

namespace App\Livewire\Transaction;

use App\Models\Shift;
use App\Models\Transaction;
use Illuminate\Support\Facades\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class RincianSesi extends Component
{
    use WithPagination;

    public $shiftId;

    public $shift;

    public $search = '';

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    public $showStruk = false;

    public $selectedPayment = null;

    public $selectedTransaction = null;

    public $showRefundStruk = false;

    public $selectedRefundTransaction = null;

    // Modal detail shift properties
    public $showDetailShiftModal = false;

    public $detailInitialCash = 0;

    public $detailFinalCash = 0;

    public $detailReceivedCash = 0;

    public $detailReceivedNonCash = 0;

    public $detailRefundCash = 0;

    public $detailRefundNonCash = 0;

    public $detailRefundTotal = 0;

    public $detailExpectedCash = 0;

    public $detailDiscountToday = 0;

    public $detailShiftNumber = '';

    public $detailShiftStartTime = null;

    public $detailShiftEndTime = null;

    public $detailShiftOpenedBy = '';

    public $nonCashDetails = [];

    public $showNonCashDetailsModal = false;

    protected $queryString = ['search', 'sortField', 'sortDirection'];

    public function mount($id)
    {
        $this->shiftId = $id;
        $this->shift = Shift::with(['openedBy', 'closedBy'])->findOrFail($id);

        View::share('title', 'Rincian Sesi Penjualan');
        View::share('mainTitle', 'Kasir');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        // For receipt_number, we'll sort by the first payment's receipt_number
        // This requires a subquery, so we'll handle it differently
        if ($field === 'receipt_number') {
            if ($this->sortField === $field) {
                $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                $this->sortField = $field;
                $this->sortDirection = 'asc';
            }
        } else {
            if ($this->sortField === $field) {
                $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                $this->sortField = $field;
                $this->sortDirection = 'asc';
            }
        }
        $this->resetPage();
    }

    public function showStrukModal($paymentId)
    {
        $this->selectedPayment = \App\Models\Payment::with(['transaction.details.product', 'transaction.user', 'channel'])->findOrFail($paymentId);
        $this->selectedTransaction = $this->selectedPayment->transaction;
        $this->showStruk = true;
    }

    public function closeStrukModal()
    {
        $this->showStruk = false;
        $this->selectedPayment = null;
        $this->selectedTransaction = null;
    }

    public function showRefundStrukModal($transactionId)
    {
        $this->selectedRefundTransaction = Transaction::with([
            'details.product',
            'user',
            'payments.channel',
            'refund.channel',
        ])->findOrFail($transactionId);
        $this->showRefundStruk = true;
    }

    public function closeRefundStrukModal()
    {
        $this->showRefundStruk = false;
        $this->selectedRefundTransaction = null;
    }

    #[On('openDetailShiftModal')]
    public function openDetailShiftModal($shiftId)
    {
        $shift = Shift::with(['openedBy', 'closedBy'])->findOrFail($shiftId);

        // Calculate received cash
        $receivedCash = Transaction::where('created_by_shift', $shift->id)
            ->whereHas('payments', fn ($q) => $q->where('payment_method', 'tunai'))
            ->with(['payments' => fn ($q) => $q->where('payment_method', 'tunai')])
            ->get()
            ->sum(fn ($t) => $t->payments->sum('paid_amount'));

        // Calculate received non-cash
        $receivedNonCash = Transaction::where('created_by_shift', $shift->id)
            ->whereHas('payments', fn ($q) => $q->where('payment_method', '!=', 'tunai'))
            ->with(['payments' => fn ($q) => $q->where('payment_method', '!=', 'tunai')])
            ->get()
            ->sum(fn ($t) => $t->payments->sum('paid_amount'));

        // Calculate refund totals
        $refundTotal = Transaction::where('refund_by_shift', $shift->id)->sum('total_refund');

        // Calculate refund cash and non-cash separately
        $refundCash = \App\Models\Refund::where('refund_by_shift', $shift->id)
            ->where('refund_method', 'tunai')
            ->sum('total_amount');

        $refundNonCash = \App\Models\Refund::where('refund_by_shift', $shift->id)
            ->where('refund_method', '!=', 'tunai')
            ->sum('total_amount');

        // Set properties
        $this->detailShiftNumber = $shift->shift_number;
        $this->detailShiftStartTime = $shift->start_time;
        $this->detailShiftEndTime = $shift->end_time;
        $this->detailShiftOpenedBy = $shift->openedBy->name ?? 'System';
        $this->detailInitialCash = $shift->initial_cash;
        $this->detailFinalCash = $shift->final_cash;
        $this->detailReceivedCash = $receivedCash;
        $this->detailReceivedNonCash = $receivedNonCash;
        $this->detailRefundTotal = $refundTotal;
        $this->detailRefundCash = $refundCash;
        $this->detailRefundNonCash = $refundNonCash;
        $this->detailDiscountToday = 0;
        $this->detailExpectedCash = $shift->initial_cash + $receivedCash - $refundCash;

        $this->showDetailShiftModal = true;
    }

    public function closeDetailShiftModal()
    {
        $this->showDetailShiftModal = false;
    }

    public function showNonCashDetails($shiftId)
    {
        $this->nonCashDetails = \App\Models\Payment::whereHas('transaction', fn ($q) => $q->where('created_by_shift', $shiftId))
            ->where('payment_method', '!=', 'tunai')
            ->with(['transaction', 'channel'])
            ->get()
            ->map(fn ($p) => [
                'receipt_number' => $p->receipt_number,
                'invoice_number' => $p->transaction->invoice_number,
                'bank_name' => $p->channel->bank_name ?? '-',
                'paid_amount' => $p->paid_amount,
                'paid_at' => $p->paid_at,
            ])
            ->toArray();

        $this->showNonCashDetailsModal = true;
    }

    public function closeNonCashDetailsModal()
    {
        $this->showNonCashDetailsModal = false;
        $this->nonCashDetails = [];
    }

    public function render()
    {
        $query = Transaction::query()
            ->with(['user', 'payments' => function ($query) {
                $query->orderBy('created_at', 'asc');
            }, 'refund'])
            ->where('created_by_shift', $this->shiftId);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('id', 'like', '%'.$this->search.'%')
                    ->orWhere('invoice_number', 'like', '%'.$this->search.'%')
                    ->orWhere('name', 'like', '%'.$this->search.'%')
                    ->orWhereHas('payments', function ($query) {
                        $query->where('receipt_number', 'like', '%'.$this->search.'%');
                    });
            });
        }

        // Handle sorting by receipt_number using a subquery
        if ($this->sortField === 'receipt_number') {
            $query->leftJoin('payments', function ($join) {
                $join->on('transactions.id', '=', 'payments.transaction_id')
                    ->whereRaw('payments.created_at = (SELECT MIN(created_at) FROM payments WHERE payments.transaction_id = transactions.id)');
            })
                ->orderBy('payments.receipt_number', $this->sortDirection)
                ->select('transactions.*');
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        $transactions = $query->paginate(10);

        return view('livewire.transaction.rincian-sesi', [
            'transactions' => $transactions,
        ]);
    }
}
