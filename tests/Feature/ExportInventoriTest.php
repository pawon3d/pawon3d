<?php

declare(strict_types=1);

use App\Exports\InventoriExport;
use App\Models\Expense;
use App\Models\ExpenseDetail;
use App\Models\InventoryLog;
use App\Models\Material;
use App\Models\MaterialBatch;
use App\Models\MaterialDetail;
use App\Models\Supplier;
use App\Models\Unit;
use Carbon\Carbon;

it('calculates persediaan used value from inventory logs', function () {
    $unit = Unit::create([
        'name' => 'Kilogram',
        'alias' => 'kg',
        'group' => 'berat',
        'conversion_factor' => 1,
    ]);

    $material = Material::create([
        'name' => 'Tepung Terigu',
        'description' => 'Bahan baku test',
        'is_active' => true,
        'is_recipe' => false,
        'minimum' => 0,
        'status' => 'Aktif',
    ]);

    MaterialDetail::create([
        'material_id' => $material->id,
        'unit_id' => $unit->id,
        'is_main' => true,
        'quantity' => 1,
        'supply_quantity' => 10,
        'supply_price' => 15000,
    ]);

    $supplier = Supplier::create([
        'name' => 'Supplier Test',
    ]);

    $expense = Expense::create([
        'supplier_id' => $supplier->id,
        'expense_date' => Carbon::parse('2026-04-10')->toDateString(),
        'status' => 'Selesai',
        'grand_total_expect' => 15000,
        'grand_total_actual' => 15000,
        'is_start' => true,
        'is_finish' => true,
    ]);

    ExpenseDetail::create([
        'expense_id' => $expense->id,
        'material_id' => $material->id,
        'unit_id' => $unit->id,
        'quantity_expect' => 1,
        'quantity_get' => 1,
        'is_quantity_get' => true,
        'price_expect' => 15000,
        'price_get' => 15000,
        'total_expect' => 15000,
        'total_actual' => 15000,
    ]);

    $batch = MaterialBatch::create([
        'material_id' => $material->id,
        'unit_id' => $unit->id,
        'batch_number' => 'B-TEST-001',
        'date' => Carbon::parse('2026-04-30')->toDateString(),
        'batch_quantity' => 10,
    ]);

    InventoryLog::create([
        'material_id' => $material->id,
        'material_batch_id' => $batch->id,
        'user_id' => null,
        'action' => 'produksi',
        'quantity_change' => -2,
        'quantity_after' => 8,
        'reference_type' => 'production',
        'reference_id' => null,
        'note' => 'Pemakaian bahan baku test',
    ]);

    $export = new InventoriExport('persediaan', 'Bulan', '2026-04-01', null, null, 'semua');
    $rows = $export->collection();

    $usedRow = $rows->firstWhere('col1', 'Nilai Persediaan Terpakai');
    $detailRow = $rows->firstWhere('col1', 'Tepung Terigu');

    expect($usedRow)->not->toBeNull();
    expect((int) str_replace(['Rp ', '.', ','], ['', '', ''], $usedRow['col2']))->toBe(30000);
    expect($detailRow)->not->toBeNull();
    expect($detailRow['col2'])->toBe('2,00 kg');
});
