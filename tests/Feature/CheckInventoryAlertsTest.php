<?php

declare(strict_types=1);

use App\Models\Material;
use App\Models\MaterialBatch;
use App\Models\Notification;
use App\Models\Unit;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->unit = Unit::firstOrCreate(['name' => 'Kilogram', 'alias' => 'kg']);

    // Setup user with inventori permission so notifications can be created
    $permission = Permission::firstOrCreate(['name' => 'inventori.persediaan.kelola', 'guard_name' => 'web']);
    $role = Role::firstOrCreate(['name' => 'Inventori Alert Test', 'guard_name' => 'web']);
    $role->givePermissionTo($permission);
    $this->user = \App\Models\User::factory()->create();
    $this->user->assignRole($role);
});

// ── BAGIAN 4: Material::recalculateStatus() ──────────────────────────────────

test('recalculateStatus marks Expired only when ALL active batches are expired', function () {
    $material = Material::create(['name' => 'Bahan Expired Semua', 'is_piority' => false]);

    // Semua batch sudah habis tanggalnya
    MaterialBatch::create([
        'material_id' => $material->id,
        'unit_id' => $this->unit->id,
        'date' => now()->subDays(5)->format('Y-m-d'),
        'batch_quantity' => 10,
    ]);

    MaterialBatch::create([
        'material_id' => $material->id,
        'unit_id' => $this->unit->id,
        'date' => now()->subDays(2)->format('Y-m-d'),
        'batch_quantity' => 5,
    ]);

    $material->recalculateStatus();

    expect($material->fresh()->status)->toBe('Expired');
});

test('recalculateStatus does NOT mark Expired when at least one valid batch exists', function () {
    $material = Material::create(['name' => 'Bahan Masih Ada Stok Valid', 'is_piority' => false]);

    // Satu batch expired
    MaterialBatch::create([
        'material_id' => $material->id,
        'unit_id' => $this->unit->id,
        'date' => now()->subDays(3)->format('Y-m-d'),
        'batch_quantity' => 5,
    ]);

    // Satu batch masih valid
    MaterialBatch::create([
        'material_id' => $material->id,
        'unit_id' => $this->unit->id,
        'date' => now()->addDays(10)->format('Y-m-d'),
        'batch_quantity' => 20,
    ]);

    $material->recalculateStatus();

    expect($material->fresh()->status)->not->toBe('Expired');
});

test('recalculateStatus marks Kosong when all batches have zero quantity', function () {
    $material = Material::create(['name' => 'Bahan Kosong', 'is_piority' => false]);

    MaterialBatch::create([
        'material_id' => $material->id,
        'unit_id' => $this->unit->id,
        'date' => now()->addDays(5)->format('Y-m-d'),
        'batch_quantity' => 0,
    ]);

    $material->recalculateStatus();

    expect($material->fresh()->status)->toBe('Kosong');
});

// ── BAGIAN 5: inventory:check-alerts command ─────────────────────────────────

test('command updates status to Expired only when all active batches are expired', function () {
    $material = Material::create(['name' => 'Bahan Command Expired', 'status' => 'Tersedia', 'is_piority' => false]);

    MaterialBatch::create([
        'material_id' => $material->id,
        'unit_id' => $this->unit->id,
        'date' => now()->subDays(1)->format('Y-m-d'),
        'batch_quantity' => 15,
    ]);

    $this->artisan('inventory:check-alerts')->assertSuccessful();

    expect($material->fresh()->status)->toBe('Expired');
});

test('command does NOT mark Expired when material still has a valid batch', function () {
    $material = Material::create(['name' => 'Bahan Command Mixed', 'status' => 'Tersedia', 'is_piority' => false]);

    // Batch expired dengan stok
    MaterialBatch::create([
        'material_id' => $material->id,
        'unit_id' => $this->unit->id,
        'date' => now()->subDays(3)->format('Y-m-d'),
        'batch_quantity' => 5,
    ]);

    // Batch valid dengan stok
    MaterialBatch::create([
        'material_id' => $material->id,
        'unit_id' => $this->unit->id,
        'date' => now()->addDays(7)->format('Y-m-d'),
        'batch_quantity' => 30,
    ]);

    $this->artisan('inventory:check-alerts')->assertSuccessful();

    expect($material->fresh()->status)->not->toBe('Expired');
});

test('command sends only one expiring notification per material even with multiple expiring batches', function () {
    Notification::truncate();

    $material = Material::create(['name' => 'Bahan Multi Batch Expiring', 'is_piority' => false]);

    // Dua batch akan kadaluarsa dalam 7 hari
    MaterialBatch::create([
        'material_id' => $material->id,
        'unit_id' => $this->unit->id,
        'date' => now()->addDays(2)->format('Y-m-d'),
        'batch_quantity' => 10,
    ]);

    MaterialBatch::create([
        'material_id' => $material->id,
        'unit_id' => $this->unit->id,
        'date' => now()->addDays(5)->format('Y-m-d'),
        'batch_quantity' => 8,
    ]);

    $this->artisan('inventory:check-alerts')->assertSuccessful();

    // Hanya satu notifikasi untuk satu bahan, meski ada dua batch yang hampir expired
    $expiryNotifications = Notification::where('type', 'inventori')
        ->where('body', 'like', '%'.$material->name.'%')
        ->count();

    expect($expiryNotifications)->toBe(1);
});
