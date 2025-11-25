<?php

namespace App\Livewire\Production;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Livewire\Component;

class RincianPesanan extends Component
{
    public $transactionId;

    public $transaction;

    public $details = [];

    public $activityLogs = [];

    public $showHistoryModal = false;

    public $note;

    public function mount($id)
    {
        View::share('title', 'Rincian Pesanan');
        View::share('mainTitle', 'Produksi');
        $this->transactionId = $id;
        $this->transaction = \App\Models\Transaction::with(['details.product', 'user'])
            ->findOrFail($this->transactionId);
        $this->details = $this->transaction->details;
        $this->activityLogs = \Spatie\Activitylog\Models\Activity::inLog('transactions')
            ->where('subject_id', $this->transactionId)
            ->latest()
            ->limit(50)
            ->get();
    }

    public function riwayatPembaruan()
    {
        $this->showHistoryModal = true;
    }

    public function cetakInformasi()
    {
        return redirect()->route('produksi.pdf', [
            'transaction_id' => $this->transactionId,
        ]);
    }

    public function start()
    {
        $produkGagal = [];

        foreach ($this->details as $detail) {
            $product = \App\Models\Product::find($detail->product_id);
            $quantityPlan = $detail->quantity;
            $kurang = false;

            foreach ($product->product_compositions as $composition) {
                $materialBatches = \App\Models\MaterialBatch::where('material_id', $composition->material_id)
                    ->where('unit_id', $composition->unit_id)
                    ->orderBy('date')
                    ->where('date', '>=', now()->format('Y-m-d'))
                    ->get();
                $batchQty = $materialBatches->sum('batch_quantity');
                $requiredQuantity = $quantityPlan / $composition->product->pcs * $composition->material_quantity;
                if (! $materialBatches || $batchQty < $requiredQuantity) {
                    $kurang = true;
                    break;
                }
            }

            if ($kurang) {
                $produkGagal[] = $product->name;
            }
        }

        if (! empty($produkGagal)) {
            $this->alert('error', 'Bahan baku tidak cukup untuk: '.implode(', ', $produkGagal));

            return;
        }

        $production = \App\Models\Production::create([
            'start_date' => now()->format('Y-m-d'),
            'note' => $this->note,
            'method' => $this->transaction->method,
            'status' => 'Sedang Diproses',
            'is_start' => true,
            'date' => now(),
            'time' => now()->format('H:i'),
            'transaction_id' => $this->transactionId,
        ]);

        $transaction = \App\Models\Transaction::find($this->transactionId);
        $transaction->status = 'Sedang Diproses';
        $transaction->save();

        \App\Models\ProductionWorker::create([
            'production_id' => $production->id,
            'user_id' => Auth::user()->id,
        ]);

        foreach ($this->details as $detail) {
            $production->details()->create([
                'product_id' => $detail->product_id,
                'quantity_plan' => $detail->quantity,
            ]);
        }

        session()->flash('success', 'Produksi berhasil dimulai.');

        return redirect()->route('produksi.rincian', ['id' => $production->id]);
    }

    public function render()
    {
        return view('livewire.production.rincian-pesanan');
    }
}
