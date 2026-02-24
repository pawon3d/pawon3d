<?php

declare(strict_types=1);

use App\Livewire\Unit\Index;
use App\Models\Material;
use App\Models\MaterialDetail;
use App\Models\Unit;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->user = User::factory()->create();

    $permission = Permission::firstOrCreate(['name' => 'inventori.persediaan.kelola']);
    $this->user->givePermissionTo($permission);

    $this->actingAs($this->user);
});

// TC-016 – Akses halaman satuan
test('TC-016 - halaman satuan dapat diakses dan menampilkan daftar', function () {
    Unit::create(['name' => 'Kilogram', 'alias' => 'kg', 'group' => 'Massa']);
    Unit::create(['name' => 'Liter', 'alias' => 'L', 'group' => 'Volume']);

    Livewire::test(Index::class)
        ->assertSee('Kilogram')
        ->assertSee('Liter')
        ->assertStatus(200);
});

// TC-017 – Tambah satuan dasar (faktor konversi default = 1)
test('TC-017 - menambah satuan dasar menyimpan dengan faktor konversi 1', function () {
    Livewire::test(Index::class)
        ->call('showAddModal')
        ->set('name', 'Kilogram')
        ->set('alias', 'kg')
        ->set('group', 'Massa')
        ->call('store')
        ->assertHasNoErrors();

    $unit = Unit::where('name', 'Kilogram')->first();
    expect($unit)->not->toBeNull();
    expect((float) $unit->conversion_factor)->toBe(1.0);
});

// TC-018 – Nama kosong
test('TC-018 - menambah satuan dengan nama kosong menampilkan pesan validasi', function () {
    Livewire::test(Index::class)
        ->call('showAddModal')
        ->set('name', '')
        ->set('alias', 'kg')
        ->set('group', 'Massa')
        ->call('store')
        ->assertHasErrors(['name'])
        ->assertSee('Nama satuan tidak boleh kosong');
});

// TC-019 – Nama duplikat
test('TC-019 - menambah satuan dengan nama duplikat menampilkan pesan validasi', function () {
    Unit::create(['name' => 'Kilogram', 'alias' => 'kg', 'group' => 'Massa']);

    Livewire::test(Index::class)
        ->call('showAddModal')
        ->set('name', 'Kilogram')
        ->set('alias', 'kg')
        ->set('group', 'Massa')
        ->call('store')
        ->assertHasErrors(['name'])
        ->assertSee('Nama satuan sudah ada');
});

// TC-020 – Kelompok kosong
test('TC-020 - menambah satuan tanpa kelompok menampilkan pesan validasi', function () {
    Livewire::test(Index::class)
        ->call('showAddModal')
        ->set('name', 'Liter')
        ->set('alias', 'L')
        ->set('group', '')
        ->call('store')
        ->assertHasErrors(['group'])
        ->assertSee('Kelompok satuan harus dipilih');
});

// TC-021 – Satuan turunan dengan faktor konversi
test('TC-021 - menambah satuan turunan dengan faktor konversi berhasil disimpan', function () {
    $baseUnit = Unit::create(['name' => 'Kilogram', 'alias' => 'kg', 'group' => 'Massa']);

    Livewire::test(Index::class)
        ->call('showAddModal')
        ->set('name', 'Gram')
        ->set('alias', 'g')
        ->set('group', 'Massa')
        ->set('base_unit_id', $baseUnit->id)
        ->set('conversion_factor', 0.001)
        ->call('store')
        ->assertHasNoErrors();

    $unit = Unit::where('name', 'Gram')->first();
    expect($unit)->not->toBeNull();
    expect((float) $unit->conversion_factor)->toBe(0.001);
    expect((string) $unit->base_unit_id)->toBe((string) $baseUnit->id);
});

// TC-022 – Satuan turunan tanpa faktor konversi
test('TC-022 - satuan turunan tanpa faktor konversi menampilkan pesan error', function () {
    $baseUnit = Unit::create(['name' => 'Kilogram', 'alias' => 'kg', 'group' => 'Massa']);

    Livewire::test(Index::class)
        ->call('showAddModal')
        ->set('name', 'Gram')
        ->set('alias', 'g')
        ->set('group', 'Massa')
        ->set('base_unit_id', $baseUnit->id)
        ->set('conversion_factor', null)
        ->call('store')
        ->assertHasErrors(['conversion_factor']);
});

// TC-023 – Hapus satuan yang digunakan bahan baku (error, tidak terhapus)
test('TC-023 - menghapus satuan yang digunakan bahan baku tidak dapat dihapus', function () {
    $unit = Unit::create(['name' => 'Kilogram', 'alias' => 'kg', 'group' => 'Massa']);

    $material = Material::create(['name' => 'Tepung Terigu', 'group' => 'persediaan']);
    MaterialDetail::create([
        'material_id' => $material->id,
        'unit_id' => $unit->id,
        'base_quantity' => 1,
        'supply_quantity' => 0,
    ]);

    Livewire::test(Index::class)
        ->set('unit_id', $unit->id)
        ->call('delete');

    expect(Unit::find($unit->id))->not->toBeNull();
});

// Update – nama duplikat menampilkan pesan Indonesia
test('update satuan dengan nama duplikat menampilkan pesan validasi bahasa indonesia', function () {
    $unit1 = Unit::create(['name' => 'Kilogram', 'alias' => 'kg', 'group' => 'Massa']);
    Unit::create(['name' => 'Gram', 'alias' => 'g', 'group' => 'Massa']);

    Livewire::test(Index::class)
        ->set('unit_id', $unit1->id)
        ->set('name', 'Gram')
        ->set('alias', 'kg')
        ->set('group', 'Massa')
        ->call('update')
        ->assertHasErrors(['name'])
        ->assertSee('Nama satuan sudah ada');
});

// Update – alias dan group divalidasi saat update
test('update satuan dengan alias kosong menampilkan pesan validasi', function () {
    $unit = Unit::create(['name' => 'Kilogram', 'alias' => 'kg', 'group' => 'Massa']);

    Livewire::test(Index::class)
        ->set('unit_id', $unit->id)
        ->set('name', 'Kilogram')
        ->set('alias', '')
        ->set('group', 'Massa')
        ->call('update')
        ->assertHasErrors(['alias']);
});

// Security – tanpa permission tidak bisa store
test('pengguna tanpa permission tidak bisa menambah satuan', function () {
    $userTanpaPermission = User::factory()->create();
    $this->actingAs($userTanpaPermission);

    Livewire::test(Index::class)
        ->set('name', 'Illegal Unit')
        ->set('alias', 'il')
        ->set('group', 'Massa')
        ->call('store')
        ->assertForbidden();
});
