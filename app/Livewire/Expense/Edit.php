<?php

namespace App\Livewire\Expense;

use Livewire\Component;

class Edit extends Component
{
    public $supplier_id = '';
    public $expense_id;
    public $expense_date = 'dd/mm/yyyy', $note, $grand_total_expect;
    public $expense_details = [], $prevInputs = [], $prevPrice = [];

    public function mount($id)
    {
        \Illuminate\Support\Facades\View::share('title', 'Ubah Daftar Belanja');
        $this->expense_id = $id;
        $expense = \App\Models\Expense::with(['expenseDetails', 'supplier'])->findOrFail($this->expense_id);
        $this->supplier_id = $expense->supplier_id;
        $this->expense_date = \Carbon\Carbon::parse($expense->expense_date)->format('d/m/Y');
        $this->note = $expense->note;
        $this->grand_total_expect = $expense->grand_total_expect;
        $this->expense_details = $expense->expenseDetails->map(function ($detail) {
            return [
                'material_id' => $detail->material_id,
                'material_quantity' => ($detail->material->material_details->where('unit_id', $detail->unit_id)->first()->supply_quantity ?? 0) . ' (' . ($detail->material->material_details->where('unit_id', $detail->unit_id)->first()->unit->alias ?? '-') . ')',
                'quantity_expect' => $detail->quantity_expect,
                'unit_id' => $detail->unit_id,
                'price_expect' => $detail->price_expect,
                'detail_total_expect' => $detail->total_expect,
            ];
        })->toArray();
        if (empty($this->expense_details)) {
            $this->expense_details = [[
                'material_id' => '',
                'material_quantity' => '0 (satuan)',
                'quantity_expect' => 0,
                'unit_id' => '',
                'price_expect' => 0,
                'detail_total_expect' => 0,
            ]];
        } else {
            $this->expense_details = array_values($this->expense_details);
        }
    }

    public function addDetail()
    {
        $this->expense_details[] = [
            'material_id' => '',
            'material_quantity' => '0 (satuan)',
            'quantity_expect' => 0,
            'unit_id' => '',
            'price_expect' => 0,
            'detail_total_expect' => 0,
        ];
    }

    public function removeDetail($index)
    {
        unset($this->expense_details[$index]);
        $this->expense_details = array_values($this->expense_details);
    }

    public function setMaterial($index, $materialId)
    {
        if ($materialId) {
            $material = \App\Models\Material::find($materialId);
            $this->expense_details[$index]['material_id'] = $materialId;
            if ($this->expense_details[$index]['unit_id'] != '') {
                $unit = \App\Models\Unit::find($this->expense_details[$index]['unit_id']);
                $this->expense_details[$index]['material_quantity'] = ($material->material_details->where('unit_id', $unit->id)->first()->supply_quantity ?? 0) . ' (' . ($material->material_details->where('unit_id', $unit->id)->first()->unit->alias ?? '-') . ')';
                $price = $material->material_details->where('unit_id', $unit->id)->first()->supply_price ?? 0;
                if ($price > 0) {
                    $this->prevInputs[$index] = true;
                    $this->prevPrice[$index] = $price;
                }
            } else {
                $this->expense_details[$index]['material_quantity'] = ($material->material_details->where('is_main', true)->first()->supply_quantity ?? 0) . ' (' . ($material->material_details->where('is_main', true)->first()->unit->alias ?? '-') . ')';
                $this->expense_details[$index]['unit_id'] = '';
            }
            $this->expense_details[$index]['quantity_expect'] = 0; // Reset quantity when changing material
            $this->expense_details[$index]['price_expect'] = 0; // Reset price when changing material
            $this->expense_details[$index]['detail_total_expect'] = 0; // Reset total when changing material

        } else {
            $this->expense_details[$index]['material_id'] = '';
            $this->expense_details[$index]['unit_id'] = '';
            $this->expense_details[$index]['material_quantity'] = '0 (satuan)';
            $this->expense_details[$index]['quantity_expect'] = 0;
            $this->expense_details[$index]['price_expect'] = 0;
            $this->expense_details[$index]['detail_total_expect'] = 0;
        }
    }

    public function setUnit($index, $unitId)
    {
        if ($unitId) {
            $this->expense_details[$index]['unit_id'] = $unitId;
            if ($this->expense_details[$index]['material_id'] != '') {
                $material = \App\Models\Material::find($this->expense_details[$index]['material_id']);
                $unit = \App\Models\Unit::find($unitId);
                $this->expense_details[$index]['material_quantity'] = ($material->material_details->where('unit_id', $unit->id)->first()->supply_quantity ?? 0) . ' (' . ($material->material_details->where('unit_id', $unit->id)->first()->unit->alias ?? '-') . ')';
                $price = $material->material_details->where('unit_id', $unit->id)->first()->supply_price ?? 0;
                if ($price > 0) {
                    $this->prevInputs[$index] = true;
                    $this->prevPrice[$index] = $price;
                }
            } else {
                $this->expense_details[$index]['material_quantity'] = '0 (satuan)';
            }
        } else {
            $this->expense_details[$index]['unit_id'] = '';
        }
    }

    public function updatedExpenseDetails()
    {
        $this->expense_details = array_map(function ($detail) {
            if (isset($detail['material_id']) && isset($detail['unit_id']) && $detail['material_id'] && $detail['unit_id']) {
                $detail['detail_total_expect'] = $detail['quantity_expect'] * $detail['price_expect'];
            }
            return $detail;
        }, $this->expense_details);
        $this->grand_total_expect = array_sum(array_column($this->expense_details, 'detail_total_expect'));
    }

    public function updatedSupplierId($value)
    {
        $this->supplier_id = $value;
    }
    public function update()
    {
        $this->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'expense_date' => 'nullable|date_format:d/m/Y',
            'note' => 'nullable|string|max:255',
            'grand_total_expect' => 'required|numeric|min:0',
            'expense_details.*.material_id' => 'required|exists:materials,id',
            'expense_details.*.quantity_expect' => 'required|numeric|min:0',
            'expense_details.*.unit_id' => 'required|exists:units,id',
            'expense_details.*.price_expect' => 'required|numeric|min:0',
        ]);

        $expense = \App\Models\Expense::findOrFail($this->expense_id);
        $expense->update([
            'supplier_id' => $this->supplier_id,
            'expense_date' => \Carbon\Carbon::createFromFormat('d/m/Y', $this->expense_date)->format('Y-m-d'),
            'note' => $this->note,
            'grand_total_expect' => $this->grand_total_expect,
        ]);

        // Hapus semua detail yang ada sebelum menambahkan yang baru
        $expense->expenseDetails()->delete();
        // Tambahkan detail baru
        foreach ($this->expense_details as $detail) {
            $expense->expenseDetails()->create([
                'material_id' => $detail['material_id'],
                'unit_id' => $detail['unit_id'],
                'quantity_expect' => $detail['quantity_expect'],
                'price_expect' => $detail['price_expect'],
                'total_expect' => $detail['detail_total_expect'],
            ]);
        }

        return redirect()->route('belanja.rincian', ['id' => $expense->id])->with('success', 'Daftar belanja berhasil diperbarui.');
    }
    public function render()
    {
        return view('livewire.expense.edit', [
            'suppliers' => \App\Models\Supplier::lazy(),
            'materials' => \App\Models\Material::with('material_details')->get(),
        ]);
    }
}
