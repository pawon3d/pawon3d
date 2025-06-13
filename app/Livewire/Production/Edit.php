<?php

namespace App\Livewire\Production;

use App\Models\ProductionWorker;
use Livewire\Component;

class Edit extends Component
{
    use \Jantinnerezo\LivewireAlert\LivewireAlert;
    public $production_id;
    public $production_details = [];
    public $production;
    public $user_ids;
    public $start_date = 'dd-mm-yyyy', $note;
    public $method = 'pesanan-reguler';
    public $current_stock_total = 0, $suggested_amount_total = 0, $quantity_plan_total = 0;

    public function mount($id)
    {
        $this->production_id = $id;
        $production = \App\Models\Production::findOrFail($this->production_id);
        $this->production = $production;
        $this->start_date = \Carbon\Carbon::parse($production->start_date)->format('d-m-Y');
        $this->note = $production->note;

        $this->user_ids = $production->workers()->pluck('user_id')->toArray();

        $this->production_details = $production->details->map(function ($detail) {
            return [
                'product_id' => $detail->product_id,
                'current_stock' => $detail->product->stock,
                'suggested_amount' => 0,
                'quantity_plan' => $detail->quantity_plan,
            ];
        })->toArray();
    }

    public function update()
    {
        $this->validate([
            'user_ids' => 'required|array',
            'start_date' => $this->start_date != 'dd-mm-yyyy' ? 'nullable|date_format:d-m-Y' : 'nullable',
            'note' => 'nullable|string|max:255',
        ]);

        $produkGagal = [];

        foreach ($this->production_details as $detail) {
            $product = \App\Models\Product::find($detail['product_id']);
            $quantityPlan = $detail['quantity_plan'];
            $kurang = false;

            foreach ($product->product_compositions as $composition) {
                $materialDetail = \App\Models\MaterialDetail::where('material_id', $composition->material_id)->first();
                $requiredQuantity = $quantityPlan / $composition->product->pcs * $composition->material_quantity;

                if (!$materialDetail || $materialDetail->supply_quantity < $requiredQuantity) {
                    $kurang = true;
                    break;
                }
            }

            if ($kurang) {
                $produkGagal[] = $product->name;
            }
        }

        if (!empty($produkGagal)) {
            $this->alert('error', 'Bahan baku tidak cukup untuk: ' . implode(', ', $produkGagal));
            return;
        }

        $production = \App\Models\Production::findOrFail($this->production_id);
        $production->update([
            'start_date' => \Carbon\Carbon::createFromFormat('d-m-Y', $this->start_date)->format('Y-m-d'),
            'note' => $this->note,
            'status' => $production->status ? $production->status : 'Draft',
        ]);



        $production->workers()->delete();
        $production->details()->delete();

        foreach ($this->user_ids as $user_id) {
            ProductionWorker::create([
                'production_id' => $production->id,
                'user_id' => $user_id,
            ]);
        }

        foreach ($this->production_details as $detail) {
            $production->details()->create([
                'product_id' => $detail['product_id'],
                'quantity_plan' => $detail['quantity_plan'],
            ]);
        }

        session()->flash('success', 'Produksi berhasil diubah.');
        return redirect()->route('produksi.rincian', ['id' => $this->production_id]);
    }
    public function render()
    {
        return view('livewire.production.edit', [
            'users' => \App\Models\User::lazy(),
            'products' => \App\Models\Product::where('method', $this->production->method)
                ->get(),
        ]);
    }
}
