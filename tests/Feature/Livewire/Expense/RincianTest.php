<?php

declare(strict_types=1);

use App\Livewire\Expense\Rincian;
use App\Models\Expense;
use App\Models\ExpenseDetail;
use App\Models\InventoryLog;
use App\Models\Material;
use App\Models\MaterialBatch;
use App\Models\MaterialDetail;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->user = User::factory()->create();

    // Create permission if not exists
    $permission = Permission::firstOrCreate(['name' => 'inventori.belanja.kelola']);
    $this->user->givePermissionTo($permission);

    $this->actingAs($this->user);

    // Create Unit
    $this->unit = Unit::create([
        'name' => 'Kilogram',
        'alias' => 'kg',
    ]);

    // Create Supplier
    $this->supplier = Supplier::create([
        'name' => 'Supplier Test',
        'contact_name' => 'John Doe',
        'phone' => '08123456789',
        'address' => 'Jl. Test No. 123',
    ]);

    // Create Material
    $this->material = Material::create([
        'name' => 'Tepung Terigu',
        'group' => 'persediaan',
    ]);

    // Create MaterialDetail
    $this->materialDetail = MaterialDetail::create([
        'material_id' => $this->material->id,
        'unit_id' => $this->unit->id,
        'base_quantity' => 1,
        'supply_quantity' => 0,
    ]);
});

test('finish expense creates inventory log', function () {
    // Create expense
    $expense = Expense::create([
        'supplier_id' => $this->supplier->id,
        'expense_number' => 'BP-251206-0001',
        'expense_date' => now(),
        'grand_total_expect' => 100000,
        'grand_total_actual' => 0,
        'status' => 'Rencana',
        'is_start' => true,
        'is_finish' => false,
    ]);

    // Create expense detail
    $expenseDetail = ExpenseDetail::create([
        'expense_id' => $expense->id,
        'material_id' => $this->material->id,
        'unit_id' => $this->unit->id,
        'quantity_expect' => 10,
        'quantity_get' => 10,
        'price_expect' => 10000,
        'price_actual' => 10000,
        'total_expect' => 100000,
        'total_actual' => 100000,
        'expiry_date' => now()->addMonths(6)->format('Y-m-d'),
    ]);

    // Ensure no inventory log exists before
    expect(InventoryLog::count())->toBe(0);

    // Finish expense - initialize component properly
    $component = new Rincian;
    $component->mount($expense->id);
    $component->finish();

    // Assert inventory log was created
    expect(InventoryLog::count())->toBe(1);

    $log = InventoryLog::first();
    expect($log->material_id)->toEqual($this->material->id)
        ->and($log->action)->toBe('belanja')
        ->and((float) $log->quantity_change)->toBe(10.0)
        ->and((float) $log->quantity_after)->toBe(10.0)
        ->and($log->reference_type)->toBe('expense')
        ->and($log->reference_id)->toEqual($expense->id)
        ->and($log->user_id)->toEqual($this->user->id)
        ->and($log->note)->toContain($expense->expense_number);
});

test('finish expense creates inventory log for each expense detail', function () {
    // Create another material
    $material2 = Material::create([
        'name' => 'Gula Pasir',
        'group' => 'persediaan',
    ]);

    MaterialDetail::create([
        'material_id' => $material2->id,
        'unit_id' => $this->unit->id,
        'base_quantity' => 1,
        'supply_quantity' => 0,
    ]);

    // Create expense
    $expense = Expense::create([
        'supplier_id' => $this->supplier->id,
        'expense_number' => 'BP-251206-0002',
        'expense_date' => now(),
        'grand_total_expect' => 200000,
        'grand_total_actual' => 0,
        'status' => 'Rencana',
        'is_start' => true,
        'is_finish' => false,
    ]);

    // Create two expense details
    ExpenseDetail::create([
        'expense_id' => $expense->id,
        'material_id' => $this->material->id,
        'unit_id' => $this->unit->id,
        'quantity_expect' => 10,
        'quantity_get' => 10,
        'price_expect' => 10000,
        'price_actual' => 10000,
        'total_expect' => 100000,
        'total_actual' => 100000,
        'expiry_date' => now()->addMonths(6)->format('Y-m-d'),
    ]);

    ExpenseDetail::create([
        'expense_id' => $expense->id,
        'material_id' => $material2->id,
        'unit_id' => $this->unit->id,
        'quantity_expect' => 5,
        'quantity_get' => 5,
        'price_expect' => 20000,
        'price_actual' => 20000,
        'total_expect' => 100000,
        'total_actual' => 100000,
        'expiry_date' => now()->addMonths(3)->format('Y-m-d'),
    ]);

    // Ensure no inventory log exists before
    expect(InventoryLog::count())->toBe(0);

    // Finish expense - initialize component properly
    $component = new Rincian;
    $component->mount($expense->id);
    $component->finish();

    // Assert two inventory logs were created
    expect(InventoryLog::count())->toBe(2);

    // Check first material log
    $log1 = InventoryLog::where('material_id', $this->material->id)->first();
    expect($log1->action)->toBe('belanja')
        ->and((float) $log1->quantity_change)->toBe(10.0);

    // Check second material log
    $log2 = InventoryLog::where('material_id', $material2->id)->first();
    expect($log2->action)->toBe('belanja')
        ->and((float) $log2->quantity_change)->toBe(5.0);
});

test('finish expense updates material batch and creates correct inventory log', function () {
    // Create expense
    $expense = Expense::create([
        'supplier_id' => $this->supplier->id,
        'expense_number' => 'BP-251206-0003',
        'expense_date' => now(),
        'grand_total_expect' => 100000,
        'grand_total_actual' => 0,
        'status' => 'Rencana',
        'is_start' => true,
        'is_finish' => false,
    ]);

    $expiryDate = now()->addMonths(6)->format('Y-m-d');

    // Create expense detail
    ExpenseDetail::create([
        'expense_id' => $expense->id,
        'material_id' => $this->material->id,
        'unit_id' => $this->unit->id,
        'quantity_expect' => 10,
        'quantity_get' => 10,
        'price_expect' => 10000,
        'price_actual' => 10000,
        'total_expect' => 100000,
        'total_actual' => 100000,
        'expiry_date' => $expiryDate,
    ]);

    // Finish expense - initialize component properly
    $component = new Rincian;
    $component->mount($expense->id);
    $component->finish();

    // Assert material batch was created
    expect(MaterialBatch::count())->toBe(1);

    $batch = MaterialBatch::first();
    expect($batch->material_id)->toEqual($this->material->id)
        ->and((float) $batch->batch_quantity)->toBe(10.0);

    // Assert inventory log references the correct batch
    $log = InventoryLog::first();
    expect($log->material_batch_id)->toEqual($batch->id)
        ->and((float) $log->quantity_after)->toBe((float) $batch->batch_quantity);
});
