<?php

namespace App\Livewire\Padan;

use Livewire\Component;

class Tambah extends Component
{
    public $action = '';

    public $padan_date = 'dd/mm/yyyy', $note = '', $grand_total = 0;
    public $padan_details = [];

    protected $messages = [
        'padan_details.*.material_id' => 'Daftar persediaan harus diisi.',
        'padan_details.*.unit_id' => 'Daftar persediaan harus diisi.',
    ];
    public function mount()
    {
        \Illuminate\Support\Facades\View::share('title', 'Tambah Aksi');

        $this->padan_details = [[
            'material_id' => '',
            'unit_id' => '',
            'material_quantity' => 0,
            'quantity_actual' => 0,
            'unit_name' => ' (satuan)',
            'total' => 0,
        ]];
    }

    public function addDetail()
    {
        $this->padan_details[] = [
            'material_id' => '',
            'unit_id' => '',
            'material_quantity' => 0,
            'quantity_actual' => 0,
            'unit_name' => ' (satuan)',
            'total' => 0,
        ];
    }

    public function removeDetail($index)
    {
        unset($this->padan_details[$index]);
        $this->padan_details = array_values($this->padan_details);
    }

    public function setMaterial($index, $materialId)
    {
        if ($materialId) {
            $material = \App\Models\Material::find($materialId);
            $this->padan_details[$index]['material_id'] = $materialId;
            if ($this->padan_details[$index]['unit_id'] != '') {
                $unit = \App\Models\Unit::find($this->padan_details[$index]['unit_id']);
                $this->padan_details[$index]['material_quantity'] = ($material->material_details->where('unit_id', $unit->id)->first()->supply_quantity ?? 0);
                $this->padan_details[$index]['unit_name'] = ' (' . ($material->material_details->where('unit_id', $unit->id)->first()->unit->alias ?? '-') . ')';
                $price = $material->material_details->where('unit_id', $unit->id)->first()->supply_price ?? 0;
                $this->padan_details[$index]['total'] = $this->padan_details[$index]['material_quantity'] * $price;
                $this->calculateGrandTotal();
            }
        } else {
            $this->padan_details[$index]['material_id'] = '';
            $this->padan_details[$index]['unit_id'] = '';
            $this->padan_details[$index]['material_quantity'] = 0;
            $this->padan_details[$index]['unit_name'] = ' (satuan)';
            $this->padan_details[$index]['total'] = 0;
            $this->calculateGrandTotal();
        }
    }

    public function setUnit($index, $unitId)
    {
        if ($unitId) {
            $this->padan_details[$index]['unit_id'] = $unitId;
            if ($this->padan_details[$index]['material_id'] != '') {
                $material = \App\Models\Material::find($this->padan_details[$index]['material_id']);
                $unit = \App\Models\Unit::find($unitId);
                $this->padan_details[$index]['material_quantity'] = ($material->material_details->where('unit_id', $unit->id)->first()->supply_quantity ?? 0);
                $this->padan_details[$index]['unit_name'] = ' (' . ($material->material_details->where('unit_id', $unit->id)->first()->unit->alias ?? '-') . ')';
                $price = $material->material_details->where('unit_id', $unit->id)->first()->supply_price ?? 0;
                $this->padan_details[$index]['total'] = $this->padan_details[$index]['material_quantity'] * $price;
                $this->calculateGrandTotal();
            }
        } else {
            $this->padan_details[$index]['unit_id'] = '';
            $this->padan_details[$index]['material_quantity'] = 0;
            $this->padan_details[$index]['unit_name'] = ' (satuan)';
            $this->padan_details[$index]['total'] = 0;
            $this->calculateGrandTotal();
        }
    }


    public function calculateGrandTotal()
    {
        $this->grand_total = array_sum(array_column($this->padan_details, 'total'));
    }

    public function updatedAction($value)
    {
        $this->action = $value;
    }
    public function store()
    {
        $this->validate([
            'action' => 'nullable',
            'padan_date' => $this->padan_date != 'dd/mm/yyyy' ? 'nullable|date_format:d/m/Y' : 'nullable',
            'note' => 'nullable|string|max:255',
            'grand_total' => 'nullable|numeric|min:0',
            'padan_details.*.material_id' => 'required|exists:materials,id',
            'padan_details.*.material_quantity' => 'nullable|numeric|min:0',
            'padan_details.*.unit_id' => 'required|exists:units,id',
            'padan_details.*.total' => 'nullable|numeric|min:0',
        ]);

        $padan = \App\Models\Padan::create([
            'action' => $this->action,
            'padan_date' => $this->padan_date != 'dd/mm/yyyy' ? \Carbon\Carbon::createFromFormat('d/m/Y', $this->padan_date)->format('Y-m-d') : null,
            'note' => $this->note,
            'grand_total' => $this->grand_total,
        ]);

        foreach ($this->padan_details as $detail) {
            $padan->details()->create([
                'material_id' => $detail['material_id'],
                'unit_id' => $detail['unit_id'],
                'quantity_expect' => $detail['material_quantity'],
                'quantity_actual' => $detail['quantity_actual'] ?? 0,
                'total' => $detail['total'],
                'loss_total' => $padan->action == 'Hitung Persediaan' ? $detail['total'] / $detail['material_quantity'] * ($detail['quantity_actual'] - $detail['material_quantity']) : $detail['total'] / $detail['material_quantity'] * $detail['quantity_actual'],
            ]);
        }

        $loss_grand_total = $padan->details->sum('loss_total');
        $padan->update(['loss_grand_total' => $loss_grand_total]);

        return redirect()->route('padan')->with('success', 'Aksi berhasil ditambahkan.');
    }

    public function start()
    {
        $this->validate([
            'action' => 'nullable',
            'padan_date' => 'nullable|date_format:d/m/Y',
            'note' => 'nullable|string|max:255',
            'grand_total' => 'nullable|numeric|min:0',
            'padan_details.*.material_id' => 'nullable|exists:materials,id',
            'padan_details.*.material_quantity' => 'nullable|numeric|min:0',
            'padan_details.*.unit_id' => 'nullable|exists:units,id',
            'padan_details.*.total' => 'nullable|numeric|min:0',
        ]);

        $padan = \App\Models\Padan::create([
            'action' => $this->action,
            'padan_date' => \Carbon\Carbon::createFromFormat('d/m/Y', $this->padan_date)->format('Y-m-d'),
            'note' => $this->note,
            'grand_total' => $this->grand_total,
            'status' => 'Sedang Diproses',
            'is_start' => true,
        ]);

        foreach ($this->padan_details as $detail) {
            $padan->details()->create([
                'material_id' => $detail['material_id'],
                'unit_id' => $detail['unit_id'],
                'quantity_expect' => $detail['material_quantity'],
                'quantity_actual' => $detail['quantity_actual'] ?? 0,
                'total' => $detail['total'],
                'loss_total' => $padan->action == 'Hitung Persediaan' ? $detail['total'] / $detail['material_quantity'] * ($detail['quantity_actual'] - $detail['material_quantity']) : $detail['total'] / $detail['material_quantity'] * $detail['quantity_actual'],
            ]);
        }

        $loss_grand_total = $padan->details->sum('loss_total');
        $padan->update(['loss_grand_total' => $loss_grand_total]);

        return redirect()->route('padan.rincian', ['id' => $padan->id])->with('success', 'Aksi berhasil Dimulai');
    }
    public function render()
    {
        return view('livewire.padan.tambah', [
            'materials' => \App\Models\Material::with('material_details')->orderBy('name')->lazy(),
        ]);
    }
}
