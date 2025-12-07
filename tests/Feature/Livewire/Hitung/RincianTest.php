<?php

declare(strict_types=1);

use App\Livewire\Hitung\Rincian;
use App\Models\Hitung;
use App\Models\HitungDetail;
use App\Models\Material;
use App\Models\MaterialBatch;
use App\Models\Unit;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->user = User::factory()->create();

    // Create permission if not exists
    $permission = Permission::firstOrCreate(['name' => 'inventori.hitung.kelola']);
    $this->user->givePermissionTo($permission);

    $this->actingAs($this->user);

    // Setup material and batch for finish tests
    $this->unit = Unit::firstOrCreate(['name' => 'Kilogram', 'alias' => 'kg']);
    $this->material = Material::create([
        'name' => 'Test Material',
        'description' => 'Test',
        'is_piority' => false,
    ]);
    $this->batch = MaterialBatch::create([
        'material_id' => $this->material->id,
        'unit_id' => $this->unit->id,
        'batch_number' => 'B-'.now()->format('ymd'),
        'date' => now(),
        'batch_quantity' => 100,
    ]);
});

test('rincian page is accessible', function () {
    $hitung = Hitung::create([
        'hitung_number' => 'HP-'.now()->format('ymd').'-0001',
        'action' => 'Hitung Persediaan',
        'hitung_date' => now(),
        'note' => 'Test note',
        'status' => 'Belum Diproses',
        'is_start' => false,
        'is_finish' => false,
        'grand_total' => 0,
        'loss_grand_total' => 0,
        'user_id' => $this->user->id,
    ]);

    $response = $this->get(route('hitung.rincian', $hitung->id));
    $response->assertStatus(200);
});

test('rincian shows hitung persediaan data', function () {
    $hitung = Hitung::create([
        'hitung_number' => 'HP-'.now()->format('ymd').'-0002',
        'action' => 'Hitung Persediaan',
        'hitung_date' => now(),
        'note' => 'Rencana hitung test',
        'status' => 'Belum Diproses',
        'is_start' => false,
        'is_finish' => false,
        'grand_total' => 100000,
        'loss_grand_total' => 0,
        'user_id' => $this->user->id,
    ]);

    $response = $this->get(route('hitung.rincian', $hitung->id));
    $response->assertStatus(200)
        ->assertSee($hitung->hitung_number)
        ->assertSee('Hitung Persediaan');
});

test('rincian shows catat persediaan rusak data', function () {
    $hitung = Hitung::create([
        'hitung_number' => 'CPR-'.now()->format('ymd').'-0001',
        'action' => 'Catat Persediaan Rusak',
        'hitung_date' => now(),
        'note' => 'Barang rusak',
        'status' => 'Belum Diproses',
        'is_start' => false,
        'is_finish' => false,
        'grand_total' => 50000,
        'loss_grand_total' => 0,
        'user_id' => $this->user->id,
    ]);

    $response = $this->get(route('hitung.rincian', $hitung->id));
    $response->assertStatus(200)
        ->assertSee($hitung->hitung_number)
        ->assertSee('Catat Persediaan Rusak');
});

test('rincian shows catat persediaan hilang data', function () {
    $hitung = Hitung::create([
        'hitung_number' => 'CPH-'.now()->format('ymd').'-0001',
        'action' => 'Catat Persediaan Hilang',
        'hitung_date' => now(),
        'note' => 'Barang hilang',
        'status' => 'Belum Diproses',
        'is_start' => false,
        'is_finish' => false,
        'grand_total' => 75000,
        'loss_grand_total' => 0,
        'user_id' => $this->user->id,
    ]);

    $response = $this->get(route('hitung.rincian', $hitung->id));
    $response->assertStatus(200)
        ->assertSee($hitung->hitung_number)
        ->assertSee('Catat Persediaan Hilang');
});

test('finish hitung persediaan updates batch quantity to actual count', function () {
    $hitung = Hitung::create([
        'hitung_number' => 'HP-'.now()->format('ymd').'-0010',
        'action' => 'Hitung Persediaan',
        'hitung_date' => now(),
        'status' => 'Sedang Diproses',
        'is_start' => true,
        'is_finish' => false,
        'grand_total' => 100000,
        'loss_grand_total' => -5000,
        'user_id' => $this->user->id,
    ]);

    // Detail dengan quantity_actual = 95 (kurang 5 dari expected 100)
    HitungDetail::create([
        'hitung_id' => $hitung->id,
        'material_id' => $this->material->id,
        'material_batch_id' => $this->batch->id,
        'quantity_expect' => 100,
        'quantity_actual' => 95,
        'total' => 100000,
        'loss_total' => -5000,
    ]);

    Livewire::test(Rincian::class, ['id' => $hitung->id])
        ->call('finish');

    // Verify batch quantity is updated to actual count (95)
    $this->batch->refresh();
    expect((float) $this->batch->batch_quantity)->toBe(95.0);

    // Verify hitung status
    $hitung->refresh();
    expect((bool) $hitung->is_finish)->toBeTrue();
    expect($hitung->status)->toBe('Selesai');
});

