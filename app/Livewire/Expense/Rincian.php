<?php

namespace App\Livewire\Expense;

use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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

    public $showNoteModal = false;

    public $noteInput = '';

    public $activityLogs = [];

    public $total_quantity_expect;

    public $total_quantity_get;

    public $percentage;

    public $is_start = false;

    public $is_finish = false;

    public $status;

    public $end_date;

    protected $listeners = [
        'delete',
    ];

    public function mount($id)
    {
        $this->expense_id = $id;
        $this->expense = \App\Models\Expense::select('id', 'supplier_id', 'expense_number', 'expense_date', 'note', 'grand_total_expect', 'grand_total_actual', 'is_start', 'is_finish', 'status', 'end_date')
            ->with([
                'expenseDetails:id,expense_id,material_id,unit_id,quantity_expect,quantity_get,price_expect,price_actual,total_expect,total_actual,expiry_date',
                'supplier:id,name,contact_name,phone',
            ])
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
        $this->noteInput = $this->expense->note ?? '';
        View::share('title', 'Rincian Belanja Persediaan');
        View::share('mainTitle', 'Inventori');

        if (session()->has('success')) {
            $this->alert('success', session('success'));
        }
    }

    public function riwayatPembaruan()
    {
        $this->activityLogs = Activity::inLog('expenses')
            ->where('subject_id', $this->expense_id)
            ->with('causer:id,name')
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function editRencanaBelanja()
    {
        if ($this->is_start) {
            return;
        }

        $this->noteInput = $this->expense->note ?? '';
        $this->showNoteModal = true;
    }

    public function saveNote()
    {
        if ($this->is_start) {
            return;
        }

        $this->validate([
            'noteInput' => 'nullable|string|max:255',
        ]);

        $noteValue = trim($this->noteInput ?? '');

        $expense = \App\Models\Expense::findOrFail($this->expense_id);
        $expense->update([
            'note' => $noteValue === '' ? null : $noteValue,
        ]);

        $this->expense->note = $noteValue === '' ? null : $noteValue;
        $this->noteInput = $this->expense->note ?? '';
        $this->showNoteModal = false;
        $this->alert('success', 'Catatan belanja berhasil diperbarui.');
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
            $expenseNumber = $expense->expense_number;
            $expense->delete();

            // Kirim notifikasi belanja dibatalkan
            NotificationService::shoppingCancelled($expenseNumber);

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

        // Kirim notifikasi belanja dimulai
        NotificationService::shoppingStarted($expense->expense_number);

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

        $affectedMaterialIds = collect();

        $expense->expenseDetails->each(function ($detail) use (&$affectedMaterialIds, $expense) {
            $materialDetail = \App\Models\MaterialDetail::where('material_id', $detail->material_id)
                ->where('unit_id', $detail->unit_id)
                ->first();
            if ($materialDetail) {
                $materialDetail->update([
                    'supply_quantity' => $materialDetail->supply_quantity + $detail->quantity_get,
                ]);
            }

            // Track material yang terpengaruh
            $affectedMaterialIds->push($detail->material_id);

            // Generate a batch number if not present in detail
            $batchNumber = 'B-' . Carbon::parse($detail->expiry_date)->format('ymd');

            // Ambil material dan base unit untuk konsolidasi
            $material = \App\Models\Material::find($detail->material_id);
            $currentUnit = \App\Models\Unit::find($detail->unit_id);

            if (! $material || ! $currentUnit) {
                return; // Di dalam each() callback, gunakan return bukan continue
            }

            // Tentukan target unit - gunakan base unit dari grup unit ini
            $targetUnit = $currentUnit->base_unit_id
                ? \App\Models\Unit::find($currentUnit->base_unit_id)
                : $currentUnit;

            if (! $targetUnit) {
                $targetUnit = $currentUnit;
            }

            // Konversi quantity ke target unit
            $quantityInTargetUnit = $currentUnit->convertTo($detail->quantity_get, $targetUnit);

            // Jika tidak bisa konversi, gunakan unit asli
            if ($quantityInTargetUnit === null) {
                $quantityInTargetUnit = $detail->quantity_get;
                $targetUnit = $currentUnit;
            }

            // Cek apakah sudah ada batch dengan batch_number dan unit_id yang sama
            $materialBatch = \App\Models\MaterialBatch::where('batch_number', $batchNumber)
                ->where('unit_id', $targetUnit->id)
                ->where('material_id', $detail->material_id)
                ->first();

            if ($materialBatch) {
                // Jika ada, update batch_quantity
                $quantityBefore = $materialBatch->batch_quantity;
                $materialBatch->update([
                    'batch_quantity' => $materialBatch->batch_quantity + $quantityInTargetUnit,
                    'date' => $detail->expiry_date,
                ]);

                // Create inventory log untuk penambahan stok
                \App\Models\InventoryLog::create([
                    'material_id' => $detail->material_id,
                    'material_batch_id' => $materialBatch->id,
                    'user_id' => Auth::id(),
                    'action' => 'belanja',
                    'quantity_change' => $quantityInTargetUnit,
                    'quantity_after' => $materialBatch->batch_quantity,
                    'reference_type' => 'expense',
                    'reference_id' => $expense->id,
                    'note' => "Belanja: {$expense->expense_number} (dari {$detail->quantity_get} {$currentUnit->name})",
                ]);
            } else {
                // Jika tidak ada, create baru dengan target unit
                $newBatch = \App\Models\MaterialBatch::create([
                    'unit_id' => $targetUnit->id,
                    'material_id' => $detail->material_id,
                    'batch_quantity' => $quantityInTargetUnit,
                    'date' => $detail->expiry_date,
                ]);

                // Create inventory log untuk penambahan stok
                \App\Models\InventoryLog::create([
                    'material_id' => $detail->material_id,
                    'material_batch_id' => $newBatch->id,
                    'user_id' => Auth::id(),
                    'action' => 'belanja',
                    'quantity_change' => $quantityInTargetUnit,
                    'quantity_after' => $quantityInTargetUnit,
                    'reference_type' => 'expense',
                    'reference_id' => $expense->id,
                    'note' => "Belanja: {$expense->expense_number} (dari {$detail->quantity_get} {$currentUnit->name})",
                ]);
            }
        });

        // Recalculate status for all affected materials
        $affectedMaterialIds->unique()->each(function ($materialId) {
            $material = \App\Models\Material::find($materialId);
            if ($material) {
                $material->recalculateStatus();
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

        // Kirim notifikasi belanja selesai
        NotificationService::shoppingCompleted($this->expense->expense_number);
        NotificationService::purchaseReceived($this->expense->expense_number);

        $this->alert('success', 'Belanja berhasil diselesaikan.');
    }

    public function render()
    {
        return view('livewire.expense.rincian', [
            'logName' => Activity::inLog('expenses')->where('subject_id', $this->expense_id)->latest()->first()?->causer->name ?? '-',
        ]);
    }
}
