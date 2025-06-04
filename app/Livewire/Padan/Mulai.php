<?php

namespace App\Livewire\Padan;

use Illuminate\Support\Facades\View;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

class Mulai extends Component
{
    use \Jantinnerezo\LivewireAlert\LivewireAlert;
    public $padan_id;
    public $padan;
    public $padanDetails = [], $errorInputs = [];
    public $showHistoryModal = false;
    public $activityLogs = [];

    public function mount($id)
    {
        $this->padan_id = $id;
        $this->padan = \App\Models\Padan::with(['details', 'details.material', 'details.unit'])
            ->findOrFail($this->padan_id);
        $this->padanDetails = $this->padan->details->map(function ($detail) {
            return [
                'id' => $detail->id,
                'material_name' => $detail->material->name,
                'quantity_expect' => $detail->quantity_expect,
                'quantity_actual' => $detail->quantity_actual,
                'unit' => $detail->unit->name . ' (' . $detail->unit->alias . ')',
                'quantity' => 0,
            ];
        })->toArray();
        View::share('title', $this->padan->action);
    }

    public function riwayatPembaruan()
    {
        $this->activityLogs = Activity::inLog('padans')->where('subject_id', $this->padan_id)
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function validateQuantities()
    {
        $this->errorInputs = [];

        foreach ($this->padanDetails as $index => $detail) {
            $padanDetail = \App\Models\PadanDetail::find($detail['id']);
            if ($padanDetail) {
                if ($this->padan->action == 'Hitung Persediaan') {
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

    public function updatedPadanDetails($value, $key)
    {
        $this->validateQuantities();
    }

    public function save()
    {
        $this->validate([
            'padanDetails.*.quantity' => 'required|numeric',
        ], [
            'padanDetails.*.quantity.required' => 'Jumlah yang didapatkan harus diisi.',
            'padanDetails.*.quantity.numeric' => 'Jumlah yang didapatkan harus berupa angka.',
            'padanDetails.*.quantity.min' => 'Jumlah yang didapatkan tidak boleh kurang dari 0.',
        ]);

        $this->validateQuantities();

        if (count($this->errorInputs) > 0) {
            $this->alert('error', 'Masih ada input yang melebihi jumlah yang diharapkan.');
            return;
        }

        foreach ($this->padanDetails as $detail) {
            $padanDetail = \App\Models\PadanDetail::find($detail['id']);
            if ($padanDetail) {
                // Hitung quantity baru yang ditambahkan
                $quantityToAdd = $detail['quantity'];

                // Update detail belanja
                $updatedQuantityActual = $detail['quantity_actual'] + $quantityToAdd;
                $padanDetail->update([
                    'quantity_actual' => $updatedQuantityActual,
                ]);

                $padanDetail->update([
                    'loss_total' => $this->padan->action == 'Hitung Persediaan'
                        ? $padanDetail->total / $padanDetail->quantity_expect * ($padanDetail->quantity_actual - $padanDetail->quantity_expect)
                        : $padanDetail->total / $padanDetail->quantity_expect * $padanDetail->quantity_actual,
                ]);


                $padanDetail->padan->update([
                    'loss_grand_total' => $padanDetail->padan->details->sum('loss_total'),
                ]);
            }
        }

        return redirect()->route('padan.rincian', ['id' => $this->padan_id])
            ->with('success', $this->padan->action . ' berhasil diperbarui.');
    }

    public function markAllReceived()
    {
        foreach ($this->padanDetails as $index => $detail) {
            $padanDetail = \App\Models\PadanDetail::find($detail['id']);

            if ($padanDetail) {
                $remaining = $padanDetail->quantity_expect - $padanDetail->quantity_actual;

                if ($remaining > 0) {
                    $this->padanDetails[$index]['quantity'] = $remaining;
                } else {
                    $this->padanDetails[$index]['quantity'] = 0;
                }
            }
        }

        // Panggil fungsi simpan biasa
        $this->save();
    }
    public function render()
    {
        return view('livewire.padan.mulai');
    }
}