test('finish catat rusak reduces batch quantity by damaged amount', function () {
    $hitung = Hitung::create([
        'hitung_number' => 'CPR-'.now()->format('ymd').'-0010',
        'action' => 'Catat Persediaan Rusak',
        'hitung_date' => now(),
        'status' => 'Sedang Diproses',
        'is_start' => true,
        'is_finish' => false,
        'grand_total' => 100000,
        'loss_grand_total' => 10000,
        'user_id' => $this->user->id,
    ]);

    // Detail dengan quantity_actual = 10 (10 barang rusak)
    HitungDetail::create([
        'hitung_id' => $hitung->id,
        'material_id' => $this->material->id,
        'material_batch_id' => $this->batch->id,
        'quantity_expect' => 100,
        'quantity_actual' => 10,
        'total' => 100000,
        'loss_total' => 10000,
    ]);

    Livewire::test(Rincian::class, ['id' => $hitung->id])
        ->call('finish');

    // Verify batch quantity is reduced by damaged amount (100 - 10 = 90)
    $this->batch->refresh();
    expect((float) $this->batch->batch_quantity)->toBe(90.0);

    // Verify hitung status
    $hitung->refresh();
    expect((bool) $hitung->is_finish)->toBeTrue();
    expect($hitung->status)->toBe('Selesai');
});

test('finish catat hilang reduces batch quantity by lost amount', function () {
    $hitung = Hitung::create([
        'hitung_number' => 'CPH-'.now()->format('ymd').'-0010',
        'action' => 'Catat Persediaan Hilang',
        'hitung_date' => now(),
        'status' => 'Sedang Diproses',
        'is_start' => true,
        'is_finish' => false,
        'grand_total' => 100000,
        'loss_grand_total' => 20000,
        'user_id' => $this->user->id,
    ]);

    // Detail dengan quantity_actual = 20 (20 barang hilang)
    HitungDetail::create([
        'hitung_id' => $hitung->id,
        'material_id' => $this->material->id,
        'material_batch_id' => $this->batch->id,
        'quantity_expect' => 100,
        'quantity_actual' => 20,
        'total' => 100000,
        'loss_total' => 20000,
    ]);

    Livewire::test(Rincian::class, ['id' => $hitung->id])
        ->call('finish');

    // Verify batch quantity is reduced by lost amount (100 - 20 = 80)
    $this->batch->refresh();
    expect((float) $this->batch->batch_quantity)->toBe(80.0);

    // Verify hitung status
    $hitung->refresh();
    expect((bool) $hitung->is_finish)->toBeTrue();
    expect($hitung->status)->toBe('Selesai');
});

test('finish catat rusak deletes expired batch when quantity becomes zero', function () {
    // Create material for expired batch
    $material = Material::create([
        'name' => 'Material With Expired Batch',
        'description' => 'Test',
        'is_piority' => false,
    ]);

    // Batch dengan date di masa lalu (expired)
    $expiredBatch = MaterialBatch::create([
        'material_id' => $material->id,
        'unit_id' => $this->unit->id,
        'batch_number' => 'B-EXP-'.now()->format('ymd'),
        'date' => now()->subDays(10), // Batch date 10 hari lalu (expired)
        'batch_quantity' => 50,
    ]);

    $hitung = Hitung::create([
        'hitung_number' => 'CPR-'.now()->format('ymd').'-0020',
        'action' => 'Catat Persediaan Rusak',
        'hitung_date' => now(),
        'status' => 'Sedang Diproses',
        'is_start' => true,
        'is_finish' => false,
        'grand_total' => 50000,
        'loss_grand_total' => 50000,
        'user_id' => $this->user->id,
    ]);

    // Detail: semua 50 barang rusak (quantity menjadi 0)
    HitungDetail::create([
        'hitung_id' => $hitung->id,
        'material_id' => $material->id,
        'material_batch_id' => $expiredBatch->id,
        'quantity_expect' => 50,
        'quantity_actual' => 50,
        'total' => 50000,
        'loss_total' => 50000,
    ]);

    $batchId = $expiredBatch->id;

    Livewire::test(Rincian::class, ['id' => $hitung->id])
        ->call('finish');

    // Verify batch is deleted because batch date is in the past and quantity is 0
    expect(MaterialBatch::find($batchId))->toBeNull();

    // Verify hitung status
    $hitung->refresh();
    expect((bool) $hitung->is_finish)->toBeTrue();
});

