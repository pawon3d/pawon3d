<?php

namespace App\Livewire\Production;

use App\Models\ProductionWorker;
use Illuminate\Support\Facades\View;
use Livewire\Component;

class EditProduksiPesanan extends Component
{
    use \Jantinnerezo\LivewireAlert\LivewireAlert;

    public $productionId;

    public $production;

    public $transaction;

    public $method;

    public $details = [];

    public $user_ids;

    public $start_date = 'dd/mm/yyyy';

    public $note;

    public $time;

    public function mount($id)
    {
        $this->productionId = $id;
        $this->production = \App\Models\Production::with(['details.product', 'workers.worker'])->findOrFail($this->productionId);
        $this->transaction = \App\Models\Transaction::with(['details.product', 'user'])->findOrFail($this->production->transaction_id);

        if ($this->production->method == 'pesanan-reguler') {
            $this->method = 'Reguler';
        } else {
            $this->method = 'Kotak';
        }

        $this->details = $this->transaction->details;
        $this->note = $this->production->note ?? '';
        $this->user_ids = $this->production->workers->pluck('user_id')->toArray();

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
        $production = \App\Models\Production::findOrFail($this->productionId);
        $production->update([
            'note' => $this->note,
        ]);

        $transaction = \App\Models\Transaction::find($this->production->transaction_id);
        $transaction->status = 'Sedang Diproses';
        $transaction->save();

        $production->workers()->delete();
        $production->details()->delete();

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

        session()->flash('success', 'Produksi berhasil diperbarui.');

        return redirect()->route('produksi.rincian', ['id' => $production->id]);
    }

    public function render()
    {
        return view('livewire.production.edit-produksi-pesanan', [
            'users' => \App\Models\User::lazy(),
        ]);
    }
}
