<?php

namespace App\Livewire\Hitung;

use Illuminate\Support\Facades\View;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

class Rincian extends Component
{
    use \Jantinnerezo\LivewireAlert\LivewireAlert;
    public $hitung_id;
    public $hitung;
    public $hitungDetails;
    public $showHistoryModal = false;
    public $activityLogs = [];
    public $is_start = false, $is_finish = false, $status, $finish_date;

    protected $listeners = [
        'delete',
    ];

    public function mount($id)
    {
        $this->hitung_id = $id;
        $this->hitung = \App\Models\Hitung::with(['details', 'details.material', 'details.unit'])
            ->findOrFail($this->hitung_id);
        $this->is_start = $this->hitung->is_start;
        $this->is_finish = $this->hitung->is_finish;
        $this->status = $this->hitung->status;
        $this->finish_date = $this->hitung->hitung_date_finish;
        $this->hitungDetails = $this->hitung->details;
        View::share('title', 'Rincian ' . $this->hitung->action);
        View::share('mainTitle', 'Inventori');


        if (session()->has('success')) {
            $this->alert('success', session('success'));
        }
    }

    public function riwayatPembaruan()
    {
        $this->activityLogs = Activity::inLog('hitungs')->where('subject_id', $this->hitung_id)
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function cetakInformasi()
    {
        return redirect()->route('rincian-hitung.pdf', [
            'id' => $this->hitung_id,
        ]);
    }

    public function confirmDelete()
    {
        // Konfirmasi menggunakan Livewire Alert
        $this->alert('warning', 'Apakah Anda yakin ingin menghapus aksi ini?', [
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

        $hitung = \App\Models\Hitung::findOrFail($this->hitung_id);
        if ($hitung) {
            $hitung->delete();
            return redirect()->intended(route('hitung'))->with('success', 'Aksi berhasil dihapus!');
        } else {
            $this->alert('error', 'Aksi tidak ditemukan!');
        }
    }

    public function start()
    {
        $this->is_start = true;
        $this->status = 'Sedang Diproses';
        $hitung = \App\Models\Hitung::findOrFail($this->hitung_id);
        $hitung->update(['is_start' => $this->is_start, 'status' => $this->status]);
        $this->alert('success', $hitung->action . ' berhasil dimulai.');
    }
    public function finish()
    {
        $this->is_finish = true;
        $this->status = 'Selesai';
        $this->finish_date = now()->format('Y-m-d');
        $hitung = \App\Models\Hitung::findOrFail($this->hitung_id);
        $hitung->update([
            'is_finish' => $this->is_finish,
            'status' => 'Selesai',
            'hitung_finish_date' => $this->finish_date
        ]);
        // $hitung->details->each(function ($detail) {
        //     $materialDetail = \App\Models\MaterialDetail::where('material_id', $detail->material_id)
        //         ->where('unit_id', $detail->unit_id)
        //         ->first();
        //     if ($this->hitung->action == 'Hitung Persediaan') {
        //         $materialDetail->update([
        //             'supply_quantity' => $materialDetail->supply_quantity - ($detail->quantity_expect - $detail->quantity_actual),
        //         ]);
        //     } else {
        //         $materialDetail->update([
        //             'supply_quantity' => $materialDetail->supply_quantity - $detail->quantity_actual,
        //         ]);
        //     }
        // });
        $this->alert('success', $hitung->action . ' berhasil diselesaikan.');
    }

    public function render()
    {
        return view('livewire.hitung.rincian', [
            'logName' => Activity::inLog('hitungs')->where('subject_id', $this->hitung_id)->latest()->first()?->causer->name ?? '-',
        ]);
    }
}