test('finish catat rusak keeps non-expired batch when quantity becomes zero', function () {
    // Create material for non-expired batch
    $material = Material::create([
        'name' => 'Material With Fresh Batch',
        'description' => 'Test',
        'is_piority' => false,
    ]);

    // Batch dengan date hari ini atau masa depan (belum expired)
    $nonExpiredBatch = MaterialBatch::create([
        'material_id' => $material->id,
        'unit_id' => $this->unit->id,
        'batch_number' => 'B-NEXP-'.now()->format('ymd'),
        'date' => now()->addDays(5), // Batch date 5 hari ke depan (belum expired)
        'batch_quantity' => 30,
    ]);

    $hitung = Hitung::create([
        'hitung_number' => 'CPR-'.now()->format('ymd').'-0021',
        'action' => 'Catat Persediaan Rusak',
        'hitung_date' => now(),
        'status' => 'Sedang Diproses',
        'is_start' => true,
        'is_finish' => false,
        'grand_total' => 30000,
        'loss_grand_total' => 30000,
        'user_id' => $this->user->id,
    ]);

    // Detail: semua 30 barang rusak (quantity menjadi 0)
    HitungDetail::create([
        'hitung_id' => $hitung->id,
        'material_id' => $material->id,
        'material_batch_id' => $nonExpiredBatch->id,
        'quantity_expect' => 30,
        'quantity_actual' => 30,
        'total' => 30000,
        'loss_total' => 30000,
    ]);

    $batchId = $nonExpiredBatch->id;

    Livewire::test(Rincian::class, ['id' => $hitung->id])
        ->call('finish');

    // Verify batch is NOT deleted because batch date is in the future
    $nonExpiredBatch->refresh();
    expect(MaterialBatch::find($batchId))->not->toBeNull();
    expect((float) $nonExpiredBatch->batch_quantity)->toBe(0.0);
});

test('finish hitung persediaan creates inventory log', function () {
    $hitung = Hitung::create([
        'hitung_number' => 'HP-'.now()->format('ymd').'-0030',
        'action' => 'Hitung Persediaan',
        'hitung_date' => now(),
        'status' => 'Sedang Diproses',
        'is_start' => true,
        'is_finish' => false,
        'grand_total' => 50000,
        'loss_grand_total' => 0,
        'user_id' => $this->user->id,
    ]);

    HitungDetail::create([
        'hitung_id' => $hitung->id,
        'material_id' => $this->material->id,
        'material_batch_id' => $this->batch->id,
        'quantity_expect' => 100,
        'quantity_actual' => 95,
        'total' => 50000,
        'loss_total' => 0,
    ]);

    expect(\App\Models\InventoryLog::count())->toBe(0);

    Livewire::test(Rincian::class, ['id' => $hitung->id])
        ->call('finish');

    expect(\App\Models\InventoryLog::count())->toBe(1);

    $log = \App\Models\InventoryLog::first();
    expect($log->action)->toBe('hitung')
        ->and((float) $log->quantity_change)->toBe(-5.0)
        ->and((float) $log->quantity_after)->toBe(95.0)
        ->and($log->reference_type)->toBe('hitung')
        ->and($log->reference_id)->toEqual($hitung->id);
});

test('finish catat rusak creates inventory log', function () {
    $hitung = Hitung::create([
        'hitung_number' => 'CPR-'.now()->format('ymd').'-0031',
        'action' => 'Catat Persediaan Rusak',
        'hitung_date' => now(),
        'status' => 'Sedang Diproses',
        'is_start' => true,
        'is_finish' => false,
        'grand_total' => 10000,
        'loss_grand_total' => 10000,
        'user_id' => $this->user->id,
    ]);

    HitungDetail::create([
        'hitung_id' => $hitung->id,
        'material_id' => $this->material->id,
        'material_batch_id' => $this->batch->id,
        'quantity_expect' => 100,
        'quantity_actual' => 10,
        'total' => 10000,
        'loss_total' => 10000,
    ]);

    expect(\App\Models\InventoryLog::count())->toBe(0);

    Livewire::test(Rincian::class, ['id' => $hitung->id])
        ->call('finish');

    expect(\App\Models\InventoryLog::count())->toBe(1);

    $log = \App\Models\InventoryLog::first();
    expect($log->action)->toBe('rusak')
        ->and((float) $log->quantity_change)->toBe(-10.0)
        ->and((float) $log->quantity_after)->toBe(90.0)
        ->and($log->reference_type)->toBe('hitung')
        ->and($log->reference_id)->toEqual($hitung->id);
});

test('finish catat hilang creates inventory log', function () {
    $hitung = Hitung::create([
        'hitung_number' => 'CPH-'.now()->format('ymd').'-0032',
        'action' => 'Catat Persediaan Hilang',
        'hitung_date' => now(),
        'status' => 'Sedang Diproses',
        'is_start' => true,
        'is_finish' => false,
        'grand_total' => 5000,
        'loss_grand_total' => 5000,
        'user_id' => $this->user->id,
    ]);

    HitungDetail::create([
        'hitung_id' => $hitung->id,
        'material_id' => $this->material->id,
        'material_batch_id' => $this->batch->id,
        'quantity_expect' => 100,
        'quantity_actual' => 5,
        'total' => 5000,
        'loss_total' => 5000,
    ]);

    expect(\App\Models\InventoryLog::count())->toBe(0);

    Livewire::test(Rincian::class, ['id' => $hitung->id])
        ->call('finish');

    expect(\App\Models\InventoryLog::count())->toBe(1);

    $log = \App\Models\InventoryLog::first();
    expect($log->action)->toBe('hilang')
        ->and((float) $log->quantity_change)->toBe(-5.0)
        ->and((float) $log->quantity_after)->toBe(95.0)
        ->and($log->reference_type)->toBe('hitung')
        ->and($log->reference_id)->toEqual($hitung->id);
});
