<?php

namespace App\Livewire\Expense;

use Livewire\Component;

class Tambah extends Component
{
    public $supplier_id = '';

    public $expense_date = 'dd/mm/yyyy', $note, $grand_total_expect;
    public $expense_details = [];

    public function mount()
    {
        \Illuminate\Support\Facades\View::share('title', 'Tambah Daftar Belanja');

        $this->expense_details = [[
            'material_id' => '',
            'material_quantity' => '0 (satuan)',
            'quantity_expect' => 0,
            'unit_id' => '',
            'price_expect' => 0,
            'detail_total_expect' => 0,
        ]];
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
            $this->expense_details[$index]['unit_id'] = '';
            $this->expense_details[$index]['material_quantity'] = ($material->material_details->where('is_main', true)->first()->supply_quantity ?? 0) . ' (' . ($material->material_details->where('is_main', true)->first()->unit->alias ?? '-') . ')';
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
        } else {
            $this->expense_details[$index]['unit_id'] = '';
        }
    }

    public function updatedExpenseDetails()
    {
        $this->expense_details = array_map(function ($detail) {
            if (isset($detail['material_id']) && $detail['material_id']) {
                $material = \App\Models\Material::find($detail['material_id']);
                $detail['material_quantity'] = ($material->material_details->where('is_main', true)->first()->supply_quantity ?? 0) . ' (' . ($material->material_details->where('is_main', true)->first()->unit->alias ?? '-') . ')';
                $detail['detail_total_expect'] = $detail['quantity_expect'] * $detail['price_expect'];
            } else {
                $detail['material_quantity'] = '0 (satuan)';
            }
            return $detail;
        }, $this->expense_details);
        $this->grand_total_expect = array_sum(array_column($this->expense_details, 'detail_total_expect'));
    }
    // public function updatedExpenseDate($value)
    // {
    //     $this->expense_date = \Carbon\Carbon::createFromFormat('d/m/Y', $value)->format('d/m/Y');
    // }
    public function updatedSupplierId($value)
    {
        $this->supplier_id = $value;
    }
    public function store()
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

        $expense = \App\Models\Expense::create([
            'supplier_id' => $this->supplier_id,
            'expense_date' => \Carbon\Carbon::createFromFormat('d/m/Y', $this->expense_date)->format('Y-m-d'),
            'note' => $this->note,
            'grand_total_expect' => $this->grand_total_expect,
        ]);

        foreach ($this->expense_details as $detail) {
            $expense->expenseDetails()->create([
                'material_id' => $detail['material_id'],
                'unit_id' => $detail['unit_id'],
                'quantity_expect' => $detail['quantity_expect'],
                'price_expect' => $detail['price_expect'],
                'total_expect' => $detail['detail_total_expect'],
            ]);
            $materialDetail = \App\Models\MaterialDetail::where('material_id', $detail['material_id'])
                ->where('unit_id', $detail['unit_id'])
                ->first();

            if ($materialDetail) {
                $materialDetail->update([
                    'supply_price' => $detail['price_expect'],
                ]);
            }
        }

        return redirect()->route('belanja')->with('success', 'Daftar belanja berhasil ditambahkan.');
    }

    public function start()
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

        $expense = \App\Models\Expense::create([
            'supplier_id' => $this->supplier_id,
            'expense_date' => \Carbon\Carbon::createFromFormat('d/m/Y', $this->expense_date)->format('Y-m-d'),
            'note' => $this->note,
            'grand_total_expect' => $this->grand_total_expect,
            'status' => 'Dimulai',
            'is_start' => true,
        ]);

        foreach ($this->expense_details as $detail) {
            $expense->expenseDetails()->create([
                'material_id' => $detail['material_id'],
                'unit_id' => $detail['unit_id'],
                'quantity_expect' => $detail['quantity_expect'],
                'price_expect' => $detail['price_expect'],
                'total_expect' => $detail['detail_total_expect'],
            ]);
            $materialDetail = \App\Models\MaterialDetail::where('material_id', $detail['material_id'])
                ->where('unit_id', $detail['unit_id'])
                ->first();

            if ($materialDetail) {
                $materialDetail->update([
                    'supply_price' => $detail['price_expect'],
                ]);
            }
        }
        return redirect()->route('belanja.rincian', ['id' => $expense->id])->with('success', 'Belanja berhasil Dimulai');
    }
    public function render()
    {
        return view('livewire.expense.tambah', [
            'suppliers' => \App\Models\Supplier::lazy(),
            'materials' => \App\Models\Material::with('material_details')->get(),
        ]);
    }
}