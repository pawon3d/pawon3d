<?php

namespace App\Livewire\Hitung;

use App\Models\Hitung;
use App\Models\HitungDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

class Mulai extends Component
{
    use \Jantinnerezo\LivewireAlert\LivewireAlert;

    public string $hitung_id = '';

    public array $hitungDetails = [];

    public array $errorInputs = [];

    public bool $showHistoryModal = false;

    public array $activityLogs = [];

    // Store hitung data as array instead of model to avoid UUID serialization issues
    public string $hitungAction = '';

    public string $hitungNumber = '';

    public function getHitungProperty(): Hitung
    {
        return Hitung::findOrFail($this->hitung_id);
    }

    public function mount($id): void
    {
        $this->hitung_id = $id;
        $hitung = Hitung::with(['details.material', 'details.materialBatch.unit'])
            ->findOrFail($this->hitung_id);

        $this->hitungAction = $hitung->action;
        $this->hitungNumber = $hitung->hitung_number;

        $this->hitungDetails = $hitung->details->map(function ($detail) {
            $batch = $detail->materialBatch;
            $selisihDidapatkan = $detail->quantity_actual - $detail->quantity_expect;
            $jumlahSebenarnya = $detail->quantity_expect - $detail->quantity_actual;

            return [
                'id' => (string) $detail->id,
                'material_name' => $detail->material->name ?? 'Barang Tidak Ditemukan',
                'batch_number' => $batch?->batch_number ?? '-',
                'quantity_expect' => (float) $detail->quantity_expect,
                'quantity_actual' => (float) $detail->quantity_actual,
                'unit_id' => (string) ($batch?->unit_id ?? ''),
                'unit_alias' => $batch?->unit->alias ?? '',
                'selisih_didapatkan' => (float) $selisihDidapatkan,
                'jumlah_sebenarnya' => (float) max(0, $jumlahSebenarnya),
                'quantity_input' => 0,
            ];
        })->toArray();

        View::share('title', $hitung->action);
        View::share('mainTitle', 'Inventori');
    }

    public function riwayatPembaruan(): void
    {
        $logs = Activity::inLog('hitungs')
            ->where('subject_id', $this->hitung_id)
            ->with('causer')
            ->latest()
            ->limit(50)
            ->get();

        $this->activityLogs = $logs->map(function ($log) {
            return [
                'description' => $log->description,
                'causer_name' => $log->causer->name ?? 'System',
                'created_at' => $log->created_at->format('d M Y H:i'),
            ];
        })->toArray();

        $this->showHistoryModal = true;
    }

    public function validateQuantities(): void
    {
        $this->errorInputs = [];

        foreach ($this->hitungDetails as $index => $detail) {
            $inputQuantity = abs($detail['quantity_input'] ?? 0);

            if ($this->hitungAction === 'Hitung Persediaan') {
                // Untuk Hitung Persediaan:
                // quantity_input = jumlah yang dihitung
                // Tidak ada batasan maksimal karena bisa lebih dari expected
                continue;
            }

            // Untuk Rusak/Hilang: tidak boleh melebihi sisa yang tersedia
            $sisaTersedia = $detail['quantity_expect'] - $detail['quantity_actual'];
            if ($inputQuantity > $sisaTersedia && $sisaTersedia >= 0) {
                $this->errorInputs[$index] = 'Nilai tidak boleh melebihi sisa yang tersedia ('.$sisaTersedia.' '.$detail['unit_alias'].')';
            }
        }
    }

    public function updatedHitungDetails($value, $key): void
    {
        $this->validateQuantities();
        $this->calculateDerived();
    }

