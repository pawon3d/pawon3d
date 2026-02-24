<?php

declare(strict_types=1);

use App\Livewire\Category\Index;
use App\Models\Category;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->user = User::factory()->create();

    $permission = Permission::firstOrCreate(['name' => 'inventori.persediaan.kelola']);
    $this->user->givePermissionTo($permission);

    $this->actingAs($this->user);
});

// TC-008 – Akses halaman kategori
test('TC-008 - halaman kategori dapat diakses dan menampilkan daftar', function () {
    Category::create(['name' => 'Kue Basah', 'is_active' => true]);
    Category::create(['name' => 'Kue Kering', 'is_active' => true]);

    Livewire::test(Index::class)
        ->assertSee('Kue Basah')
        ->assertSee('Kue Kering')
        ->assertStatus(200);
});

// TC-009 – Tambah kategori valid
test('TC-009 - menambah kategori valid berhasil menyimpan ke database', function () {
    Livewire::test(Index::class)
        ->call('showAddModal')
        ->set('name', 'Kue Tradisional')
        ->set('is_active', true)
        ->call('store')
        ->assertHasNoErrors();

    expect(Category::where('name', 'Kue Tradisional')->exists())->toBeTrue();
});

// TC-010 – Nama kosong
test('TC-010 - menambah kategori dengan nama kosong menampilkan pesan validasi', function () {
    Livewire::test(Index::class)
        ->call('showAddModal')
        ->set('name', '')
        ->call('store')
        ->assertHasErrors(['name'])
        ->assertSee('Nama kategori tidak boleh kosong');
});

// TC-011 – Nama < 3 karakter
test('TC-011 - menambah kategori dengan nama kurang dari 3 karakter menampilkan pesan validasi', function () {
    Livewire::test(Index::class)
        ->call('showAddModal')
        ->set('name', 'Ku')
        ->call('store')
        ->assertHasErrors(['name'])
        ->assertSee('Nama kategori minimal 3 karakter');
});

// TC-012 – Nama duplikat saat tambah
test('TC-012 - menambah kategori dengan nama duplikat menampilkan pesan validasi', function () {
    Category::create(['name' => 'Kue Basah', 'is_active' => true]);

    Livewire::test(Index::class)
        ->call('showAddModal')
        ->set('name', 'Kue Basah')
        ->call('store')
        ->assertHasErrors(['name'])
        ->assertSee('Nama kategori sudah ada');
});

// TC-013 – Edit kategori valid
test('TC-013 - mengubah kategori dengan data valid berhasil diperbarui', function () {
    $category = Category::create(['name' => 'Kue Basah', 'is_active' => true]);

    Livewire::test(Index::class)
        ->set('category_id', $category->id)
        ->set('name', $category->name)
        ->set('is_active', $category->is_active)
        ->set('showEditModal', true)
        ->set('name', 'Kue Kering')
        ->call('update')
        ->assertHasNoErrors();

    expect(Category::find($category->id)->name)->toBe('Kue Kering');
});

// TC-014 – Edit dengan nama duplikat kategori lain (pesan dalam Bahasa Indonesia)
test('TC-014 - mengubah kategori dengan nama duplikat menampilkan pesan validasi bahasa indonesia', function () {
    $category1 = Category::create(['name' => 'Kue Basah', 'is_active' => true]);
    Category::create(['name' => 'Kue Kering', 'is_active' => true]);

    Livewire::test(Index::class)
        ->set('category_id', $category1->id)
        ->set('name', 'Kue Basah')
        ->set('is_active', true)
        ->set('showEditModal', true)
        ->set('name', 'Kue Kering')
        ->call('update')
        ->assertHasErrors(['name'])
        ->assertSee('Nama kategori sudah ada');
});

// TC-014 – Edit nama sendiri tidak dianggap duplikat
test('TC-014 - mengubah nama kategori ke nama sendiri tidak dianggap duplikat', function () {
    $category = Category::create(['name' => 'Kue Basah', 'is_active' => true]);

    Livewire::test(Index::class)
        ->set('category_id', $category->id)
        ->set('name', 'Kue Basah')
        ->set('is_active', false)
        ->set('showEditModal', true)
        ->call('update')
        ->assertHasNoErrors();
});

// TC-015 – Hapus kategori
test('TC-015 - menghapus kategori berhasil menghapus dari database', function () {
    $category = Category::create(['name' => 'Kue Basah', 'is_active' => true]);

    Livewire::test(Index::class)
        ->set('category_id', $category->id)
        ->call('delete')
        ->assertHasNoErrors();

    expect(Category::find($category->id))->toBeNull();
});

// Security – pengguna tanpa permission tidak bisa store
test('pengguna tanpa permission tidak bisa menambah kategori', function () {
    $userTanpaPermission = User::factory()->create();
    $this->actingAs($userTanpaPermission);

    Livewire::test(Index::class)
        ->set('name', 'Kategori Ilegal')
        ->call('store')
        ->assertForbidden();
});

// Security – pengguna tanpa permission tidak bisa delete
test('pengguna tanpa permission tidak bisa menghapus kategori', function () {
    $userTanpaPermission = User::factory()->create();
    $this->actingAs($userTanpaPermission);

    $category = Category::create(['name' => 'Kue Basah', 'is_active' => true]);

    Livewire::test(Index::class)
        ->set('category_id', $category->id)
        ->call('delete')
        ->assertForbidden();
});
