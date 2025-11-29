<?php

declare(strict_types=1);

use App\Livewire\Hitung\Mulai;
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
    $permission = Permission::firstOrCreate(['name' => 'Inventori']);
    $this->user->givePermissionTo($permission);

    $this->actingAs($this->user);

    // Create Unit
    $this->unit = Unit::create([
        'name' => 'Kilogram',
        'alias' => 'kg',
    ]);

    // Create Material
    $this->material = Material::create([
        'name' => 'Tepung Terigu',
        'group' => 'persediaan',
    ]);

    // Create MaterialBatch
    $this->batch = MaterialBatch::create([
        'material_id' => $this->material->id,
        'batch_quantity' => 100,
        'unit_id' => $this->unit->id,
        'date' => now(),
    ]);
});

test('mulai page is accessible for hitung persediaan', function () {
    $hitung = Hitung::create([
        'hitung_number' => 'HC-'.now()->format('ymd').'-0001',
        'action' => 'Hitung Persediaan',
        'hitung_date' => now(),
        'status' => 'Sedang Diproses',
        'is_start' => true,
        'is_finish' => false,
        'grand_total' => 0,
        'user_id' => $this->user->id,
    ]);

    HitungDetail::create([
        'hitung_id' => $hitung->id,
        'material_id' => $this->material->id,
        'material_batch_id' => $this->batch->id,
        'quantity_expect' => 100,
        'quantity_actual' => 0,
        'total' => 100000,
        'loss_total' => 0,
    ]);

    $response = $this->get(route('hitung.mulai', $hitung->id));
    $response->assertStatus(200)
        ->assertSee('Hitung Persediaan')
        ->assertSee('Tepung Terigu');
});

test('mulai page is accessible for catat persediaan rusak', function () {
    $hitung = Hitung::create([
        'hitung_number' => 'HC-'.now()->format('ymd').'-0002',
        'action' => 'Catat Persediaan Rusak',
        'hitung_date' => now(),
        'status' => 'Sedang Diproses',
        'is_start' => true,
        'is_finish' => false,
        'grand_total' => 0,
        'user_id' => $this->user->id,
    ]);

    HitungDetail::create([
        'hitung_id' => $hitung->id,
        'material_id' => $this->material->id,
        'material_batch_id' => $this->batch->id,
        'quantity_expect' => 100,
        'quantity_actual' => 0,
        'total' => 100000,
        'loss_total' => 0,
    ]);

    $response = $this->get(route('hitung.mulai', $hitung->id));
    $response->assertStatus(200)
        ->assertSee('Catat Persediaan Rusak')
        ->assertSee('Tandai')
        ->assertSee('Rusak')
        ->assertSee('Semua');
});

test('mulai page is accessible for catat persediaan hilang', function () {
    $hitung = Hitung::create([
        'hitung_number' => 'HC-'.now()->format('ymd').'-0003',
        'action' => 'Catat Persediaan Hilang',
        'hitung_date' => now(),
        'status' => 'Sedang Diproses',
        'is_start' => true,
        'is_finish' => false,
        'grand_total' => 0,
        'user_id' => $this->user->id,
    ]);

    HitungDetail::create([
        'hitung_id' => $hitung->id,
        'material_id' => $this->material->id,
        'material_batch_id' => $this->batch->id,
        'quantity_expect' => 100,
        'quantity_actual' => 0,
        'total' => 100000,
        'loss_total' => 0,
    ]);

    $response = $this->get(route('hitung.mulai', $hitung->id));
    $response->assertStatus(200)
        ->assertSee('Catat Persediaan Hilang')
        ->assertSee('Tandai')
        ->assertSee('Hilang')
        ->assertSee('Semua');
});

test('can mark all as counted for hitung persediaan', function () {
    $hitung = Hitung::create([
        'hitung_number' => 'HC-'.now()->format('ymd').'-0004',
        'action' => 'Hitung Persediaan',
        'hitung_date' => now(),
        'status' => 'Sedang Diproses',
        'is_start' => true,
        'is_finish' => false,
        'grand_total' => 100000,
        'user_id' => $this->user->id,
    ]);

    HitungDetail::create([
        'hitung_id' => $hitung->id,
        'material_id' => $this->material->id,
        'material_batch_id' => $this->batch->id,
        'quantity_expect' => 100,
        'quantity_actual' => 0,
        'total' => 100000,
        'loss_total' => 0,
    ]);

    Livewire::test(Mulai::class, ['id' => $hitung->id])
        ->call('markAllAs')
        ->assertSet('hitungDetails.0.quantity_input', 100);
});

