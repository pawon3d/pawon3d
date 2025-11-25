<?php

namespace App\Livewire\Production;

use App\Models\Production;
use App\Models\ProductionWorker;
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
            $production->delete();

            return redirect()->route('produksi.antrian-produksi')->with('success', 'Rencana produksi berhasil dihapus!');
        } else {
            $this->alert('error', 'Produksi tidak ditemukan!');
        }
    }

    public function start()
    {
        $production = Production::findOrFail($this->production_id);
        $production->update([
            'status' => 'Sedang Diproses',
            'date' => now()->format('Y-m-d H:i'),
        ]);

        ProductionWorker::create([
            'production_id' => $this->production_id,
            'user_id' => Auth::user()->id,
        ]);

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
