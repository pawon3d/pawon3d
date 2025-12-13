<?php

namespace App\Livewire\Expense;

use App\Services\NotificationService;
use Illuminate\Support\Facades\View;
use Livewire\Component;

class Form extends Component
{
    public $expense_id;

    public $supplier_id = '';

    public $expense_date = 'dd/mm/yyyy';

    public $note;

    public $grand_total_expect;

    public $expense_details = [];

    public $prevInputs = [];

    public $prevPrice = [];

    public function mount($id = null): void
    {
        $this->expense_id = $id;

        if ($id) {
            // Edit mode
            View::share('title', 'Ubah Daftar Belanja');
            $this->loadExpense($id);
        } else {
            // Create mode
            View::share('title', 'Tambah Daftar Belanja');
            $this->expense_details = [[
                'material_id' => '',
                'material_quantity' => '0 (satuan)',
                'quantity_expect' => 0,
                'unit_id' => '',
                'price_expect' => 0,
                'detail_total_expect' => 0,
            ]];
        }

        View::share('mainTitle', 'Inventori');
    }

    protected function loadExpense($id): void
    {
        $expense = \App\Models\Expense::with(['expenseDetails', 'supplier'])->findOrFail($id);
        $expense->load([
            'expenseDetails.material.material_details.unit',
            'expenseDetails.material.batches.unit',
            'expenseDetails.unit',
        ]);
        $this->supplier_id = $expense->supplier_id;
        $this->expense_date = \Carbon\Carbon::parse($expense->expense_date)->format('d M Y');
        $this->note = $expense->note;
        $this->grand_total_expect = $expense->grand_total_expect;
        $today = now()->format('Y-m-d');
        $prevInputs = [];
        $prevPrice = [];
        $this->expense_details = $expense->expenseDetails->map(function ($detail, $index) use ($today, &$prevInputs, &$prevPrice) {
            $material = $detail->material ?? \App\Models\Material::with(['material_details.unit', 'batches.unit'])->find($detail->material_id);

            if (! $material) {
                return [
                    'material_id' => '',
                    'material_quantity' => '0 (satuan)',
                    'quantity_expect' => 0,
                    'unit_id' => '',
                    'price_expect' => 0,
                    'detail_total_expect' => 0,
                ];
            }

            $unit = $detail->unit ?? \App\Models\Unit::find($detail->unit_id);
            $persediaan = $material->batches ?? collect();

            if ($unit) {
                if ($persediaan->isEmpty()) {
                    $satuan = $material->material_details->where('unit_id', $unit->id)->first();
                } else {
                    $satuan = $persediaan->where('unit_id', $unit->id)->sortBy('date')->where('date', '>=', $today)->first();
                }
                $batchItem = $persediaan->where('unit_id', $unit->id)->sortBy('date')->where('date', '>=', $today)->first();
            } else {
                $satuan = null;
                $batchItem = $persediaan->sortBy('date')->where('date', '>=', $today)->first();
            }

            $batchQty = $batchItem?->batch_quantity ?? 0;
            $aliasFallback = $material->material_details->where('is_main', true)->first()?->unit?->alias ?? '-';
            $alias = $unit?->alias ?? $aliasFallback;

            if ($unit) {
                $price = $material->material_details->where('unit_id', $unit->id)->first()?->supply_price ?? 0;
                if ($price > 0) {
                    $prevInputs[$index] = true;
                    $prevPrice[$index] = $price;
                }
            }

            return [
                'material_id' => $detail->material_id,
                'material_quantity' => $batchQty . ' (' . $alias . ')',
                'quantity_expect' => $detail->quantity_expect,
                'unit_id' => $detail->unit_id,
                'price_expect' => $detail->price_expect,
                'detail_total_expect' => $detail->total_expect,
            ];
        })->toArray();
        $this->prevInputs = $prevInputs;
        $this->prevPrice = $prevPrice;
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
            $persediaan = $material->batches;

            if ($this->expense_details[$index]['unit_id'] != '') {
                $unit = \App\Models\Unit::find($this->expense_details[$index]['unit_id']);
                if ($persediaan->isEmpty()) {
                    $satuan = $material->material_details->where('unit_id', $unit->id)->first();
                } else {
                    $satuan = $persediaan->where('unit_id', $unit->id)->sortBy('date')->where('date', '>=', now()->format('Y-m-d'))->first();
                }

                $batchItem = $persediaan->where('unit_id', $unit->id)->sortBy('date')->where('date', '>=', now()->format('Y-m-d'))->first();
                $batchQty = $batchItem?->batch_quantity ?? 0;
                $aliasFallback = $material->material_details->where('is_main', true)->first()?->unit?->alias ?? '-';
                $alias = $satuan?->unit?->alias ?? $aliasFallback;

                $this->expense_details[$index]['material_quantity'] = $batchQty . ' (' . $alias . ')';

                $price = $material->material_details->where('unit_id', $unit->id)->first()?->supply_price ?? 0;
                if ($price > 0) {
                    $this->prevInputs[$index] = true;
                    $this->prevPrice[$index] = $price;
                }
            } else {
                $batchItem = $persediaan->sortBy('date')->where('date', '>=', now()->format('Y-m-d'))->first();
                $batchQty = $batchItem?->batch_quantity ?? 0;
                $alias = $material->material_details->where('is_main', true)->first()?->unit?->alias ?? '-';
                $this->expense_details[$index]['material_quantity'] = $batchQty . ' (' . $alias . ')';
                $this->expense_details[$index]['unit_id'] = '';
            }
            $this->expense_details[$index]['price_expect'] = 0;
            $this->expense_details[$index]['detail_total_expect'] = 0;
            $this->expense_details[$index]['quantity_expect'] = 0;
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
                $persediaan = $material->batches;
                if ($persediaan->isEmpty()) {
                    $satuan = $material->material_details->where('unit_id', $unitId)->first();
                } else {
                    $satuan = $persediaan->where('unit_id', $unitId)->sortBy('date')->where('date', '>=', now()->format('Y-m-d'))->first();
                }

                $batchItem = $persediaan->where('unit_id', $unitId)->sortBy('date')->where('date', '>=', now()->format('Y-m-d'))->first();
                $batchQty = $batchItem?->batch_quantity ?? 0;
                $aliasFallback = $material->material_details->where('is_main', true)->first()?->unit?->alias ?? '-';
                $alias = $unit?->alias ?? $aliasFallback;

                $this->expense_details[$index]['material_quantity'] = $batchQty . ' (' . $alias . ')';

                $price = $material->material_details->where('unit_id', $unit->id)->first()?->supply_price ?? 0;
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

    public function store()
    {
        $this->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'expense_date' => $this->expense_date != 'dd/mm/yyyy' ? 'nullable|date_format:d M Y' : 'nullable',
            'note' => 'nullable|string|max:255',
            'grand_total_expect' => 'required|numeric|min:0',
            'expense_details.*.material_id' => 'required|exists:materials,id',
            'expense_details.*.quantity_expect' => 'required|numeric|min:0',
            'expense_details.*.unit_id' => 'required|exists:units,id',
            'expense_details.*.price_expect' => 'required|numeric|min:0',
        ]);

