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

    public function mount($id)
    {
        $this->production_id = $id;
        $this->production = \App\Models\Production::with(['details'])
            ->findOrFail($this->production_id);
        $this->production_details = $this->production->details->map(function ($detail) {
            return [
                'id' => $detail->id,
                'product_name' => $detail->product->name,
                'quantity_plan' => $detail->quantity_plan,
                'quantity_get' => $detail->quantity_get,
                'cycle' => $detail->cycle,
                'quantity' => 0,
            ];
        })->toArray();
        View::share('title', 'Dapatkan Hasil Produksi');
    }

    public function riwayatPembaruan()
    {
        $this->activityLogs = Activity::inLog('productions')->where('subject_id', $this->production_id)
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function save()
    {
        $this->validate([
            'production_details.*.quantity' => 'required|numeric',
        ], [
            'production_details.*.quantity.required' => 'Jumlah yang didapatkan harus diisi.',
            'production_details.*.quantity.numeric' => 'Jumlah yang didapatkan harus berupa angka.',
            'production_details.*.quantity.min' => 'Jumlah yang didapatkan tidak boleh kurang dari 0.',
        ]);

        foreach ($this->production_details as $detail) {
            $productionDetail = \App\Models\ProductionDetail::find($detail['id']);
            if ($productionDetail) {
                // Hitung quantity baru yang ditambahkan
                $quantityToAdd = $detail['quantity'];

                // Update detail belanja
                $updatedQuantityActual = $detail['quantity_get'] + $quantityToAdd;
                $productionDetail->update([
                    'quantity_get' => $updatedQuantityActual,
                ]);

                if ($productionDetail->quantity_get < $productionDetail->quantity_plan) {
                    $productionDetail->update([
                        'quantity_fail' => $productionDetail->quantity_plan - $productionDetail->quantity_get,
                    ]);
                }

                $productComposition = \App\Models\ProductComposition::where('product_id', $productionDetail->product_id)
                    ->first();
                $materialDetail = \App\Models\MaterialDetail::where('material_id', $productComposition->material_id)
                    ->where('unit_id', $productComposition->unit_id)
                    ->first();
                if ($productionDetail->cycle < $detail['cycle']) {
                    $requiredQuantity = $productionDetail->quantity_plan * $productComposition->material_quantity;

                    if ($materialDetail->supply_quantity < $requiredQuantity) {
                        $this->alert('error', 'Jumlah bahan baku produk' . $productionDetail->product->name . 'tidak cukup untuk produksi ini.');
                        return;
                    }

                    // Bahan cukup, lanjut produksi
                    $productionDetail->update([
                        'cycle' => $detail['cycle'],
                    ]);

                    $materialDetail->update([
                        'supply_quantity' => $materialDetail->supply_quantity - $requiredQuantity,
                    ]);
                    $productComposition->product->update([
                        'stock' => $productComposition->product->stock + $quantityToAdd,
                    ]);
                }
            }
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

    public function repeat($index)
    {
        if ($this->production_details[$index]['quantity_get'] >= $this->production_details[$index]['quantity_plan']) {
            $this->alert('error', 'Jumlah yang didapatkan sudah mencapai jumlah yang diharapkan.');
            return;
        }
        $productionDetail = \App\Models\ProductionDetail::find($this->production_details[$index]['id']);
        $productComposition = \App\Models\ProductComposition::where('product_id', $productionDetail->product_id)
            ->first();
        $materialDetail = \App\Models\MaterialDetail::where('material_id', $productComposition->material_id)
            ->where('unit_id', $productComposition->unit_id)
            ->first();
        $requiredQuantity = $productionDetail->quantity_plan * $productComposition->material_quantity;
        if ($materialDetail->supply_quantity < $requiredQuantity) {
            $this->alert('error', 'Jumlah bahan baku produk ' . $productionDetail->product->name . ' tidak cukup untuk mengulang produksi.');
            return;
        }
        $this->production_details[$index]['cycle'] += 1;
    }
    public function render()
    {
        return view('livewire.production.mulai');
    }
}
