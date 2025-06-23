<?php

namespace App\Livewire\Expense;

use Carbon\Carbon;
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
    public $is_start = false, $is_finish = false, $status, $end_date;

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
        $this->status = $this->expense->status;
        $this->end_date = $this->expense->end_date;
        $this->total_quantity_expect = $this->expense->expenseDetails->sum('quantity_expect');
        $this->total_quantity_get = $this->expense->expenseDetails->sum('quantity_get');
        $this->percentage = $this->total_quantity_expect > 0 ? ($this->total_quantity_get / $this->total_quantity_expect) * 100 : 0;
        $this->percentage = floor($this->percentage);
        $this->expenseDetails = $this->expense->expenseDetails;
        View::share('title', 'Rincian Belanja Persediaan');
        View::share('mainTitle', 'Inventori');

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
        return redirect()->route('rincian-belanja.pdf', [
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
        $this->status = 'Dimulai';
        $expense = \App\Models\Expense::findOrFail($this->expense_id);
        $expense->update(['is_start' => $this->is_start, 'status' => $this->status]);
        $this->alert('success', 'Belanja berhasil dimulai.');
    }
    public function finish()
    {
        $this->is_finish = true;
        $this->status = 'Selesai';
        $this->end_date = Carbon::now()->toDateTimeString();
        $expense = \App\Models\Expense::findOrFail($this->expense_id);
        $expense->update([
            'is_finish' => $this->is_finish,
            'status' => 'Selesai',
            'end_date' => Carbon::now()->toDateTimeString(),
        ]);
        $expense->expenseDetails->each(function ($detail) {
            $materialDetail = \App\Models\MaterialDetail::where('material_id', $detail->material_id)
                ->where('unit_id', $detail->unit_id)
                ->first();
            if ($materialDetail) {
                $materialDetail->update([
                    'supply_quantity' => $materialDetail->supply_quantity + $detail->quantity_get,
                ]);
            }
            // Generate a batch number if not present in detail
            $batchNumber = 'B-' . Carbon::parse($detail->expiry_date)->format('ymd');

            // Cek apakah sudah ada batch dengan batch_number dan unit_id yang sama
            $materialBatch = \App\Models\MaterialBatch::where('batch_number', $batchNumber)
                ->where('unit_id', $detail->unit_id)
                ->first();

            if ($materialBatch) {
                // Jika ada, update batch_quantity
                $materialBatch->update([
                    'batch_quantity' => $materialBatch->batch_quantity + $detail->quantity_get,
                    'date' => $detail->expiry_date,
                    'material_id' => $detail->material_id,
                ]);
            } else {
                // Jika tidak ada, create baru
                \App\Models\MaterialBatch::create([
                    'unit_id' => $detail->unit_id,
                    'material_id' => $detail->material_id,
                    'batch_quantity' => $detail->quantity_get,
                    'date' => $detail->expiry_date,
                ]);
            }
        });
        if ($this->expense->expenseDetails->sum('quantity_get') > 0) {
            if ($this->expense->expenseDetails->sum('quantity_get') >= $this->expense->expenseDetails->sum('quantity_expect')) {
                $this->expense->update(['status' => 'Lengkap']);
                $this->status = 'Lengkap';
            } elseif ($this->expense->expenseDetails->sum('quantity_get') >= 0.8 * $this->expense->expenseDetails->sum('quantity_expect')) {
                $this->expense->update(['status' => 'Hampir Lengkap']);
                $this->status = 'Hampir Lengkap';
            } elseif ($this->expense->expenseDetails->sum('quantity_get') >= 0.5 * $this->expense->expenseDetails->sum('quantity_expect')) {
                $this->expense->update(['status' => 'Separuh']);
                $this->status = 'Separuh';
            } else {
                $this->expense->update(['status' => 'Sedikit']);
                $this->status = 'Sedikit';
            }
        } else {
            $this->expense->update(['status' => 'Gagal']);
            $this->status = 'Gagal';
        }
        $this->alert('success', 'Belanja berhasil diselesaikan.');
    }

    public function render()
    {
        return view('livewire.expense.rincian', [
            'logName' => Activity::inLog('expenses')->where('subject_id', $this->expense_id)->latest()->first()?->causer->name ?? '-',
        ]);
    }
}