test('can save hitung persediaan with counted quantity', function () {
    $hitung = Hitung::create([
        'hitung_number' => 'HC-'.now()->format('ymd').'-0005',
        'action' => 'Hitung Persediaan',
        'hitung_date' => now(),
        'status' => 'Sedang Diproses',
        'is_start' => true,
        'is_finish' => false,
        'grand_total' => 100000,
        'user_id' => $this->user->id,
    ]);

    $detail = HitungDetail::create([
        'hitung_id' => $hitung->id,
        'material_id' => $this->material->id,
        'material_batch_id' => $this->batch->id,
        'quantity_expect' => 100,
        'quantity_actual' => 0,
        'total' => 100000,
        'loss_total' => 0,
    ]);

    Livewire::test(Mulai::class, ['id' => $hitung->id])
        ->set('hitungDetails.0.quantity_input', 95)
        ->call('save')
        ->assertRedirect(route('hitung.rincian', $hitung->id));

    // Verify quantity_actual is updated
    $detail->refresh();
    expect((float) $detail->quantity_actual)->toBe(95.0);
    expect((float) $detail->loss_total)->toBe(-5000.0); // (95 - 100) * 1000 per unit
});

test('can save catat persediaan rusak without updating batch quantity', function () {
    $hitung = Hitung::create([
        'hitung_number' => 'HC-'.now()->format('ymd').'-0006',
        'action' => 'Catat Persediaan Rusak',
        'hitung_date' => now(),
        'status' => 'Sedang Diproses',
        'is_start' => true,
        'is_finish' => false,
        'grand_total' => 100000,
        'user_id' => $this->user->id,
    ]);

    $detail = HitungDetail::create([
        'hitung_id' => $hitung->id,
        'material_id' => $this->material->id,
        'material_batch_id' => $this->batch->id,
        'quantity_expect' => 100,
        'quantity_actual' => 0,
        'total' => 100000,
        'loss_total' => 0,
    ]);

    Livewire::test(Mulai::class, ['id' => $hitung->id])
        ->set('hitungDetails.0.quantity_input', 10)
        ->call('save')
        ->assertRedirect(route('hitung.rincian', $hitung->id));

    // Verify quantity_actual and loss_total are updated
    $detail->refresh();
    expect((float) $detail->quantity_actual)->toBe(10.0);
    expect((float) $detail->loss_total)->toBe(10000.0); // 10 * 1000 per unit

    // Verify batch quantity is NOT reduced yet (only reduced on finish)
    $this->batch->refresh();
    expect((float) $this->batch->batch_quantity)->toBe(100.0);
});

test('validation prevents exceeding available quantity for rusak', function () {
    $hitung = Hitung::create([
        'hitung_number' => 'HC-'.now()->format('ymd').'-0007',
        'action' => 'Catat Persediaan Rusak',
        'hitung_date' => now(),
        'status' => 'Sedang Diproses',
        'is_start' => true,
        'is_finish' => false,
        'grand_total' => 100000,
        'user_id' => $this->user->id,
    ]);

    HitungDetail::create([
        'hitung_id' => $hitung->id,
        'material_id' => $this->material->id,
        'material_batch_id' => $this->batch->id,
        'quantity_expect' => 100,
        'quantity_actual' => 50, // Already recorded 50 as rusak
        'total' => 100000,
        'loss_total' => 50000,
    ]);

    $component = Livewire::test(Mulai::class, ['id' => $hitung->id])
        ->set('hitungDetails.0.quantity_input', 60); // 60 > 50 remaining

    // Error should be set
    expect($component->get('errorInputs'))->not->toBeEmpty();
});

test('riwayat pembaruan modal can be opened', function () {
    $hitung = Hitung::create([
        'hitung_number' => 'HC-'.now()->format('ymd').'-0008',
        'action' => 'Hitung Persediaan',
        'hitung_date' => now(),
        'status' => 'Sedang Diproses',
        'is_start' => true,
        'is_finish' => false,
        'grand_total' => 0,
        'user_id' => $this->user->id,
    ]);

    HitungDetail::create([
        'hitung_id' => $hitung->id,
        'material_id' => $this->material->id,
        'material_batch_id' => $this->batch->id,
        'quantity_expect' => 100,
        'quantity_actual' => 0,
        'total' => 100000,
        'loss_total' => 0,
    ]);

    Livewire::test(Mulai::class, ['id' => $hitung->id])
        ->call('riwayatPembaruan')
        ->assertSet('showHistoryModal', true);
});
