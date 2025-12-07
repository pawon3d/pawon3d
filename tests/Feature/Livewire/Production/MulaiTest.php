<?php

declare(strict_types=1);

use App\Livewire\Production\Mulai;
use App\Models\InventoryLog;
use App\Models\Material;
use App\Models\MaterialBatch;
use App\Models\MaterialDetail;
use App\Models\Product;
use App\Models\ProductComposition;
use App\Models\Production;
use App\Models\ProductionDetail;
use App\Models\Unit;
use App\Models\User;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->user = User::factory()->create();

    // Create permission if not exists
    $permission = Permission::firstOrCreate(['name' => 'produksi.kelola']);
    $this->user->givePermissionTo($permission);

    $this->actingAs($this->user);

    // Create Unit
    $this->unit = Unit::firstOrCreate(['name' => 'Kilogram', 'alias' => 'kg']);

    // Create Material
    $this->material = Material::create([
        'name' => 'Tepung Terigu',
        'group' => 'persediaan',
        'description' => 'Bahan utama',
    ]);

    // Create MaterialDetail
    MaterialDetail::create([
        'material_id' => $this->material->id,
        'unit_id' => $this->unit->id,
        'base_quantity' => 1,
        'supply_quantity' => 0,
    ]);

    // Create MaterialBatch with sufficient stock
    $this->batch = MaterialBatch::create([
        'material_id' => $this->material->id,
        'unit_id' => $this->unit->id,
        'batch_number' => 'B-'.now()->format('ymd').'-001',
        'date' => now()->addMonths(6)->format('Y-m-d'),
        'batch_quantity' => 100,
    ]);

    // Create Product
    $this->product = Product::create([
        'name' => 'Roti Tawar',
        'pcs' => 10,
        'price' => 15000,
        'stock' => 0,
    ]);

    // Create ProductComposition
    ProductComposition::create([
        'product_id' => $this->product->id,
        'material_id' => $this->material->id,
        'unit_id' => $this->unit->id,
        'material_quantity' => 5,
    ]);

    // Create Production
    $this->production = Production::create([
        'production_number' => 'PR-'.now()->format('ymd').'-001',
        'production_date' => now(),
        'method' => 'siap-beli',
        'status' => 'Rencana',
        'is_start' => true,
        'is_finish' => false,
        'user_id' => $this->user->id,
    ]);

    // Create ProductionDetail
    $this->productionDetail = ProductionDetail::create([
        'production_id' => $this->production->id,
        'product_id' => $this->product->id,
        'quantity_plan' => 10,
        'quantity_get' => 0,
        'quantity_fail' => 0,
        'cycle' => 0,
    ]);
});

test('production save creates inventory log', function () {
    expect(InventoryLog::count())->toBe(0);

    // Initialize component
    $component = new Mulai;
    $component->mount($this->production->id);

    // Set production details with recipe quantity
    $component->production_details = [
        [
            'id' => $this->productionDetail->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'quantity_plan' => 10,
            'quantity_get' => 0,
            'cycle' => 0,
            'quantity_fail' => 0,
            'recipe_quantity' => '1',
            'quantity' => 10,
            'quantity_fail_raw' => 0,
        ],
    ];

    // Save production
    $component->save();

    // Assert inventory log was created
    expect(InventoryLog::count())->toBe(1);

    $log = InventoryLog::first();
    expect($log->material_id)->toEqual($this->material->id)
        ->and($log->action)->toBe('produksi')
        ->and((float) $log->quantity_change)->toBeLessThan(0)
        ->and($log->reference_type)->toBe('production')
        ->and($log->reference_id)->toEqual($this->production->id)
        ->and($log->user_id)->toEqual($this->user->id);
});

test('production save reduces material batch quantity', function () {
    // Initialize component
    $component = new Mulai;
    $component->mount($this->production->id);

    $initialQuantity = $this->batch->batch_quantity;

    // Set production details
    $component->production_details = [
        [
            'id' => $this->productionDetail->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'quantity_plan' => 10,
            'quantity_get' => 0,
            'cycle' => 0,
            'quantity_fail' => 0,
            'recipe_quantity' => '1',
            'quantity' => 10,
            'quantity_fail_raw' => 0,
        ],
    ];

    // Save production
    $component->save();

    // Refresh batch
    $this->batch->refresh();

    // Assert batch quantity decreased
    expect((float) $this->batch->batch_quantity)->toBeLessThan((float) $initialQuantity);

    // Verify inventory log quantity_after matches batch quantity
    $log = InventoryLog::first();
    expect((float) $log->quantity_after)->toBe((float) $this->batch->batch_quantity);
});

test('production save creates multiple inventory logs when using multiple batches', function () {
    // Create second batch
    $batch2 = MaterialBatch::create([
        'material_id' => $this->material->id,
        'unit_id' => $this->unit->id,
        'batch_number' => 'B-'.now()->format('ymd').'-002',
        'date' => now()->addMonths(3)->format('Y-m-d'),
        'batch_quantity' => 50,
    ]);

    expect(InventoryLog::count())->toBe(0);

    // Initialize component
    $component = new Mulai;
    $component->mount($this->production->id);

    // Set production details requiring more than one batch (150kg total, batches have 100kg + 50kg)
    $component->production_details = [
        [
            'id' => $this->productionDetail->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'quantity_plan' => 10,
            'quantity_get' => 0,
            'cycle' => 0,
            'quantity_fail' => 0,
            'recipe_quantity' => '20',
            'quantity' => 10,
            'quantity_fail_raw' => 0,
        ],
    ];

    // Save production
    $component->save();

    // Should create 2 inventory logs (one for each batch used)
    expect(InventoryLog::count())->toBe(2);

    $logs = InventoryLog::orderBy('created_at')->get();
    expect($logs->every(fn ($log) => $log->action === 'produksi'))->toBeTrue()
        ->and($logs->every(fn ($log) => $log->reference_type === 'production'))->toBeTrue()
        ->and($logs->every(fn ($log) => (float) $log->quantity_change < 0))->toBeTrue();
});
