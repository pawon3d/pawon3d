<?php

namespace App\Livewire\Production;

use App\Models\MaterialDetail;
use App\Models\Product;
use App\Models\ProductionWorker;
use Illuminate\Support\Facades\View;
use Livewire\Component;

class Tambah extends Component
{
    use \Jantinnerezo\LivewireAlert\LivewireAlert;
    public $production_details = [];
    public $user_ids;
    public $start_date = 'dd/mm/yyyy', $note, $time;
    public $method = 'pesanan-reguler';
    public $current_stock_total = 0, $suggested_amount_total = 0, $quantity_plan_total = 0;

    public function mount($method)
    {
        View::share('title', 'Tambah Produksi');

        $this->method = $method;

        $this->production_details = [[
            'product_id' => '',
            'current_stock' => 0,
            'suggested_amount' => 0,
            'quantity_plan' => 0,
        ]];
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

    // public function setProduct($index, $productId)
    // {
    //     $this->production_details[$index]['product_id'] = $productId;
    //     $this->production_details[$index]['current_stock'] = \App\Models\Product::find($productId)->stock;
    // }
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

    public function store()
    {
        $this->validate(
            [
                'user_ids' => 'required|array',
                'start_date' => $this->start_date != 'dd/mm/yyyy' ? 'nullable|date_format:d/m/Y' : 'nullable',
                'note' => 'nullable|string|max:255',
                'production_details.*' => 'required|array',
            ],
            [
                'user_ids.required' => 'Pilih minimal satu pekerja.',
                'start_date.date_format' => 'Format tanggal harus dd/mm/yyyy.',
                'production_details.*.product_id.required' => 'Pilih produk untuk produksi.',
            ],
        );

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

        $production = \App\Models\Production::create([
            'start_date' => $this->start_date != 'dd/mm/yyyy' ? \Carbon\Carbon::createFromFormat('d/m/Y', $this->start_date)->format('Y-m-d') : null,
            'time' => $this->time,
            'note' => $this->note,
            'method' => $this->method,
            'status' => 'Draft',
        ]);

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

        session()->flash('success', 'Produksi berhasil ditambahkan.');
        return redirect()->route('produksi');
    }

    public function start()
    {
        $this->validate([
            'user_ids' => 'required|array',
            'start_date' => $this->start_date != 'dd/mm/yyyy' ? 'nullable|date_format:d/m/Y' : 'nullable',
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

        $production = \App\Models\Production::create([
            'start_date' => $this->start_date != 'dd/mm/yyyy' ? \Carbon\Carbon::createFromFormat('d/m/Y', $this->start_date)->format('Y-m-d') : null,
            'note' => $this->note,
            'method' => $this->method,
            'status' => 'Sedang Diproses',
            'is_start' => true,
            'date' => now(),
        ]);

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

        // $production->details->each(function ($detail) {
        //     $productComposition = \App\Models\ProductComposition::where('product_id', $detail->product_id)
        //         ->first();
        //     $materialDetail = \App\Models\MaterialDetail::where('material_id', $productComposition->material_id)
        //         ->where('unit_id', $productComposition->unit_id)
        //         ->first();
        //     $materialDetail->update([
        //         'supply_quantity' => $materialDetail->supply_quantity - ($detail->quantity_plan / $productComposition->product->pcs * $productComposition->material_quantity),
        //     ]);
        // });

        session()->flash('success', 'Produksi berhasil dimulai.');
        return redirect()->route('produksi.rincian', ['id' => $production->id]);
    }
    public function render()
    {
        return view('livewire.production.tambah', [
            'users' => \App\Models\User::lazy(),
            'products' => \App\Models\Product::when($this->method, function ($query) {
                $query->whereJsonContains('method', $this->method);
            })
                ->get(),
        ]);
    }
}
