<?php

namespace App\Livewire\Production;

use App\Models\Product;
use App\Services\NotificationService;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class TambahSiapBeli extends Component
{
    use LivewireAlert;

    public $production_id = null;

    public $isEditMode = false;

    public $production_details = [];

    public $start_date = '';

    public $time = '';

    public $note;

    public $current_stock_total = 0;

    public $suggested_amount_total = 0;

    public $quantity_plan_total = 0;

    public function mount($id = null)
    {
        $this->production_id = $id;
        $this->isEditMode = ! is_null($id);

        if ($this->isEditMode) {
            View::share('title', 'Ubah Produksi Siap Saji');
            View::share('mainTitle', 'Produksi');

            $production = \App\Models\Production::with('details')->findOrFail($id);

            $this->start_date = \Carbon\Carbon::parse($production->start_date)->format('d/m/Y');
            $this->time = $production->time;
            $this->note = $production->note;

            $this->production_details = [];
            foreach ($production->details as $detail) {
                $product = Product::find($detail->product_id);
                $this->production_details[] = [
                    'id' => $detail->id,
                    'product_id' => $detail->product_id,
                    'current_stock' => $product ? $product->stock : 0,
                    'suggested_amount' => 0,
                    'quantity_plan' => $detail->quantity_plan,
                ];
            }

            // Recalculate suggested amounts
            foreach ($this->production_details as $index => $detail) {
                $this->setProduct($index);
            }
        } else {
            View::share('title', 'Tambah Produksi Siap Saji');
            View::share('mainTitle', 'Produksi');

            // Set default date and time only if empty
            if (empty($this->start_date)) {
                $this->start_date = now()->format('d/m/Y');
            }
            if (empty($this->time)) {
                $this->time = now()->format('H:i');
            }

            $this->production_details = [[
                'product_id' => '',
                'current_stock' => 0,
                'suggested_amount' => 0,
                'quantity_plan' => 0,
            ]];
        }
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
        $this->calculateTotals();
    }

    public function setProduct($index)
    {
        $productId = $this->production_details[$index]['product_id'] ?? null;

        if ($productId) {
            $product = Product::find($productId);

            if ($product) {
                // Update stok saat ini
                $this->production_details[$index]['current_stock'] = $product->stock;

                // Hitung suggested_amount berdasarkan penjualan
                $yesterday = now()->subDay()->toDateString();
                $weekAgo = now()->subDays(6)->startOfDay();
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
        $this->validate([
            'start_date' => 'required|date_format:d/m/Y',
            'time' => 'required',
            'note' => 'nullable|string|max:255',
            'production_details.*.product_id' => 'required',
            'production_details.*.quantity_plan' => 'required|numeric|min:1',
        ], [
            'start_date.required' => 'Tanggal produksi harus diisi.',
            'time.required' => 'Jam produksi harus diisi.',
            'production_details.*.product_id.required' => 'Pilih produk untuk produksi.',
            'production_details.*.quantity_plan.required' => 'Jumlah produksi harus diisi.',
            'production_details.*.quantity_plan.min' => 'Jumlah produksi minimal 1.',
        ]);

        if (empty($this->production_details) || $this->production_details[0]['product_id'] == '') {
            $this->alert('error', 'Belum ada produk yang ditambahkan.');

            return;
        }

        if ($this->isEditMode) {
            $production = \App\Models\Production::findOrFail($this->production_id);
            $production->update([
                'start_date' => \Carbon\Carbon::createFromFormat('d/m/Y', $this->start_date)->format('Y-m-d'),
                'time' => $this->time,
                'note' => $this->note,
            ]);

            // Delete existing details
            $production->details()->delete();

            // Create new details
            foreach ($this->production_details as $detail) {
                $production->details()->create([
                    'product_id' => $detail['product_id'],
                    'quantity_plan' => $detail['quantity_plan'],
                ]);
            }

            $this->alert('success', 'Rencana produksi berhasil diperbarui!');
        } else {
            $production = \App\Models\Production::create([
                'start_date' => \Carbon\Carbon::createFromFormat('d/m/Y', $this->start_date)->format('Y-m-d'),
                'time' => $this->time,
                'note' => $this->note,
                'method' => 'siap-beli',
                'status' => 'Belum Diproses',
            ]);

            foreach ($this->production_details as $detail) {
                $production->details()->create([
                    'product_id' => $detail['product_id'],
                    'quantity_plan' => $detail['quantity_plan'],
                ]);
            }

            // Kirim notifikasi produksi direncanakan
            NotificationService::productionPlanned($production->production_number);

            $this->alert('success', 'Rencana produksi berhasil ditambahkan!');
        }

        return redirect()->route('produksi.antrian-produksi');
    }

    public function render()
    {
        return view('livewire.production.tambah-siap-beli', [
            'products' => Product::whereJsonContains('method', 'siap-beli')->get(),
        ]);
    }
}
