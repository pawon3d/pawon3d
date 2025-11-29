<?php

namespace App\Livewire\Production;

use App\Models\ProductionWorker;
use Illuminate\Support\Facades\View;
use Livewire\Component;

class TambahProduksiPesanan extends Component
{
    use \Jantinnerezo\LivewireAlert\LivewireAlert;

    public $transactionId;

    public $transaction;

    public $method;

    public $details = [];

    public $user_ids;

    public $note;

    public function mount($id)
    {
        $this->transactionId = $id;
        $this->transaction = \App\Models\Transaction::with(['details.product', 'user'])->findOrFail($this->transactionId);

        if ($this->transaction->method == 'pesanan-reguler') {
            $this->method = 'Reguler';
        } else {
            $this->method = 'Kotak';
        }

        $this->details = $this->transaction->details;

        View::share('title', 'Rencana Produksi '.$this->method);
        View::share('mainTitle', 'Produksi');
    }

    public function start()
    {
        $this->validate([
            'user_ids' => 'required|array',
            'note' => 'nullable|string|max:255',
        ]);

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

        foreach ($this->user_ids as $user_id) {
            ProductionWorker::create([
                'production_id' => $production->id,
                'user_id' => $user_id,
            ]);
        }
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
        return view('livewire.production.tambah-produksi-pesanan', [
            'users' => \App\Models\User::lazy(),
        ]);
    }
}
