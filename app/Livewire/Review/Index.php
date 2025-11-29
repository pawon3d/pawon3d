<?php

namespace App\Livewire\Review;

use App\Models\Transaction;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use LivewireAlert, WithPagination;

    public $selectedTransaction = null;

    public $showDetailModal = false;

    public $delete_id;

    protected $listeners = [
        'delete',
    ];

    public function mount()
    {
        View::share('title', 'Ulasan');
    }

    public function render()
    {
        $transactions = Transaction::where('reviews_count', '>', 0)->withCount('reviews')->with(['reviews'])
            ->latest()
            ->paginate(10);

        return view('livewire.review.index', [
            'transactions' => $transactions,
        ]);
    }

    public function updateVisibility($reviewId)
    {
        $review = \App\Models\Review::find($reviewId);
        $review->update([
            'visible' => ! $review->visible,
        ]);
        $this->alert('success', 'Ulasan berhasil diperbarui');
    }

    public function showDetail($transactionId)
    {
        $this->selectedTransaction = Transaction::with(['reviews'])
            ->find($transactionId);
        $this->showDetailModal = true;
    }

    public function deleteReviews($transactionId)
    {
        $this->delete_id = $transactionId;
        $this->alert('warning', 'Apakah Anda yakin ingin menghapus semua ulasan dari transaksi ini?', [
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
        $transaction->reviews()->delete();
        $this->alert('success', 'Ulasan berhasil dihapus');
    }
}
