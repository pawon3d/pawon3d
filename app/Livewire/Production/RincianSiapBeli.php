<?php

namespace App\Livewire\Production;

use App\Models\Production;
use App\Models\ProductionWorker;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class RincianSiapBeli extends Component
{
    use LivewireAlert;

    public $production_id;

    public $production;

    public $production_details;

    public $showNoteModal = false;

    public $total_quantity_plan;

    public $total_quantity_get;

    public $total_selisih;

    public $total_pcs_lebih_rusak;

    public $total_pcs_gagal;

    public $total_pcs_lebih;

    public $percentage;

    public $status;

    public $noteInput = '';

    protected $listeners = [
        'confirmDelete' => 'confirmDelete',
        'delete' => 'delete',
        'start' => 'start',
    ];

    public function mount($id)
    {
        $this->production_id = $id;
        $this->production = Production::with(['details.product'])
            ->where('method', 'siap-beli')
            ->findOrFail($this->production_id);

        $this->status = $this->production->status;
        $this->production_details = $this->production->details;

        // Hitung totals
        $this->calculateTotals();

        View::share('title', 'Rincian Produksi Siap Saji');
        View::share('mainTitle', 'Produksi');

        if (session()->has('success')) {
            $this->alert('success', session('success'));
        }
    }

    public function calculateTotals()
    {
        $this->total_quantity_plan = $this->production->details->sum('quantity_plan');
        $this->total_quantity_get = $this->production->details->sum('quantity_get');
        $this->total_selisih = $this->total_quantity_get - $this->total_quantity_plan;

        // Hitung pcs gagal dari kolom quantity_fail
        $this->total_pcs_gagal = $this->production->details->sum('quantity_fail');

        // Hitung pcs lebih: jika quantity_get > quantity_plan
        $this->total_pcs_lebih = $this->production->details->sum(function ($detail) {
            return max(0, $detail->quantity_get - $detail->quantity_plan);
        });

        // Total cycle (berapa kali produksi ulang)
        $this->total_pcs_lebih_rusak = $this->production->details->sum('cycle');

        $this->percentage = $this->total_quantity_plan > 0
            ? ($this->total_quantity_get / $this->total_quantity_plan) * 100
            : 0;

        if ($this->percentage > 100) {
            $this->percentage = 100;
        }
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

        $production = Production::findOrFail($this->production_id);
        $production->update(['note' => $this->noteInput]);

        // Refresh production data
        $this->production = $production;

        $this->showNoteModal = false;
        $this->alert('success', 'Catatan berhasil disimpan!');
    }

    public function riwayatPembaruan()
    {
        $this->alert('info', 'Fitur riwayat pembaruan akan segera tersedia');
    }

    public function selesaikanProduksi()
    {
        $production = Production::findOrFail($this->production_id);
        $production->update([
            'status' => 'Selesai',
            'end_date' => now()->format('Y-m-d H:i'),
            'is_finish' => true,
        ]);

        // Kirim notifikasi produksi selesai
        NotificationService::productionCompleted($production->production_number);

        $this->alert('success', 'Produksi berhasil diselesaikan!');

        return redirect()->route('produksi', ['method' => 'siap-beli'])->with('success', 'Produksi berhasil diselesaikan!');
    }

    public function dapatkanProduk()
    {
        return redirect()->route('produksi.mulai-siap-beli', ['id' => $this->production_id]);
    }

    public function confirmDelete()
    {
        $this->alert('warning', 'Apakah Anda yakin ingin menghapus rencana produksi ini?', [
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
        $production = Production::findOrFail($this->production_id);
        if ($production) {
            $productionNumber = $production->production_number;
            $production->delete();

            // Kirim notifikasi produksi dibatalkan
            NotificationService::productionCancelled($productionNumber);

            return redirect()->route('produksi.antrian-produksi')->with('success', 'Rencana produksi berhasil dihapus!');
        } else {
            $this->alert('error', 'Produksi tidak ditemukan!');
        }
    }

    public function start()
    {
        // Cek kecukupan bahan baku sebelum memulai produksi
        $produkGagal = [];
        $bahanKurang = [];

        foreach ($this->production_details as $detail) {
            $product = \App\Models\Product::find($detail->product_id);
            $quantityPlan = $detail->quantity_plan;
            $kurang = false;

            foreach ($product->product_compositions as $composition) {
                // Gunakan helper method Material untuk cek stok dengan konversi otomatis
                $material = \App\Models\Material::find($composition->material_id);
                $compositionUnit = \App\Models\Unit::find($composition->unit_id);

                if (! $material || ! $compositionUnit) {
                    $kurang = true;
                    $bahanKurang[] = [
                        'product' => $product->name,
                        'material' => $material ? $material->name : 'Unknown',
                        'required' => 0,
                        'available' => 0,
                        'unit' => $compositionUnit ? $compositionUnit->name : 'Unknown',
                    ];
                    break;
                }

                $requiredQuantity = $quantityPlan / $composition->product->pcs * $composition->material_quantity;
                $availableQuantity = $material->getTotalQuantityInUnit($compositionUnit);

                if ($availableQuantity < $requiredQuantity) {
                    $kurang = true;
                    $bahanKurang[] = [
                        'product' => $product->name,
                        'material' => $material->name,
                        'required' => $requiredQuantity,
                        'available' => $availableQuantity,
                        'unit' => $compositionUnit->name,
                    ];
                    break;
                }
            }

            if ($kurang) {
                $produkGagal[] = $product->name;
            }
        }

        if (! empty($produkGagal)) {
            $errorMessage = 'Bahan baku tidak cukup:<br><br>';
            foreach ($bahanKurang as $item) {
                $errorMessage .= sprintf(
                    '• <b>%s</b> membutuhkan <b>%s</b>: %.2f %s (tersedia: %.2f %s)<br>',
                    $item['product'],
                    $item['material'],
                    $item['required'],
                    $item['unit'],
                    $item['available'],
                    $item['unit']
                );
            }
            $this->alert('error', $errorMessage, ['html' => true]);

            return;
        }

        $production = Production::findOrFail($this->production_id);
        $production->update([
            'status' => 'Sedang Diproses',
            'date' => now()->format('Y-m-d H:i'),
        ]);

        ProductionWorker::create([
            'production_id' => $this->production_id,
            'user_id' => Auth::user()->id,
        ]);

        // Kirim notifikasi produksi diproses
        NotificationService::productionProcessing($production->production_number);

        $this->status = 'Sedang Diproses';
        $this->production = $production;

        $this->alert('success', 'Produksi berhasil dimulai!');
    }

    public function ubahProduksi()
    {
        return redirect()->route('produksi.edit-siap-beli', ['id' => $this->production_id]);
    }

    public function render()
    {
        return view('livewire.production.rincian-siap-beli');
    }
}