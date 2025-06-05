<?php

namespace App\Livewire\Production;

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
    public $activityLogs = [];
    public $total_quantity_plan, $total_quantity_get, $percentage;
    public $is_start = false, $is_finish = false, $status, $end_date;

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
        $this->total_quantity_plan = $this->production->details->sum('quantity_plan');
        $this->total_quantity_get = $this->production->details->sum('quantity_get');
        $this->percentage = $this->total_quantity_plan > 0 ? ($this->total_quantity_get / $this->total_quantity_plan) * 100 : 0;
        if ($this->percentage > 100) {
            $this->percentage = 100;
        }
        $this->production_details = $this->production->details;
        View::share('title', 'Rincian Produksi');

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
        $this->status = 'Dimulai';
        $production = \App\Models\Production::findOrFail($this->production_id);
        $production->update(['is_start' => $this->is_start, 'status' => $this->status]);
        $production->details->each(function ($detail) {
            $productComposition = \App\Models\ProductComposition::where('product_id', $detail->product_id)
                ->first();
            $materialDetail = \App\Models\MaterialDetail::where('material_id', $productComposition->material_id)
                ->where('unit_id', $productComposition->unit_id)
                ->first();
            $materialDetail->update([
                'supply_quantity' => $materialDetail->supply_quantity - ($detail->quantity_plan * $productComposition->material_quantity),
            ]);
        });
        $this->alert('success', 'Produksi berhasil dimulai.');
    }
    public function finish()
    {
        $this->is_finish = true;
        $this->status = 'Selesai';
        $this->end_date = now()->format('Y-m-d');
        $production = \App\Models\Production::findOrFail($this->production_id);
        $production->update([
            'is_finish' => $this->is_finish,
            'status' => 'Selesai',
            'end_date' => $this->end_date
        ]);

        $this->alert('success', 'Produksi berhasil diselesaikan.');
    }
    public function render()
    {
        return view('livewire.production.rincian');
    }
}
