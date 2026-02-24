<?php

declare(strict_types=1);

use App\Livewire\Production\Index;
use App\Livewire\Production\Mulai;
use App\Livewire\Production\Rincian;
use App\Livewire\Production\RincianSiapBeli;
use App\Livewire\Production\Riwayat;
use App\Livewire\Production\TambahSiapBeli;
use App\Models\Material;
use App\Models\MaterialBatch;
use App\Models\Product;
use App\Models\ProductComposition;
use App\Models\Production;
use App\Models\ProductionDetail;
use App\Models\Unit;
use App\Models\User;
use Spatie\Permission\Models\Permission;

// --- Helpers ---

function makeProductionFixture(): array
{
    $unit = Unit::firstOrCreate(
        ['name' => 'Kilogram', 'alias' => 'kg'],
        ['group' => 'Massa']
    );

    $material = Material::create([
        'name' => 'Tepung Terigu Test',
        'status' => 'Kosong',
        'minimum' => 1,
        'is_active' => true,
    ]);

    $product = Product::create([
        'name' => 'Brownies Test',
        'pcs' => 8,
        'price' => 50000,
        'stock' => 0,
        'is_active' => true,
        'is_recipe' => true,
        'method' => json_encode(['siap-beli']),
    ]);

    ProductComposition::create([
        'product_id' => $product->id,
        'material_id' => $material->id,
        'unit_id' => $unit->id,
        'material_quantity' => 0.5,
    ]);

    return compact('unit', 'material', 'product');
}

function makeBatch(mixed $materialId, mixed $unitId, float $qty): MaterialBatch
{
    static $seq = 0;
    $seq++;

    return MaterialBatch::create([
        'material_id' => $materialId,
        'unit_id' => $unitId,
        'batch_number' => 'B-'.now()->format('ymd').'-'.str_pad((string) $seq, 3, '0', STR_PAD_LEFT),
        'date' => now()->addMonths(6)->format('Y-m-d'),
        'batch_quantity' => $qty,
    ]);
}

function makeProduction(mixed $productId, int $quantityPlan = 10, string $status = 'Sedang Diproses'): array
{
    $production = Production::create([
        'method' => 'siap-beli',
        'status' => $status,
        'is_start' => true,
        'is_finish' => false,
    ]);

    $detail = ProductionDetail::create([
        'production_id' => $production->id,
        'product_id' => $productId,
        'quantity_plan' => $quantityPlan,
        'quantity_get' => 0,
        'quantity_fail' => 0,
        'cycle' => 0,
    ]);

    return compact('production', 'detail');
}

// --- Setup ---

beforeEach(function () {
    $this->user = User::factory()->create();

    foreach (['produksi.rencana.kelola', 'produksi.mulai', 'produksi.laporan.kelola'] as $perm) {
        $p = Permission::firstOrCreate(['name' => $perm]);
        $this->user->givePermissionTo($p);
    }

    $this->actingAs($this->user);
});

// --- TC-054: Access production index ---

test('TC-054: authenticated user with permission can access production page', function () {
    $this->get(route('produksi'))
        ->assertStatus(200)
        ->assertSeeLivewire(Index::class);
});

// --- TC-055: Access antrian produksi ---

test('TC-055: authenticated user can access antrian produksi page', function () {
    $this->get(route('produksi.antrian-produksi'))
        ->assertStatus(200);
});

// --- TC-057: Start production with sufficient materials ---

test('TC-057: save production with sufficient materials deducts stock', function () {
    ['unit' => $unit, 'material' => $material, 'product' => $product] = makeProductionFixture();

    makeBatch($material->id, $unit->id, 10.0);

    ['production' => $production, 'detail' => $detail] = makeProduction($product->id, 8);

    $batchBefore = MaterialBatch::where('material_id', $material->id)->first()->batch_quantity;

    Livewire::test(Mulai::class, ['id' => $production->id])
        ->set('production_details', [[
            'id' => (string) $detail->id,
            'product_id' => (string) $product->id,
            'product_name' => $product->name,
            'quantity_plan' => 8,
            'quantity_get' => 0,
            'cycle' => 0,
            'quantity_fail' => 0,
            'recipe_quantity' => '1',
            'quantity' => $product->pcs,
            'quantity_fail_raw' => 0,
        ]])
        ->call('save');

    $batchAfter = MaterialBatch::where('material_id', $material->id)->first()->batch_quantity;
    expect((float) $batchAfter)->toBeLessThan((float) $batchBefore);
});

// --- TC-058: Start production with insufficient materials ---

