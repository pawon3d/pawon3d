<?php

namespace App\Livewire\Production;

use App\Models\Product;
use App\Models\Production;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

class Rincian extends Component
{
    use \Jantinnerezo\LivewireAlert\LivewireAlert;

    public $production_id;

    public $production;

    public $production_details;

    public $showHistoryModal = false;

    public $showNoteModal = false;

    public $activityLogs = [];

    public $total_quantity_plan;

    public $total_quantity_get;

    public $percentage;

    public $is_start = false;

    public $is_finish = false;

    public $status;

    public $end_date;

    public $date;

    public $noteInput = '';

    protected $listeners = [
        'confirmDelete' => 'confirmDelete',
        'delete' => 'delete',
        'start' => 'start',
        'finish' => 'finish',
    ];

    public function mount($id)
    {
        $this->production_id = $id;
        $this->production = \App\Models\Production::with(['details', 'workers'])
            ->findOrFail($this->production_id);
        $this->is_start = $this->production->is_start;
        $this->is_finish = $this->production->is_finish;
        $this->status = $this->production->status;
        $this->end_date = $this->production->end_date;
        $this->date = $this->production->date;
        $this->total_quantity_plan = $this->production->details->sum('quantity_plan');
        $this->total_quantity_get = $this->production->details->sum('quantity_get');
        $this->percentage = $this->total_quantity_plan > 0 ? ($this->total_quantity_get / $this->total_quantity_plan) * 100 : 0;
        if ($this->percentage > 100) {
            $this->percentage = 100;
        }
        $this->production_details = $this->production->details;
        View::share('title', 'Rincian Produksi');
        View::share('mainTitle', 'Produksi');

        if (session()->has('success')) {
            $this->alert('success', session('success'));
        }
    }

    public function riwayatPembaruan()
    {
        $this->activityLogs = Activity::inLog('productions')->where('subject_id', $this->production_id)
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function buatCatatan()
    {
        $this->noteInput = $this->production->note ?? '';
        $this->showNoteModal = true;
    }

    public function simpanCatatan()
    {
        $this->validate([
            'noteInput' => 'nullable|string|max:1000',
        ]);

        $production = \App\Models\Production::findOrFail($this->production_id);
        $production->update(['note' => $this->noteInput]);

        // Refresh production data
        $this->production = $production;

        $this->showNoteModal = false;
        $this->alert('success', 'Catatan produksi berhasil disimpan!');
    }

    public function cetakInformasi()
    {
        return redirect()->route('rincian-produksi.pdf', [
            'id' => $this->production_id,
        ]);
    }

    public function confirmDelete()
    {
        // Konfirmasi menggunakan Livewire Alert
        $this->alert('warning', 'Apakah Anda yakin ingin menghapus produksi ini?', [
            'showConfirmButton' => true,
            'showCancelButton' => true,
            'confirmButtonText' => 'Ya, hapus',
            'cancelButtonText' => 'Batal',
            'onConfirmed' => 'delete',
            'onCancelled' => 'cancelled',
            'toast' => false,
            'position' => 'center',
            'timer' => null,
        ]);
    }

    public function delete()
    {

        $production = \App\Models\Production::findOrFail($this->production_id);
        if ($production) {
            $production->delete();

            return redirect()->intended(route('produksi'))->with('success', 'Produksi berhasil dihapus!');
        } else {
            $this->alert('error', 'Produksi tidak ditemukan!');
        }
    }

    public function start()
    {
        $this->is_start = true;
        $this->status = 'Sedang Diproses';
        $this->date = now()->format('Y-m-d H:i');
        $production = \App\Models\Production::findOrFail($this->production_id);
        $production->update(['is_start' => $this->is_start, 'status' => $this->status, 'date' => $this->date]);
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
        $this->alert('success', 'Produksi berhasil dimulai.');
    }

    public function finish()
    {
        $this->is_finish = true;
        $this->status = 'Selesai';
        $this->end_date = now()->format('Y-m-d H:i');
        $production = \App\Models\Production::findOrFail($this->production_id);
        $production->update([
            'is_finish' => $this->is_finish,
            'status' => 'Selesai',
            'end_date' => $this->end_date,
        ]);
        if ($production->method != 'siap-beli') {
            if ($production->details->sum('quantity_get') >= $production->details->sum('quantity_plan')) {
                $production->transaction->update(['status' => 'Dapat Diambil']);
            }
            $this->handleExcessProduction($production);
        }

        $this->alert('success', 'Produksi berhasil diselesaikan.');
    }

    private function handleExcessProduction(Production $production)
    {
        DB::transaction(function () use ($production) {
            foreach ($production->details as $detail) {
                // Hitung kelebihan produksi
                $excess = $detail->quantity_get - $detail->quantity_plan;

                if ($excess > 0) {
                    $product = Product::lockForUpdate()->find($detail->product_id);

                    // Cek apakah produk memiliki metode "siap-beli"
                    if ($product && in_array('siap-beli', $product->method)) {
                        // Tambahkan kelebihan ke stok
                        $product->stock += $excess;
                        $product->save();

                        // Opsional: Log/track penambahan stok
                        Log::info('Excess production added to stock', [
                            'production_id' => $production->id,
                            'product_id' => $product->id,
                            'excess_quantity' => $excess,
                        ]);
                    }
                }
            }
        });
    }

    public function render()
    {
        return view('livewire.production.rincian');
    }
}