        $expense = \App\Models\Expense::create([
            'supplier_id' => $this->supplier_id,
            'expense_date' => $this->expense_date != 'dd/mm/yyyy' ? \Carbon\Carbon::createFromFormat('d M Y', $this->expense_date)->format('Y-m-d') : null,
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
        }

        // Kirim notifikasi rencana belanja dibuat
        NotificationService::shoppingPlanCreated($expense->expense_number);

        return redirect()->route('belanja.rencana')->with('success', 'Daftar belanja berhasil ditambahkan.');
    }

    public function start()
    {
        $this->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'expense_date' => $this->expense_date != 'dd/mm/yyyy' ? 'nullable|date_format:d M Y' : 'nullable',
            'note' => 'nullable|string|max:255',
            'grand_total_expect' => 'required|numeric|min:0',
            'expense_details.*.material_id' => 'required|exists:materials,id',
            'expense_details.*.quantity_expect' => 'required|numeric|min:0',
            'expense_details.*.unit_id' => 'required|exists:units,id',
            'expense_details.*.price_expect' => 'required|numeric|min:0',
        ]);

        $expense = \App\Models\Expense::create([
            'supplier_id' => $this->supplier_id,
            'expense_date' => $this->expense_date != 'dd/mm/yyyy' ? \Carbon\Carbon::createFromFormat('d F Y', $this->expense_date)->format('Y-m-d') : null,
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

        // Update harga modal produk yang menggunakan material yang harganya berubah
        $affectedMaterialIds = collect($this->expense_details)->pluck('material_id')->unique();

        foreach ($affectedMaterialIds as $materialId) {
            // Cari semua produk yang menggunakan material ini
            $productCompositions = \App\Models\ProductComposition::where('material_id', $materialId)
                ->with(['product', 'material.material_details'])
                ->get();

            foreach ($productCompositions as $composition) {
                $product = $composition->product;

                // Hitung ulang capital produk
                $compositionTotal = $product->product_compositions->sum(function ($comp) {
                    $materialDetail = \App\Models\MaterialDetail::where('material_id', $comp->material_id)
                        ->where('unit_id', $comp->unit_id)
                        ->first();

                    $materialPrice = $materialDetail?->supply_price ?? 0;

                    // Jika harga 0, coba konversi dari unit lain yang sudah ada harga
                    if ($materialPrice == 0) {
                        $targetUnit = \App\Models\Unit::find($comp->unit_id);
                        $otherDetails = \App\Models\MaterialDetail::where('material_id', $comp->material_id)
                            ->where('unit_id', '!=', $comp->unit_id)
                            ->where('supply_price', '>', 0)
                            ->with('unit')
                            ->get();

                        foreach ($otherDetails as $otherDetail) {
                            if ($otherDetail->unit && $targetUnit) {
                                // Konversi harga dari unit lain ke unit target
                                $convertedQuantity = $otherDetail->unit->convertTo(1, $targetUnit);
                                if ($convertedQuantity !== null) {
                                    $materialPrice = $otherDetail->supply_price * $convertedQuantity;
                                    break;
                                }
                            }
                        }
                    }

                    return $materialPrice * $comp->material_quantity;
                });

                $otherTotal = $product->other_costs->sum('price');
                $capital = $otherTotal + $compositionTotal;
                $pcsCapital = $product->pcs > 0 ? $capital / $product->pcs : $capital;

                // Update capital produk
                $product->update([
                    'capital' => $capital,
                    'pcs_capital' => $pcsCapital,
                ]);
            }
        }

        // Kirim notifikasi belanja dimulai
        NotificationService::shoppingStarted($expense->expense_number);

        return redirect()->route('belanja.rincian', ['id' => $expense->id])->with('success', 'Belanja berhasil Dimulai');
    }

    public function update()
    {
        $this->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'expense_date' => 'nullable|date_format:d M Y',
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
            'expense_date' => \Carbon\Carbon::createFromFormat('d M Y', $this->expense_date)->format('Y-m-d'),
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
        return view('livewire.expense.form', [
            'suppliers' => \App\Models\Supplier::select('id', 'name')->get(),
            'materials' => \App\Models\Material::select('id', 'name')
                ->with(['material_details:id,material_id,unit_id,supply_price,is_main', 'material_details.unit:id,name,alias', 'batches:id,material_id,unit_id,batch_quantity,date'])
                ->get(),
        ]);
    }
}
