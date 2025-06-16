<?php

namespace App\Livewire\Production;

use App\Models\ProductionWorker;
use Illuminate\Support\Facades\View;
use Livewire\Component;

class TambahProduksiPesanan extends Component
{
    public $transactionId;
    public $transaction;
    public $method;
    public $details = [];
    public $user_ids;
    public $start_date = 'dd/mm/yyyy', $note, $time;

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

        // Hitung start_date
        $transactionDate = \Carbon\Carbon::parse($this->transaction->date);
        $today = now()->startOfDay();

        $diffInDays = $transactionDate->diffInDays($today, false); // false untuk nilai negatif kalau hari ini > transaction date

        if ($diffInDays >= 3) {
            $this->start_date = $transactionDate->copy()->subDays(3)->toDateString();
        } elseif ($diffInDays === 2) {
            $this->start_date = $transactionDate->copy()->subDays(2)->toDateString();
        } else {
            // fallback ke 1 hari sebelum
            $this->start_date = $transactionDate->copy()->subDays(1)->toDateString();
        }
        $this->start_date = \Carbon\Carbon::parse($this->start_date)->format('d/m/Y');

        View::share('title', 'Rencana Produksi ' . $this->method);
    }

    public function start()
    {
        $this->validate([
            'user_ids' => 'required|array',
            'start_date' => $this->start_date != 'dd/mm/yyyy' ? 'nullable|date_format:d/m/Y' : 'nullable',
            'note' => 'nullable|string|max:255',
        ]);

        $produkGagal = [];

        foreach ($this->details as $detail) {
            $product = \App\Models\Product::find($detail->product_id);
            $quantityPlan = $detail->quantity;
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
            'method' => $this->transaction->method,
            'status' => 'Sedang Diproses',
            'is_start' => true,
            'date' => now(),
            'time' => $this->time,
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