    /**
     * Hitung ulang nilai turunan (selisih/jumlah sebenarnya) berdasarkan input
     */
    protected function calculateDerived(): void
    {
        foreach ($this->hitungDetails as $index => $detail) {
            $inputQuantity = $detail['quantity_input'] ?? 0;
            $quantityExpect = $detail['quantity_expect'];
            $quantityActual = $detail['quantity_actual'];

            if ($this->hitungAction === 'Hitung Persediaan') {
                // Selisih = (sudah terhitung + input baru) - expected
                $totalTerhitung = $quantityActual + $inputQuantity;
                $this->hitungDetails[$index]['selisih_didapatkan'] = $totalTerhitung - $quantityExpect;
            } else {
                // Jumlah sebenarnya = expected - (sudah rusak/hilang + input baru)
                $totalRusakHilang = $quantityActual + $inputQuantity;
                $this->hitungDetails[$index]['jumlah_sebenarnya'] = max(0, $quantityExpect - $totalRusakHilang);
            }
        }
    }

    public function save()
    {
        $this->validate([
            'hitungDetails.*.quantity_input' => 'required|numeric|min:0',
        ], [
            'hitungDetails.*.quantity_input.required' => 'Jumlah harus diisi.',
            'hitungDetails.*.quantity_input.numeric' => 'Jumlah harus berupa angka.',
            'hitungDetails.*.quantity_input.min' => 'Jumlah tidak boleh kurang dari 0.',
        ]);

        $this->validateQuantities();

        if (count($this->errorInputs) > 0) {
            $this->alert('error', 'Masih ada input yang melebihi jumlah yang tersedia.');

            return;
        }

        foreach ($this->hitungDetails as $detail) {
            $hitungDetail = HitungDetail::find($detail['id']);
            if (! $hitungDetail) {
                continue;
            }

            $inputQuantity = $detail['quantity_input'] ?? 0;
            if ($inputQuantity <= 0) {
                continue;
            }

            // Update quantity_actual pada hitung detail
            $newQuantityActual = $hitungDetail->quantity_actual + $inputQuantity;
            $hitungDetail->update(['quantity_actual' => $newQuantityActual]);

            // Hitung loss_total
            $pricePerUnit = $hitungDetail->quantity_expect > 0
                ? $hitungDetail->total / $hitungDetail->quantity_expect
                : 0;

            if ($this->hitungAction === 'Hitung Persediaan') {
                // Selisih modal = harga per unit × (aktual - expected)
                // Positif = kelebihan, Negatif = kekurangan/kerugian
                $lossTotal = $pricePerUnit * ($newQuantityActual - $hitungDetail->quantity_expect);
            } else {
                // Kerugian = harga per unit × jumlah rusak/hilang
                $lossTotal = $pricePerUnit * $newQuantityActual;
            }

            $hitungDetail->update(['loss_total' => $lossTotal]);

            // TIDAK update MaterialBatch di sini
            // Pengurangan batch_quantity dilakukan saat finish() di Rincian.php
        }

        // Update total kerugian pada hitung
        $hitung = $this->hitung;
        $hitung->refresh();
        $hitung->update([
            'loss_grand_total' => $hitung->details->sum('loss_total'),
        ]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($hitung)
            ->event('updated')
            ->withProperties(['action' => $this->hitungAction])
            ->log('Memperbarui data '.$this->hitungAction);

        return redirect()->route('hitung.rincian', ['id' => $this->hitung_id])
            ->with('success', $this->hitungAction.' berhasil diperbarui.');
    }

    public function markAllAs(): void
    {
        foreach ($this->hitungDetails as $index => $detail) {
            $quantityExpect = $detail['quantity_expect'];
            $quantityActual = $detail['quantity_actual'];

            if ($this->hitungAction === 'Hitung Persediaan') {
                // Tandai hitung semua: isi sisa yang belum terhitung
                // Jika sudah 0 terhitung dan expected 100, maka input = 100
                // Jika sudah terhitung 30 dan expected 100, maka input = 70
                $remaining = max(0, $quantityExpect - $quantityActual);
                $this->hitungDetails[$index]['quantity_input'] = $remaining;
            } else {
                // Tandai rusak/hilang semua: isi sisa yang tersedia
                $sisaTersedia = max(0, $quantityExpect - $quantityActual);
                $this->hitungDetails[$index]['quantity_input'] = $sisaTersedia;
            }
        }

        $this->calculateDerived();
        $this->validateQuantities();
    }

    public function render()
    {
        return view('livewire.hitung.mulai');
    }
}