test('TC-058: save production with insufficient materials does not deduct stock', function () {
    ['unit' => $unit, 'material' => $material, 'product' => $product] = makeProductionFixture();

    makeBatch($material->id, $unit->id, 0.1);

    ['production' => $production, 'detail' => $detail] = makeProduction($product->id, 80);

    $batchBefore = MaterialBatch::where('material_id', $material->id)->first()->batch_quantity;

    Livewire::test(Mulai::class, ['id' => $production->id])
        ->set('production_details', [[
            'id' => (string) $detail->id,
            'product_id' => (string) $product->id,
            'product_name' => $product->name,
            'quantity_plan' => 80,
            'quantity_get' => 0,
            'cycle' => 0,
            'quantity_fail' => 0,
            'recipe_quantity' => '10',
            'quantity' => 80,
            'quantity_fail_raw' => 0,
        ]])
        ->call('save');

    $batchAfter = MaterialBatch::where('material_id', $material->id)->first()->batch_quantity;
    expect((float) $batchAfter)->toBe((float) $batchBefore);
});

// --- TC-059: Complete production (finish) ---

test('TC-059: finish production sets status to Selesai', function () {
    ['product' => $product] = makeProductionFixture();
    ['production' => $production] = makeProduction($product->id, 10);

    Livewire::test(Rincian::class, ['id' => $production->id])
        ->call('finish');

    expect(Production::find($production->id)->status)->toBe('Selesai');
});

// --- TC-060: Create siap-beli production with valid data ---

test('TC-060: store siap-beli production with valid data redirects to antrian', function () {
    ['unit' => $unit, 'material' => $material, 'product' => $product] = makeProductionFixture();
    makeBatch($material->id, $unit->id, 100.0);

    Livewire::test(TambahSiapBeli::class)
        ->set('start_date', now()->addDay()->format('d/m/Y'))
        ->set('time', '08:00')
        ->set('note', 'Produksi harian')
        ->set('production_details', [[
            'product_id' => (string) $product->id,
            'product_name' => $product->name,
            'quantity_plan' => 10,
            'current_stock' => 0,
            'suggested_amount' => 5,
        ]])
        ->call('store')
        ->assertRedirect(route('produksi.antrian-produksi'));
});

// --- TC-061: Siap-beli production with insufficient materials ---

test('TC-061: store siap-beli production with insufficient materials does not create production', function () {
    ['unit' => $unit, 'material' => $material, 'product' => $product] = makeProductionFixture();
    makeBatch($material->id, $unit->id, 0.0);

    Livewire::test(TambahSiapBeli::class)
        ->set('start_date', now()->addDay()->format('d/m/Y'))
        ->set('time', '08:00')
        ->set('production_details', [[
            'product_id' => (string) $product->id,
            'product_name' => $product->name,
            'quantity_plan' => 100,
            'current_stock' => 0,
            'suggested_amount' => 0,
        ]])
        ->call('store');

    expect(Production::count())->toBe(0);
});

// --- TC-062: Siap-beli with quantity_plan below minimum ---

test('TC-062: store siap-beli production with quantity_plan = 0 fails validation', function () {
    ['product' => $product] = makeProductionFixture();

    Livewire::test(TambahSiapBeli::class)
        ->set('start_date', now()->addDay()->format('d/m/Y'))
        ->set('time', '08:00')
        ->set('production_details', [[
            'product_id' => (string) $product->id,
            'product_name' => $product->name,
            'quantity_plan' => 0,
            'current_stock' => 0,
            'suggested_amount' => 0,
        ]])
        ->call('store')
        ->assertHasErrors(['production_details.0.quantity_plan']);
});

// --- TC-063: Complete siap-beli production ---

test('TC-063: selesaikanProduksi sets status to Selesai', function () {
    ['product' => $product] = makeProductionFixture();

    $production = Production::create([
        'method' => 'siap-beli',
        'status' => 'Sedang Diproses',
        'is_start' => true,
        'is_finish' => false,
    ]);

    ProductionDetail::create([
        'production_id' => $production->id,
        'product_id' => $product->id,
        'quantity_plan' => 10,
        'quantity_get' => 10,
        'quantity_fail' => 0,
        'cycle' => 0,
    ]);

    Livewire::test(RincianSiapBeli::class, ['id' => $production->id])
        ->call('selesaikanProduksi')
        ->assertRedirect();

    expect(Production::find($production->id)->status)->toBe('Selesai');
});

// --- TC-064: Access production history ---

test('TC-064: user can access production riwayat page', function () {
    $this->get(route('produksi.riwayat', ['method' => 'siap-beli']))
        ->assertStatus(200)
        ->assertSeeLivewire(Riwayat::class);
});

// --- Security ---

test('unauthenticated user cannot access production pages', function () {
    auth()->logout();

    $this->get(route('produksi'))->assertRedirect(route('login'));
});

test('user without permission cannot store production', function () {
    $noPermUser = User::factory()->create();
    $this->actingAs($noPermUser);

    ['product' => $product] = makeProductionFixture();

    Livewire::test(TambahSiapBeli::class)
        ->set('start_date', now()->addDay()->format('d/m/Y'))
        ->set('time', '08:00')
        ->set('production_details', [[
            'product_id' => (string) $product->id,
            'product_name' => $product->name,
            'quantity_plan' => 5,
            'current_stock' => 0,
            'suggested_amount' => 0,
        ]])
        ->call('store')
        ->assertForbidden();
});
