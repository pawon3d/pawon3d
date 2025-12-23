<?php

namespace App\Livewire\Expense;

use Illuminate\Support\Facades\View;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

class Mulai extends Component
{
    use \Jantinnerezo\LivewireAlert\LivewireAlert;

    public $expense_id;

    public $expense;

    public $expenseDetails = [];

    public $errorInputs = [];

    public $showHistoryModal = false;

    public $activityLogs = [];

    public $total_quantity_expect;

    public $total_quantity_get;

    public $percentage;

    public function mount($id)
    {
        $this->expense_id = $id;
        $this->expense = \App\Models\Expense::with(['expenseDetails', 'supplier'])
            ->findOrFail($this->expense_id);
        $this->total_quantity_expect = $this->expense->expenseDetails->sum('quantity_expect');
        $this->total_quantity_get = $this->expense->expenseDetails->sum('quantity_get');
        $this->percentage = $this->total_quantity_expect > 0 ? ($this->total_quantity_get / $this->total_quantity_expect) * 100 : 0;
        
        // Filter hanya item yang masih kurang (quantity_get < quantity_expect)
        $this->expenseDetails = $this->expense->expenseDetails
            ->filter(fn($detail) => $detail->quantity_get < $detail->quantity_expect)
            ->map(function ($detail) {
                return [
                    'id' => $detail->id,
                    'material_name' => $detail->material->name,
                    'quantity_expect' => $detail->quantity_expect,
                    'quantity_get' => $detail->quantity_get,
                    'price_expect' => $detail->price_expect,
                    'price_get' => $detail->price_get ?? $detail->price_expect,
                    'unit' => $detail->unit->name . ' (' . $detail->unit->alias . ')',
                    'quantity' => 0,
                    'expiry_date' => null,
                ];
            })->values()->toArray();
            
        View::share('title', 'Dapatkan Belanja');
        View::share('mainTitle', 'Inventori');
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

    public function validateQuantities()
    {
        $this->errorInputs = [];

        foreach ($this->expenseDetails as $index => $detail) {
            $expenseDetail = \App\Models\ExpenseDetail::find($detail['id']);
            if ($expenseDetail) {
                $sisa = $detail['quantity_expect'] - $detail['quantity_get'];
                if ($detail['quantity'] > $sisa) {
                    $this->errorInputs[$index] = true;
                }
            }
        }
    }

    public function updatedExpenseDetails($value, $key)
    {
        $this->validateQuantities();
    }

    public function save()
    {
        $this->validate([
            'expenseDetails.*.quantity' => 'required|numeric|min:0',
            'expenseDetails.*.price_get' => 'required|numeric|min:0',
        ], [
            'expenseDetails.*.quantity.required' => 'Jumlah yang didapatkan harus diisi.',
            'expenseDetails.*.quantity.numeric' => 'Jumlah yang didapatkan harus berupa angka.',
            'expenseDetails.*.quantity.min' => 'Jumlah yang didapatkan tidak boleh kurang dari 0.',
            'expenseDetails.*.price_get.required' => 'Harga satuan didapat harus diisi.',
            'expenseDetails.*.price_get.numeric' => 'Harga satuan didapat harus berupa angka.',
            'expenseDetails.*.price_get.min' => 'Harga satuan didapat tidak boleh kurang dari 0.',
        ]);

        $this->validateQuantities();

        if (count($this->errorInputs) > 0) {
            $this->alert('error', 'Masih ada input yang melebihi jumlah yang diharapkan.');

            return;
        }

        foreach ($this->expenseDetails as $detail) {
            $expenseDetail = \App\Models\ExpenseDetail::find($detail['id']);
            if ($expenseDetail) {
                // Hitung quantity baru yang ditambahkan
                $quantityToAdd = $detail['quantity'];

                // Update detail belanja dengan price_get
                $updatedQuantityGet = $detail['quantity_get'] + $quantityToAdd;
                $expenseDetail->update([
                    'quantity_get' => $updatedQuantityGet,
                    'price_get' => $detail['price_get'],
                    'total_actual' => $updatedQuantityGet * $detail['price_get'],
                    'expiry_date' => $detail['expiry_date'] ? \Carbon\Carbon::createFromFormat('d M Y', $detail['expiry_date'])->format('Y-m-d') : null,
                ]);

                // Update total keseluruhan belanja
                $expenseDetail->expense->update([
                    'grand_total_actual' => $expenseDetail->expense->expenseDetails->sum('total_actual'),
                ]);
            }
        }

        if ($this->expense->expenseDetails->sum('quantity_get') >= $this->expense->expenseDetails->sum('quantity_expect')) {
            $this->expense->update(['status' => 'Lengkap']);
        } elseif ($this->expense->expenseDetails->sum('quantity_get') >= 0.8 * $this->expense->expenseDetails->sum('quantity_expect')) {
            $this->expense->update(['status' => 'Hampir Lengkap']);
        } elseif ($this->expense->expenseDetails->sum('quantity_get') >= 0.5 * $this->expense->expenseDetails->sum('quantity_expect')) {
            $this->expense->update(['status' => 'Separuh']);
        } else {
            $this->expense->update(['status' => 'Sedikit']);
        }

        return redirect()->route('belanja.rincian', ['id' => $this->expense_id])
            ->with('success', 'Jumlah yang didapatkan berhasil diperbarui.');
    }

    public function markAllReceived()
    {
        foreach ($this->expenseDetails as $index => $detail) {
            $expenseDetail = \App\Models\ExpenseDetail::find($detail['id']);

            if ($expenseDetail) {
                $remaining = $expenseDetail->quantity_expect - $expenseDetail->quantity_get;

                // Hanya isi jika masih ada yang belum didapat
                if ($remaining > 0) {
                    $this->expenseDetails[$index]['quantity'] = $remaining;
                } else {
                    $this->expenseDetails[$index]['quantity'] = 0;
                }
            }
        }

        // Panggil fungsi simpan biasa
        // $this->save();
    }

    public function render()
    {
        return view('livewire.expense.mulai');
    }
}
