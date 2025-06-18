<?php

namespace App\Livewire\Hitung;

use Livewire\Component;

class Tambah extends Component
{
    public $action = '';

    public $hitung_date = 'dd/mm/yyyy', $note = '', $grand_total = 0;
    public $hitung_details = [];

    protected $messages = [
        'hitung_details.*.material_id' => 'Daftar persediaan harus diisi.',
        'hitung_details.*.material_batch_id' => 'Daftar asd harus diisi.',
        'action.required' => 'Aksi harus diisi.',
    ];
    public function mount()
    {
        \Illuminate\Support\Facades\View::share('title', 'Tambah Aksi');

        $this->hitung_details = [[
            'material_id' => '',
            'material_batch_id' => '',
            'material_quantity' => 0,
            'quantity_actual' => 0,
            'unit_name' => ' (satuan)',
            'total' => 0,
        ]];
    }

    public function addDetail()
    {
        $this->hitung_details[] = [
            'material_id' => '',
            'material_batch_id' => '',
            'material_quantity' => 0,
            'quantity_actual' => 0,
            'unit_name' => ' (satuan)',
            'total' => 0,
        ];
    }

    public function removeDetail($index)
    {
        unset($this->hitung_details[$index]);
        $this->hitung_details = array_values($this->hitung_details);
        $this->calculateGrandTotal();
    }

    public function setMaterial($index, $materialId)
    {
        if ($materialId) {
            $material = \App\Models\Material::find($materialId);
            $this->hitung_details[$index]['material_id'] = $materialId;
            if ($this->hitung_details[$index]['material_batch_id'] != '') {
                $batch = \App\Models\MaterialBatch::find($this->hitung_details[$index]['material_batch_id']);
                $this->hitung_details[$index]['material_quantity'] = ($material->batches->where('id', $batch->id)->first()->quantity ?? 0);
                $this->hitung_details[$index]['unit_name'] = ' (' . ($material->batches->where('id', $batch->id)->first()->unit->alias ?? '-') . ')';
                $price = $material->material_details->where('unit_id', $batch->unit->id)->first()->supply_price ?? 0;
                $this->hitung_details[$index]['total'] = $this->hitung_details[$index]['material_quantity'] * $price;
                $this->calculateGrandTotal();
            }
        } else {
            $this->hitung_details[$index]['material_id'] = '';
            $this->hitung_details[$index]['material_batch_id'] = '';
            $this->hitung_details[$index]['material_quantity'] = 0;
            $this->hitung_details[$index]['unit_name'] = ' (satuan)';
            $this->hitung_details[$index]['total'] = 0;
            $this->calculateGrandTotal();
        }
    }

    public function setBatch($index, $batchId)
    {
        if ($batchId) {
            $this->hitung_details[$index]['material_batch_id'] = $batchId;
            if ($this->hitung_details[$index]['material_id'] != '') {
                $material = \App\Models\Material::find($this->hitung_details[$index]['material_id']);
                $batch = \App\Models\MaterialBatch::find($batchId);
                $this->hitung_details[$index]['material_quantity'] = ($material->batches->where('id', $batch->id)->first()->batch_quantity ?? 0);
                $this->hitung_details[$index]['unit_name'] = ' (' . ($material->batches->where('id', $batch->id)->first()->unit->alias ?? '-') . ')';
                $price = $material->material_details->where('unit_id', $batch->unit->id)->first()->supply_price ?? 0;
                $this->hitung_details[$index]['total'] = $this->hitung_details[$index]['material_quantity'] * $price;
                $this->calculateGrandTotal();
            }
        } else {
            $this->hitung_details[$index]['material_batch_id'] = '';
            $this->hitung_details[$index]['material_quantity'] = 0;
            $this->hitung_details[$index]['unit_name'] = ' (satuan)';
            $this->hitung_details[$index]['total'] = 0;
            $this->calculateGrandTotal();
        }
    }


    public function calculateGrandTotal()
    {
        $this->grand_total = array_sum(array_column($this->hitung_details, 'total'));
    }

