<?php

use App\Livewire\Alur\Index;
use App\Models\InventoryLog;
use App\Models\Material;
use App\Models\MaterialBatch;
use App\Models\Unit;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    // Create permission for alur persediaan
    $permission = Permission::firstOrCreate(['name' => 'inventori.alur.lihat', 'guard_name' => 'web']);

    $this->user = User::factory()->create();
    $this->user->givePermissionTo($permission);
});

test('alur persediaan page can be rendered', function () {
    $response = $this->actingAs($this->user)->get(route('alur-persediaan'));

    $response->assertStatus(200);
    $response->assertSeeLivewire(Index::class);
});

test('alur persediaan page shows correct title', function () {
    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->assertSee('Alur Persediaan');
});

test('alur persediaan page shows empty message when no data', function () {
    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->assertSee('Tidak ada riwayat persediaan yang tersedia.');
});

test('alur persediaan page displays inventory logs', function () {
    $unit = Unit::create([
        'name' => 'Kilogram',
        'alias' => 'kg',
        'group' => 'berat',
    ]);

    $material = Material::create([
        'name' => 'Tepung Terigu',
        'is_active' => true,
        'status' => 'tersedia',
    ]);

    $batch = MaterialBatch::create([
        'material_id' => $material->id,
        'unit_id' => $unit->id,
        'date' => now(),
        'batch_quantity' => 10,
    ]);

    InventoryLog::create([
        'material_id' => $material->id,
        'material_batch_id' => $batch->id,
        'user_id' => $this->user->id,
        'action' => 'belanja',
        'quantity_change' => 10,
        'quantity_after' => 10,
    ]);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->assertSee('Tepung Terigu')
        ->assertSee($batch->batch_number)
        ->assertSee('Belanja');
});

test('can search inventory logs by material name', function () {
    $unit = Unit::create([
        'name' => 'Gram',
        'alias' => 'g',
        'group' => 'berat',
    ]);

    $material1 = Material::create([
        'name' => 'Gula Pasir',
        'is_active' => true,
        'status' => 'tersedia',
    ]);

    $material2 = Material::create([
        'name' => 'Tepung Beras',
        'is_active' => true,
        'status' => 'tersedia',
    ]);

    $batch1 = MaterialBatch::create([
        'material_id' => $material1->id,
        'unit_id' => $unit->id,
        'batch_number' => 'B-251201',
        'date' => now(),
        'batch_quantity' => 500,
    ]);

    $batch2 = MaterialBatch::create([
        'material_id' => $material2->id,
        'unit_id' => $unit->id,
        'batch_number' => 'B-251202',
        'date' => now(),
        'batch_quantity' => 1000,
    ]);

    InventoryLog::create([
        'material_id' => $material1->id,
        'material_batch_id' => $batch1->id,
        'user_id' => $this->user->id,
        'action' => 'belanja',
        'quantity_change' => 500,
        'quantity_after' => 500,
    ]);

    InventoryLog::create([
        'material_id' => $material2->id,
        'material_batch_id' => $batch2->id,
        'user_id' => $this->user->id,
        'action' => 'terpakai',
        'quantity_change' => -200,
        'quantity_after' => 800,
    ]);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->set('search', 'Gula')
        ->assertSee('Gula Pasir')
        ->assertDontSee('Tepung Beras');
});

test('can filter inventory logs by action', function () {
    $unit = Unit::create([
        'name' => 'Gram',
        'alias' => 'g',
        'group' => 'berat',
    ]);

    $material1 = Material::create([
        'name' => 'Tepung Terigu',
        'is_active' => true,
        'status' => 'tersedia',
    ]);

    $material2 = Material::create([
        'name' => 'Gula Pasir',
        'is_active' => true,
        'status' => 'tersedia',
    ]);

    $batch1 = MaterialBatch::create([
        'material_id' => $material1->id,
        'unit_id' => $unit->id,
        'date' => now(),
        'batch_quantity' => 1000,
    ]);

    $batch2 = MaterialBatch::create([
        'material_id' => $material2->id,
        'unit_id' => $unit->id,
        'date' => now(),
        'batch_quantity' => 500,
    ]);

    InventoryLog::create([
        'material_id' => $material1->id,
        'material_batch_id' => $batch1->id,
        'user_id' => $this->user->id,
        'action' => 'belanja',
        'quantity_change' => 1000,
        'quantity_after' => 1000,
    ]);

    InventoryLog::create([
        'material_id' => $material2->id,
        'material_batch_id' => $batch2->id,
        'user_id' => $this->user->id,
        'action' => 'rusak',
        'quantity_change' => -100,
        'quantity_after' => 400,
    ]);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->set('filterAction', 'rusak')
        ->assertSee('Gula Pasir')
        ->assertDontSee('Tepung Terigu');
});
