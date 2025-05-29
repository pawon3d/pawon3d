<?php

namespace App\Livewire\Expense;

use Illuminate\Support\Facades\View;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

class Rincian extends Component
{
    use \Jantinnerezo\LivewireAlert\LivewireAlert;
    public $expense_id;
    public $expense;
    public $expenseDetails;
    public $showHistoryModal = false;
    public $activityLogs = [];
    public $total_quantity_expect, $total_quantity_get, $percentage;
    public $is_start = false, $is_finish = false;

    protected $listeners = [
        'delete'
    ];

    public function mount($id)
    {
        $this->expense_id = $id;
        $this->expense = \App\Models\Expense::with(['expenseDetails', 'supplier'])
            ->findOrFail($this->expense_id);
        $this->is_start = $this->expense->is_start;
        $this->is_finish = $this->expense->is_finish;
        $this->total_quantity_expect = $this->expense->expenseDetails->sum('quantity_expect');
        $this->total_quantity_get = $this->expense->expenseDetails->sum('quantity_get');
        $this->percentage = $this->total_quantity_expect > 0 ? ($this->total_quantity_get / $this->total_quantity_expect) * 100 : 0;
        $this->expenseDetails = $this->expense->expenseDetails;
        View::share('title', 'Rincian Belanja Persediaan');

        if (session()->has('success')) {
            $this->alert('success', session('success'));
        }
    }

    public function riwayatPembaruan()
    {
        $this->activityLogs = Activity::inLog('expenses')->where('subject_id', $this->expense_id)
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function cetakInformasi()
    {
        return redirect()->route('belanja.pdf', [
            'search' => $this->search,
            'id' => $this->expense_id,
        ]);
    }

    public function confirmDelete()
    {
        // Konfirmasi menggunakan Livewire Alert
        $this->alert('warning', 'Apakah Anda yakin ingin menghapus daftar ini?', [
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

        $expense = \App\Models\Expense::findOrFail($this->expense_id);
        if ($expense) {
            $expense->delete();
            return redirect()->intended(route('belanja'))->with('success', 'Daftar belanja berhasil dihapus!');
        } else {
            $this->alert('error', 'belanja tidak ditemukan!');
        }
    }

    public function start()
    {
        $this->is_start = true;
        $expense = \App\Models\Expense::findOrFail($this->expense_id);
        $expense->update(['is_start' => $this->is_start]);
        $this->alert('success', 'Belanja berhasil dimulai.');
    }
    public function finish()
    {
        $this->is_finish = false;
        $expense = \App\Models\Expense::findOrFail($this->expense_id);
        $expense->update(['is_finish' => $this->is_finish]);
        $this->alert('success', 'Belanja berhasil diselesaikan.');
    }

    public function render()
    {
        return view('livewire.expense.rincian', [
            'logName' => Activity::inLog('expenses')->where('subject_id', $this->expense_id)->latest()->first()?->causer->name ?? '-',
        ]);
    }
}
