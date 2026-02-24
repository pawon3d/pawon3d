<?php

declare(strict_types=1);

use App\Livewire\Material\Form;
use App\Livewire\Material\Index;
use App\Models\Material;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    Storage::fake('public');

    $this->user = User::factory()->create();

    $permission = Permission::firstOrCreate(['name' => 'inventori.persediaan.kelola']);
    $this->user->givePermissionTo($permission);

    $this->actingAs($this->user);
});

// TC-030 – Akses halaman bahan baku
test('TC-030 - halaman bahan baku dapat diakses', function () {
    Livewire::test(Index::class)
        ->assertStatus(200);
});

// TC-031 – Tambah bahan baku valid
test('TC-031 - menambah bahan baku valid berhasil redirect ke daftar', function () {
    Livewire::test(Form::class)
        ->set('name', 'Tepung Terigu')
        ->set('minimum', 5)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('bahan-baku'));

    expect(Material::where('name', 'Tepung Terigu')->exists())->toBeTrue();
});

// TC-032 – Nama kosong
test('TC-032 - menambah bahan baku dengan nama kosong menampilkan pesan validasi', function () {
    Livewire::test(Form::class)
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name'])
        ->assertSee('Nama bahan baku tidak boleh kosong.');
});

// TC-033 – Format gambar tidak valid (GIF passes 'image' rule but fails 'mimes:jpg,jpeg,png')
test('TC-033 - menambah bahan baku dengan format gambar tidak valid menampilkan pesan validasi', function () {
    $invalidImage = UploadedFile::fake()->image('bahan.gif');

    Livewire::test(Form::class)
        ->set('name', 'Tepung Terigu')
        ->set('image', $invalidImage)
        ->assertHasErrors(['image'])
        ->assertSee('Format gambar yang diizinkan adalah jpg, jpeg, png.');
});

// TC-034 – Ubah data bahan baku dengan recalculate status stok
test('TC-034 - mengubah data bahan baku berhasil redirect dan data diperbarui', function () {
    $material = Material::create([
        'name' => 'Tepung Lama',
        'status' => 'Kosong',
        'minimum' => 5,
        'is_active' => true,
    ]);

    Livewire::test(Form::class, ['id' => $material->id])
        ->set('name', 'Tepung Protein Tinggi')
        ->set('minimum', 10)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('bahan-baku'));

    $updated = Material::find($material->id);
    expect($updated->name)->toBe('Tepung Protein Tinggi');
    expect((int) $updated->minimum)->toBe(10);
});

// Security – tanpa permission tidak bisa menyimpan
test('pengguna tanpa permission tidak bisa menyimpan bahan baku', function () {
    $userTanpaPermission = User::factory()->create();
    $this->actingAs($userTanpaPermission);

    Livewire::test(Form::class)
        ->set('name', 'Bahan Ilegal')
        ->call('save')
        ->assertForbidden();
});
