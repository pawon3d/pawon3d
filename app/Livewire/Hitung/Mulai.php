<?php

namespace App\Livewire\Hitung;

use Illuminate\Support\Facades\View;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

class Mulai extends Component
{
    use \Jantinnerezo\LivewireAlert\LivewireAlert;
    public $hitung_id;
    public $hitung;
    public $hitungDetails = [], $errorInputs = [];
    public $showHistoryModal = false;
    public $activityLogs = [];

    public function mount($id)
    {
        $this->hitung_id = $id;
        $this->hitung = \App\Models\Hitung::with(['details', 'details.material', 'details.unit'])
            ->findOrFail($this->hitung_id);
        $this->hitungDetails = $this->hitung->details->map(function ($detail) {
            return [
                'id' => $detail->id,
                'material_name' => $detail->material->name,
                'quantity_expect' => $detail->quantity_expect,
                'quantity_actual' => $detail->quantity_actual,
                'unit' => $detail->unit->name . ' (' . $detail->unit->alias . ')',
                'quantity' => 0,
            ];
        })->toArray();
        View::share('title', $this->hitung->action);
    }

    public function riwayatPembaruan()
    {
        $this->activityLogs = Activity::inLog('hitungs')->where('subject_id', $this->hitung_id)
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function validateQuantities()
    {
        $this->errorInputs = [];

        foreach ($this->hitungDetails as $index => $detail) {
            $hitungDetail = \App\Models\HitungDetail::find($detail['id']);
            if ($hitungDetail) {
                if ($this->hitung->action == 'Hitung Persediaan') {
                    $sisa = abs($detail['quantity_expect'] - $detail['quantity_actual']);
                } else {
                    $sisa = $detail['quantity_expect'] - $detail['quantity_actual'];
                }
                if ($detail['quantity'] > $sisa) {
                    $this->errorInputs[$index] = true;
                }
            }
        }
    }

    public function updatedHitungDetails($value, $key)
    {
        $this->validateQuantities();
    }

    public function save()
    {
        $this->validate([
            'hitungDetails.*.quantity' => 'required|numeric',
        ], [
            'hitungDetails.*.quantity.required' => 'Jumlah yang didapatkan harus diisi.',
            'hitungDetails.*.quantity.numeric' => 'Jumlah yang didapatkan harus berupa angka.',
            'hitungDetails.*.quantity.min' => 'Jumlah yang didapatkan tidak boleh kurang dari 0.',
        ]);

        $this->validateQuantities();

        if (count($this->errorInputs) > 0) {
            $this->alert('error', 'Masih ada input yang melebihi jumlah yang diharapkan.');
            return;
        }

        foreach ($this->hitungDetails as $detail) {
            $hitungDetail = \App\Models\HitungDetail::find($detail['id']);
            if ($hitungDetail) {
                // Hitung quantity baru yang ditambahkan
                $quantityToAdd = $detail['quantity'];

                // Update detail belanja
                $updatedQuantityActual = $detail['quantity_actual'] + $quantityToAdd;
                $hitungDetail->update([
                    'quantity_actual' => $updatedQuantityActual,
                ]);

                $hitungDetail->update([
                    'loss_total' => $this->hitung->action == 'Hitung Persediaan'
                        ? $hitungDetail->total / $hitungDetail->quantity_expect * ($hitungDetail->quantity_actual - $hitungDetail->quantity_expect)
                        : $hitungDetail->total / $hitungDetail->quantity_expect * $hitungDetail->quantity_actual,
                ]);


                $hitungDetail->hitung->update([
                    'loss_grand_total' => $hitungDetail->hitung->details->sum('loss_total'),
                ]);
            }
        }

        return redirect()->route('hitung.rincian', ['id' => $this->hitung_id])
            ->with('success', $this->hitung->action . ' berhasil diperbarui.');
    }

    public function markAllReceived()
    {
        foreach ($this->hitungDetails as $index => $detail) {
            $hitungDetail = \App\Models\HitungDetail::find($detail['id']);

            if ($hitungDetail) {
                $remaining = $hitungDetail->quantity_expect - $hitungDetail->quantity_actual;

                if ($remaining > 0) {
                    $this->hitungDetails[$index]['quantity'] = $remaining;
                } else {
                    $this->hitungDetails[$index]['quantity'] = 0;
                }
            }
        }

        // Panggil fungsi simpan biasa
        $this->save();
    }
    public function render()
    {
        return view('livewire.hitung.mulai');
    }
}
