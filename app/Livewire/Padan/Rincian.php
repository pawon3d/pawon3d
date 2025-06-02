<?php

namespace App\Livewire\Padan;

use Illuminate\Support\Facades\View;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

class Rincian extends Component
{
    use \Jantinnerezo\LivewireAlert\LivewireAlert;
    public $padan_id;
    public $padan;
    public $padanDetails;
    public $showHistoryModal = false;
    public $activityLogs = [];
    public $is_start = false, $is_finish = false, $status, $finish_date;

    protected $listeners = [
        'delete',
    ];

    public function mount($id)
    {
        $this->padan_id = $id;
        $this->padan = \App\Models\Padan::with(['details', 'details.material', 'details.unit'])
            ->findOrFail($this->padan_id);
        $this->is_start = $this->padan->is_start;
        $this->is_finish = $this->padan->is_finish;
        $this->status = $this->padan->status;
        $this->finish_date = $this->padan->finish_date;
        $this->padanDetails = $this->padan->details;
        View::share('title', 'Rincian ' . $this->padan->action);

        if (session()->has('success')) {
            $this->alert('success', session('success'));
        }
    }

    public function riwayatPembaruan()
    {
        $this->activityLogs = Activity::inLog('padans')->where('subject_id', $this->padan_id)
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function cetakInformasi()
    {
        return redirect()->route('rincian-padan.pdf', [
            'id' => $this->padan_id,
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

        $padan = \App\Models\Padan::findOrFail($this->padan_id);
        if ($padan) {
            $padan->delete();
            return redirect()->intended(route('padan'))->with('success', 'Aksi berhasil dihapus!');
        } else {
            $this->alert('error', 'Aksi tidak ditemukan!');
        }
    }

    public function start()
    {
        $this->is_start = true;
        $this->status = 'Sedang Diproses';
        $padan = \App\Models\Padan::findOrFail($this->padan_id);
        $padan->update(['is_start' => $this->is_start, 'status' => $this->status]);
        $this->alert('success', $padan->action . ' berhasil dimulai.');
    }
    public function finish()
    {
        $this->is_finish = true;
        $this->status = 'Selesai';
        $this->finish_date = now()->format('Y-m-d');
        $padan = \App\Models\Padan::findOrFail($this->padan_id);
        $padan->update(['is_finish' => $this->is_finish, 'status' => 'Selesai', 'padan_finish_date' => $this->finish_date]);
        $this->alert('success', $padan->action . ' berhasil diselesaikan.');
    }

    public function render()
    {
        return view('livewire.padan.rincian', [
            'logName' => Activity::inLog('padans')->where('subject_id', $this->padan_id)->latest()->first()?->causer->name ?? '-',
        ]);
    }
}
