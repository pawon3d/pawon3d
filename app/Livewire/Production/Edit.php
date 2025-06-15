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
    public $start_date = 'dd/mm/yyyy', $note, $time;
    public $method = 'pesanan-reguler';
    public $current_stock_total = 0, $suggested_amount_total = 0, $quantity_plan_total = 0;

    public function mount($id)
    {
        $this->production_id = $id;
        $production = \App\Models\Production::findOrFail($this->production_id);
        $this->production = $production;
        $this->start_date = \Carbon\Carbon::parse($production->start_date)->format('d/m/Y');
        $this->method = $production->method;
        $this->time = $production->time ? \Carbon\Carbon::parse($production->time)->format('H:i') : '00:00';
        $this->note = $production->note;

        $this->user_ids = $production->workers()->pluck('user_id')->toArray();


        $this->production_details = $production->details->map(function ($detail) {
            $product = \App\Models\Product::find($detail->product_id);

            if ($product) {
                // Update stok saat ini
                $stock = $product->stock;

                // Hitung suggested_amount
                $yesterday = now()->subDay()->toDateString();
                $weekAgo = now()->subDays(6)->startOfDay(); // 7 hari termasuk hari ini
                $todayEnd = now()->endOfDay();

                // Cek penjualan kemarin
                $salesYesterday = \App\Models\TransactionDetail::where('product_id', $detail->product_id)
                    ->whereHas('transaction', function ($query) use ($yesterday) {
                        $query->whereDate('start_date', $yesterday);
                    })
                    ->sum('quantity');

                if ($salesYesterday > 0) {
                    $suggested = $salesYesterday;
                } else {
                    $weeklySales = \App\Models\TransactionDetail::where('product_id', $detail->product_id)
                        ->whereHas('transaction', function ($query) use ($weekAgo, $todayEnd) {
                            $query->whereBetween('start_date', [$weekAgo, $todayEnd]);
                        })
                        ->sum('quantity');

                    $suggested = ceil($weeklySales / 7);
                }

                $suggested_amount = $suggested ?: 0;
            };
            return [
                'product_id' => $detail->product_id,
                'current_stock' => $stock,
                'suggested_amount' => $suggested_amount,
                'quantity_plan' => $detail->quantity_plan,
            ];
        })->toArray();
    }

    public function addProduct()
    {
        $this->production_details[] = [
            'product_id' => '',
            'current_stock' => 0,
            'suggested_amount' => 0,
            'quantity_plan' => 0,
        ];
    }
    public function removeProduct($index)
    {
        unset($this->production_details[$index]);
        $this->production_details = array_values($this->production_details);
    }
    public function setProduct($index)
    {
        $productId = $this->production_details[$index]['product_id'] ?? null;

        if ($productId) {
            $product = \App\Models\Product::find($productId);

            if ($product) {
                // Update stok saat ini
                $this->production_details[$index]['current_stock'] = $product->stock;

                // Hitung suggested_amount
                $yesterday = now()->subDay()->toDateString();
                $weekAgo = now()->subDays(6)->startOfDay(); // 7 hari termasuk hari ini
                $todayEnd = now()->endOfDay();

                // Cek penjualan kemarin
                $salesYesterday = \App\Models\TransactionDetail::where('product_id', $productId)
                    ->whereHas('transaction', function ($query) use ($yesterday) {
                        $query->whereDate('start_date', $yesterday);
                    })
                    ->sum('quantity');

                if ($salesYesterday > 0) {
                    $suggested = $salesYesterday;
                } else {
                    $weeklySales = \App\Models\TransactionDetail::where('product_id', $productId)
                        ->whereHas('transaction', function ($query) use ($weekAgo, $todayEnd) {
                            $query->whereBetween('start_date', [$weekAgo, $todayEnd]);
                        })
                        ->sum('quantity');

                    $suggested = ceil($weeklySales / 7);
                }

                $this->production_details[$index]['suggested_amount'] = $suggested ?: 0;
            }
        }


        $this->calculateTotals();
    }

    public function updatedProductionDetails()
    {
        // foreach ($this->production_details as $index => $detail) {
        //     if (isset($detail['product_id'])) {
        //         $product = \App\Models\Product::find($detail['product_id']);
        //         if ($product) {
        //             $this->production_details[$index]['current_stock'] = $product->stock;
        //         }
        //     }
        // }
        $this->calculateTotals();
    }
    public function calculateTotals()
    {
        $this->current_stock_total = 0;
        $this->suggested_amount_total = 0;
        $this->quantity_plan_total = 0;

        foreach ($this->production_details as $detail) {
            $this->current_stock_total += $detail['current_stock'];
            $this->suggested_amount_total += $detail['suggested_amount'];
            $this->quantity_plan_total += $detail['quantity_plan'];
        }
    }

    public function update()
    {
        $this->validate([
            'user_ids' => 'required|array',
            'start_date' => $this->start_date != 'dd/mm/yyyy' ? 'nullable|date_format:d/m/Y' : 'nullable',
            'note' => 'nullable|string|max:255',
        ]);

        $produkGagal = [];

        if (empty($this->production_details) || $this->production_details[0]['product_id'] == '') {
            $this->alert('error', 'Belum ada produk yang ditambahkan.');
            return;
        }
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
            'start_date' => $this->start_date != 'dd/mm/yyyy' ? \Carbon\Carbon::createFromFormat('d/m/Y', $this->start_date)->format('Y-m-d') : null,
            'time' => $this->time != '00:00' ? \Carbon\Carbon::createFromFormat('H:i', $this->time)->format('H:i') : null,
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
