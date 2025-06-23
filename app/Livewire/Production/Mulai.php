<?php

namespace App\Livewire\Production;

use Illuminate\Support\Facades\View;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

class Mulai extends Component
{
    use \Jantinnerezo\LivewireAlert\LivewireAlert;
    public $production_id;
    public $production;
    public $production_details = [];
    public $showHistoryModal = false;
    public $activityLogs = [];
    public $selectedProducts = [];
    public $parsedQuantity;

    public function mount($id)
    {
        $this->production_id = $id;
        $this->production = \App\Models\Production::with(['details'])
            ->findOrFail($this->production_id);
        $this->production_details = $this->production->details->map(function ($detail) {
            return [
                'id' => $detail->id,
                'product_id' => $detail->product_id,
                'product_name' => $detail->product->name,
                'quantity_plan' => $detail->quantity_plan,
                'quantity_get' => $detail->quantity_get,
                'cycle' => $detail->cycle,
                'quantity_fail' => 0,
                'recipe_quantity' => 0,
                'quantity' => 0,
                'quantity_fail_raw' => $detail->quantity_fail,
            ];
        })->toArray();
        View::share('title', 'Dapatkan Hasil Produksi');
        View::share('mainTitle', 'Produksi');
    }

    public function riwayatPembaruan()
    {
        $this->activityLogs = Activity::inLog('productions')->where('subject_id', $this->production_id)
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function updatedProductionDetails()
    {
        foreach ($this->production_details as $index => $detail) {
            $this->parsedQuantity = $this->parseFraction($detail['recipe_quantity'] ?? '');
            $product = \App\Models\Product::find($detail['product_id']);
            $detail['quantity'] = $this->parsedQuantity * $product->pcs;
            $detail['quantity'] = ceil($detail['quantity']);
            $this->production_details[$index]['quantity'] = $detail['quantity'] ?? 0;
        }
    }


    private function parseFraction($input)
    {
        $input = str_replace(',', '.', $input); // Ubah koma ke titik
        $input = preg_replace('/\s+/', ' ', trim($input));

        // Format: 1 1/2
        if (preg_match('/^(\d+)\s+(\d+)\/(\d+)$/', $input, $m)) {
            return (float) $m[1] + ($m[2] / $m[3]);
        }

        // Format: 1/2
        if (preg_match('/^(\d+)\/(\d+)$/', $input, $m)) {
            return $m[1] / $m[2];
        }

        // Format: desimal langsung
        if (is_numeric($input)) {
            return (float) $input;
        }

        return null;
    }




    public function save()
    {
        foreach ($this->production_details as $detail) {
            if ($this->parseFraction($detail['recipe_quantity'] ?? '') === null) {
                $this->alert('error', 'Format kuantitas resep tidak valid. Gunakan format pecahan seperti "1/2", atau angka desimal.');
                return;
            }
        }
        // foreach ($this->production_details as $i => $detail) {
        //     $parsed = $this->parseFraction($detail['recipe_quantity'] ?? '');
        // }
        // dd($parsed, $this->production_details);
        // dd($this->selectedProducts);

        foreach ($this->production_details as $detail) {
            $productionDetail = \App\Models\ProductionDetail::find($detail['id']);
            $parsed = $this->parseFraction($detail['recipe_quantity'] ?? '');
            if ($productionDetail) {
                // Hitung quantity baru yang ditambahkan
                $quantityToAdd = $detail['quantity'];
                $productComposition = \App\Models\ProductComposition::where('product_id', $productionDetail->product_id)
                    ->first();
                $materialBatches = \App\Models\MaterialBatch::where('material_id', $productComposition->material_id)
                    ->where('unit_id', $productComposition->unit_id)
                    ->orderBy('date')
                    ->where('date', '>=', now()->format('Y-m-d'))
                    ->get();
                $batchQty = $materialBatches->sum('batch_quantity');
                $requiredQuantity = $parsed * $productComposition->material_quantity;
                if ($batchQty < $requiredQuantity) {
                    $this->alert('error', 'Jumlah bahan baku produk ' . $productionDetail->product->name . ' tidak cukup untuk produksi ini.');
                    return;
                }

                $remaining = $requiredQuantity;

                foreach ($materialBatches as $batch) {
                    if ($remaining <= 0) break;

                    if ($batch->batch_quantity >= $remaining) {
                        // Batch ini cukup, kurangi langsung
                        $batch->batch_quantity -= $remaining;
                        $batch->save();
                        $remaining = 0;
                    } else {
                        // Batch ini tidak cukup, habiskan batch ini dan lanjut
                        $remaining -= $batch->batch_quantity;
                        $batch->batch_quantity = 0;
                        $batch->save();
                    }
                }
                // Update detail belanja
                $updatedQuantityActual = $detail['quantity_get'] + $quantityToAdd;
                $productionDetail->update([
                    'quantity_get' => $updatedQuantityActual - $detail['quantity_fail'],
                    'quantity_fail' => $detail['quantity_fail'] + $detail['quantity_fail_raw'],
                ]);

                if ($productionDetail->production->method == 'siap-beli') {
                    $productComposition->product->update([
                        'stock' => $productComposition->product->stock + ($quantityToAdd - $detail['quantity_fail']),
                    ]);
                }
            }
        }
        if (!empty($this->selectedProducts)) {
            \App\Models\ProductionDetail::whereIn('id', $this->selectedProducts)
                ->where('production_id', $this->production_id)
                ->increment('cycle');
        }

        return redirect()->route('produksi.rincian', ['id' => $this->production_id])
            ->with('success', 'Produksi berhasil diperbarui.');
    }
    public function markAllReceived()
    {
        foreach ($this->production_details as $index => $detail) {
            $productionDetail = \App\Models\ProductionDetail::find($detail['id']);

            if ($productionDetail) {
                if ($productionDetail->quantity_plan > $productionDetail->quantity_get) {
                    $this->production_details[$index]['quantity'] = $productionDetail->quantity_plan - $productionDetail->quantity_get;
                } else {
                    $this->production_details[$index]['quantity'] = 0;
                }
            }
        }

        // Panggil fungsi simpan biasa
        $this->save();
    }

    public function render()
    {
        return view('livewire.production.mulai');
    }
}
