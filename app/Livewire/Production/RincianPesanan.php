<?php

namespace App\Livewire\Production;

use Illuminate\Support\Facades\View;
use Livewire\Component;

class RincianPesanan extends Component
{
    public $transactionId;
    public $transaction;
    public $details = [];
    public $activityLogs = [];
    public $showHistoryModal = false;

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
        return redirect()->route('produksi.tambah-produksi-pesanan', [
            'id' => $this->transactionId,
        ]);
    }
    public function render()
    {
        return view('livewire.production.rincian-pesanan');
    }
}
