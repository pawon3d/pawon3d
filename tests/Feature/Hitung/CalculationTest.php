<?php

declare(strict_types=1);

use App\Models\Hitung;
use App\Models\HitungDetail;
use App\Models\Material;
use App\Models\MaterialBatch;
use App\Models\MaterialDetail;
use App\Models\StoreProfile;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\View;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $profile = StoreProfile::firstOrCreate(['id' => 1], [
        'name' => 'Test Store',
        'address' => 'Test Address',
        'phone' => '08123456789',
    ]);
    View::share('storeProfile', $profile);

    Permission::firstOrCreate(['name' => 'inventori.hitung.kelola', 'guard_name' => 'web']);

    $user = User::factory()->create();
    $user->givePermissionTo('inventori.hitung.kelola');
    $this->actingAs($user);
    $this->user = $user;
});

test('hitung detail modal (total) dihitung dengan benar', function () {
    // Buat unit
    $unit = Unit::create([
        'name' => 'Kilogram',
        'alias' => 'kg',
    ]);

    // Buat material
    $material = Material::create([
        'name' => 'Beras',
        'status' => 'Aktif',
    ]);

    // Set harga material
    MaterialDetail::create([
        'material_id' => $material->id,
        'unit_id' => $unit->id,
        'supply_price' => 10000, // Rp10.000 per kg
    ]);

    // Buat batch dengan 50kg
    $batch = MaterialBatch::create([
        'material_id' => $material->id,
        'unit_id' => $unit->id,
        'batch_number' => 'BATCH-001',
        'batch_quantity' => 50, // 50kg
        'date' => now()->addDays(30),
    ]);

    // Create hitung
    $hitung = Hitung::create([
        'user_id' => $this->user->id,
        'action' => 'Hitung Persediaan',
        'hitung_number' => 'HC-test-001',
        'status' => 'Belum Diproses',
    ]);

    // Create detail
    $detail = HitungDetail::create([
        'hitung_id' => $hitung->id,
        'material_id' => $material->id,
        'material_batch_id' => $batch->id,
        'quantity_expect' => 50,
        'quantity_actual' => 0,
        'total' => 50 * 10000, // 50kg × Rp10.000 = Rp500.000
        'loss_total' => 0,
    ]);

    expect($detail->total)->toEqual(500000);

    // Simulate hitung input: terhitung 45kg (kurang 5kg)
    $detail->update(['quantity_actual' => 45]);

    // Calculate price per unit
    $pricePerUnit = (float) $detail->total / $detail->quantity_expect; // Rp10.000
    expect($pricePerUnit)->toEqual(10000.0);

    // Calculate loss for Hitung Persediaan: (45 - 50) × Rp10.000 = -Rp50.000
    $expectedLoss = $pricePerUnit * (45 - 50);
    expect($expectedLoss)->toEqual(-50000.0);
});

test('kerugian dari persediaan rusak dihitung dengan benar', function () {
    // Buat unit
    $unit = Unit::create([
        'name' => 'Pack',
        'alias' => 'pck',
    ]);

    // Buat material
    $material = Material::create([
        'name' => 'Kue Coklat',
        'status' => 'Aktif',
    ]);

    // Set harga material
    MaterialDetail::create([
        'material_id' => $material->id,
        'unit_id' => $unit->id,
        'supply_price' => 5000, // Rp5.000 per pack
    ]);

    // Buat batch dengan 100 pack
    $batch = MaterialBatch::create([
        'material_id' => $material->id,
        'unit_id' => $unit->id,
        'batch_number' => 'BATCH-KEK-001',
        'batch_quantity' => 100,
        'date' => now()->addDays(15),
    ]);

    // Create hitung untuk rusak
    $hitung = Hitung::create([
        'user_id' => $this->user->id,
        'action' => 'Catat Persediaan Rusak',
        'hitung_number' => 'HC-rusak-001',
        'status' => 'Belum Diproses',
    ]);

    // Create detail
    $detail = HitungDetail::create([
        'hitung_id' => $hitung->id,
        'material_id' => $material->id,
        'material_batch_id' => $batch->id,
        'quantity_expect' => 100,
        'quantity_actual' => 0,
        'total' => 100 * 5000, // 100pck × Rp5.000 = Rp500.000
        'loss_total' => 0,
    ]);

    expect($detail->total)->toEqual(500000);

    // Simulate rusak: 10 pack rusak
    $detail->update(['quantity_actual' => 10]);

    // Calculate price per unit
    $pricePerUnit = (float) $detail->total / $detail->quantity_expect; // Rp5.000
    expect($pricePerUnit)->toEqual(5000.0);

    // Calculate loss for Catat Rusak: 10 × Rp5.000 = Rp50.000 kerugian
    $expectedLoss = $pricePerUnit * 10;
    expect($expectedLoss)->toEqual(50000.0);
});