    public function updatedAction($value)
    {
        $this->action = $value;
    }
    public function store()
    {
        $this->validate([
            'action' => 'required|string|max:255',
            'hitung_date' => $this->hitung_date != 'dd/mm/yyyy' ? 'nullable|date_format:d/m/Y' : 'nullable',
            'note' => 'nullable|string|max:255',
            'grand_total' => 'nullable|numeric|min:0',
            'hitung_details.*.material_id' => 'required|exists:materials,id',
            'hitung_details.*.material_quantity' => 'nullable|numeric|min:0',
            'hitung_details.*.material_batch_id' => 'required|exists:material_batches,id',
            'hitung_details.*.total' => 'nullable|numeric|min:0',
        ]);

        $hitung = \App\Models\Hitung::create([
            'action' => $this->action ?? 'Hitung Persediaan',
            'hitung_date' => $this->hitung_date != 'dd/mm/yyyy' ? \Carbon\Carbon::createFromFormat('d/m/Y', $this->hitung_date)->format('Y-m-d') : null,
            'note' => $this->note,
            'grand_total' => $this->grand_total,
        ]);

        foreach ($this->hitung_details as $detail) {
            $hitung->details()->create([
                'material_id' => $detail['material_id'],
                'material_batch_id' => $detail['material_batch_id'],
                'quantity_expect' => $detail['material_quantity'],
                'quantity_actual' => $detail['quantity_actual'] ?? 0,
                'total' => $detail['total'],
                'loss_total' => $hitung->action == 'Hitung Persediaan' ? $detail['total'] / $detail['material_quantity'] * ($detail['quantity_actual'] - $detail['material_quantity']) : $detail['total'] / $detail['material_quantity'] * $detail['quantity_actual'],
            ]);
        }

        $loss_grand_total = $hitung->details->sum('loss_total');
        $hitung->update(['loss_grand_total' => $loss_grand_total]);

        return redirect()->route('hitung')->with('success', 'Aksi berhasil ditambahkan.');
    }

    public function start()
    {
        $this->validate([
            'action' => 'required|string|max:255',
            'hitung_date' => $this->hitung_date != 'dd/mm/yyyy' ? 'nullable|date_format:d/m/Y' : 'nullable',
            'note' => 'nullable|string|max:255',
            'grand_total' => 'nullable|numeric|min:0',
            'hitung_details.*.material_id' => 'nullable|exists:materials,id',
            'hitung_details.*.material_quantity' => 'nullable|numeric|min:0',
            'hitung_details.*.material_batch_id' => 'nullable|exists:material_batches,id',
            'hitung_details.*.total' => 'nullable|numeric|min:0',
        ]);

        $hitung = \App\Models\Hitung::create([
            'action' => $this->action ?? 'Hitung Persediaan',
            'hitung_date' => $this->hitung_date != 'dd/mm/yyyy' ? \Carbon\Carbon::createFromFormat('d/m/Y', $this->hitung_date)->format('Y-m-d') : null,
            'note' => $this->note,
            'grand_total' => $this->grand_total,
            'status' => 'Sedang Diproses',
            'is_start' => true,
        ]);

        foreach ($this->hitung_details as $detail) {
            $hitung->details()->create([
                'material_id' => $detail['material_id'],
                'material_batch_id' => $detail['material_batch_id'],
                'quantity_expect' => $detail['material_quantity'],
                'quantity_actual' => $detail['quantity_actual'] ?? 0,
                'total' => $detail['total'],
                'loss_total' => $hitung->action == 'Hitung Persediaan' ? $detail['total'] / $detail['material_quantity'] * ($detail['quantity_actual'] - $detail['material_quantity']) : $detail['total'] / $detail['material_quantity'] * $detail['quantity_actual'],
            ]);
        }

        $loss_grand_total = $hitung->details->sum('loss_total');
        $hitung->update(['loss_grand_total' => $loss_grand_total]);

        return redirect()->route('hitung.rincian', ['id' => $hitung->id])->with('success', 'Aksi berhasil Dimulai');
    }
    public function render()
    {
        return view('livewire.hitung.tambah', [
            'materials' => \App\Models\Material::with('material_details')->orderBy('name')->lazy(),
        ]);
    }
}
