<?php

namespace App\Livewire\Production;

use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class RincianPesanan extends Component
{
    use LivewireAlert;

    public $transactionId;

    public $transaction;

    public $details = [];

    public $activityLogs = [];

    public $showHistoryModal = false;

    public $note;

    public function mount($id)
    {
        View::share('title', 'Rincian Pesanan');
        View::share('mainTitle', 'Produksi');
        $this->transactionId = $id;
        $this->transaction = \App\Models\Transaction::with(['details.product', 'user'])
            ->findOrFail($this->transactionId);
        $this->details = $this->transaction->details;
        $this->activityLogs = \Spatie\Activitylog\Models\Activity::inLog('transactions')
            ->where('subject_id', $this->transactionId)
            ->latest()
            ->limit(50)
            ->get();
    }

    public function riwayatPembaruan()
    {
        $this->showHistoryModal = true;
    }

    public function cetakInformasi()
    {
        return redirect()->route('produksi.pdf', [
            'transaction_id' => $this->transactionId,
        ]);
    }

    public function start()
    {
        // $produkGagal = [];
        // $bahanKurang = [];

        // foreach ($this->details as $detail) {
        //     $product = \App\Models\Product::find($detail->product_id);
        //     $quantityPlan = $detail->quantity;
        //     $kurang = false;

        //     foreach ($product->product_compositions as $composition) {
        //         // Gunakan helper method Material untuk cek stok dengan konversi otomatis
        //         $material = \App\Models\Material::find($composition->material_id);
        //         $compositionUnit = \App\Models\Unit::find($composition->unit_id);

        //         if (! $material || ! $compositionUnit) {
        //             $kurang = true;
        //             $bahanKurang[] = [
        //                 'product' => $product->name,
        //                 'material' => $material ? $material->name : 'Unknown',
        //                 'required' => 0,
        //                 'available' => 0,
        //                 'unit' => $compositionUnit ? $compositionUnit->name : 'Unknown',
        //             ];
        //             break;
        //         }

        //         $requiredQuantity = $quantityPlan / $composition->product->pcs * $composition->material_quantity;
        //         $availableQuantity = $material->getTotalQuantityInUnit($compositionUnit);

        //         if ($availableQuantity < $requiredQuantity) {
        //             $kurang = true;
        //             $bahanKurang[] = [
        //                 'product' => $product->name,
        //                 'material' => $material->name,
        //                 'required' => $requiredQuantity,
        //                 'available' => $availableQuantity,
        //                 'unit' => $compositionUnit->name,
        //             ];
        //             break;
        //         }
        //     }

        //     if ($kurang) {
        //         $produkGagal[] = $product->name;
        //     }
        // }

        // if (! empty($produkGagal)) {
        //     $errorMessage = 'Bahan baku tidak cukup:<br><br>';
        //     foreach ($bahanKurang as $item) {
        //         $errorMessage .= sprintf(
        //             '• <b>%s</b> membutuhkan <b>%s</b>: %.2f %s (tersedia: %.2f %s)<br>',
        //             $item['product'],
        //             $item['material'],
        //             $item['required'],
        //             $item['unit'],
        //             $item['available'],
        //             $item['unit']
        //         );
        //     }
        //     $this->alert('error', $errorMessage, ['html' => true]);

        //     return;
        // }

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

        \App\Models\ProductionWorker::create([
            'production_id' => $production->id,
            'user_id' => Auth::user()->id,
        ]);

        foreach ($this->details as $detail) {
            $production->details()->create([
                'product_id' => $detail->product_id,
                'quantity_plan' => $detail->quantity,
            ]);
        }

        // Notifikasi: pesanan masuk ke produksi
        NotificationService::orderInProduction($this->transaction->invoice_number);

        session()->flash('success', 'Produksi berhasil dimulai.');

        return redirect()->route('produksi.rincian', ['id' => $production->id]);
    }

    public function render()
    {
        return view('livewire.production.rincian-pesanan');
    }
}
