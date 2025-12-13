<?php

namespace App\Livewire\Production;

use Illuminate\Support\Facades\Auth;
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

        // FASE 1: Validasi semua bahan cukup
        foreach ($this->production_details as $detail) {
            $productionDetail = \App\Models\ProductionDetail::find($detail['id']);
            $parsed = $this->parseFraction($detail['recipe_quantity'] ?? '');

            if ($productionDetail && $parsed > 0) {
                $productCompositions = \App\Models\ProductComposition::where('product_id', $productionDetail->product_id)
                    ->get();

                foreach ($productCompositions as $productComposition) {
                    $material = \App\Models\Material::find($productComposition->material_id);
                    $compositionUnit = \App\Models\Unit::find($productComposition->unit_id);

                    $requiredQuantity = $parsed * $productComposition->material_quantity;
                    $availableQuantity = $material->getTotalQuantityInUnit($compositionUnit);

                    if ($availableQuantity < $requiredQuantity) {
                        $this->alert('error', 'Jumlah bahan baku ' . $material->name . ' untuk produk ' . $productionDetail->product->name . ' tidak cukup untuk produksi ini. Tersedia: ' . $availableQuantity . ' ' . $compositionUnit->name . ', dibutuhkan: ' . $requiredQuantity . ' ' . $compositionUnit->name);

                        return;
                    }
                }
            }
        }

        // FASE 2: Kurangi bahan dan update produksi
        foreach ($this->production_details as $detail) {
            $productionDetail = \App\Models\ProductionDetail::find($detail['id']);
            $parsed = $this->parseFraction($detail['recipe_quantity'] ?? '');

            if ($productionDetail && $parsed > 0) {
                $quantityToAdd = $detail['quantity'];
                $productCompositions = \App\Models\ProductComposition::where('product_id', $productionDetail->product_id)
                    ->get();

                foreach ($productCompositions as $productComposition) {
                    $material = \App\Models\Material::find($productComposition->material_id);
                    $compositionUnit = \App\Models\Unit::find($productComposition->unit_id);

                    $requiredQuantity = $parsed * $productComposition->material_quantity;

                    // Kurangi stok dengan konversi otomatis (FIFO)
                    $success = $material->reduceQuantity($requiredQuantity, $compositionUnit, [
                        'user_id' => Auth::user()->id,
                        'action' => 'produksi',
                        'reference_type' => 'production',
                        'reference_id' => $productionDetail->production_id,
                        'note' => 'Produksi ' . $productionDetail->product->name,
                    ]);

                    if (! $success) {
                        $this->alert('error', 'Gagal mengurangi stok bahan baku ' . $material->name . ' untuk produk ' . $productionDetail->product->name);

                        return;
                    }
                }

                // Update detail produksi
                $updatedQuantityActual = $detail['quantity_get'] + $quantityToAdd;
                $productionDetail->update([
                    'quantity_get' => $updatedQuantityActual - $detail['quantity_fail'],
                    'quantity_fail' => $detail['quantity_fail'] + $detail['quantity_fail_raw'],
                ]);

                if ($productionDetail->production->method == 'siap-beli') {
                    $productionDetail->product->update([
                        'stock' => $productionDetail->product->stock + ($quantityToAdd - $detail['quantity_fail']),
                    ]);
                }
            }
        }

        if (! empty($this->selectedProducts)) {
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
