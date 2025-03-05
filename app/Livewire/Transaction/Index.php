<?php

namespace App\Livewire\Transaction;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Transaction;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Index extends Component
{
    use WithPagination, LivewireAlert;

    public $search = '';
    public $typeFilter = 'all';
    public $paymentStatusFilter = 'all';
    public $showDetailModal = false;
    public $selectedTransaction = null;
    public $delete_id;

    protected $listeners = [
        'refreshTransactions' => '$refresh',
        'delete'
    ];

    public function mount()
    {
        View::share('title', 'Transaksi');
    }

    public function render()
    {
        $transactions = Transaction::with(['user'])
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->typeFilter !== 'all', function ($query) {
                $query->where('type', $this->typeFilter);
            })
            ->when($this->paymentStatusFilter !== 'all', function ($query) {
                $query->where('payment_status', $this->paymentStatusFilter);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.transaction.index', [
            'transactions' => $transactions
        ]);
    }

    public function updatePaymentStatus($transactionId, $status)
    {
        $transaction = Transaction::find($transactionId);
        $transaction->update(['payment_status' => $status]);
        $this->alert('success', 'Status pembayaran berhasil diperbarui!');
    }

    public function showDetail($transactionId)
    {
        $this->selectedTransaction = Transaction::with(['user'])
            ->find($transactionId);
        $this->showDetailModal = true;
    }

    public function deleteTransaction($transactionId)
    {
        $this->delete_id = $transactionId;
        $this->alert('warning', 'Apakah Anda yakin ingin menghapus produk ini?', [
            'showConfirmButton' => true,
            'showCancelButton' => true,
            'confirmButtonText' => 'Ya, hapus',
            'cancelButtonText' => 'Batal',
            'onConfirmed' => 'delete',
            'onCancelled' => 'cancelled',
            'toast' => false,
            'position' => 'center',
            'timer' => null,
        ]);
    }

    public function delete()
    {
        $transaction = Transaction::find($this->delete_id);
        $transaction->delete();
        $this->alert('success', 'Transaksi berhasil dihapus!');
        $this->reset('delete_id');
    }
}
