<?php

declare(strict_types=1);

use App\Livewire\Supplier\Form;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    Storage::fake('public');

    $this->user = User::factory()->create();

    $permission = Permission::firstOrCreate(['name' => 'inventori.toko.kelola']);
    $this->user->givePermissionTo($permission);

    $this->actingAs($this->user);
});

// TC-024 – Akses halaman supplier
test('TC-024 - halaman supplier tambah dapat diakses', function () {
    Livewire::test(Form::class)
        ->assertStatus(200);
});

// TC-025 – Tambah supplier dengan data lengkap
test('TC-025 - menambah supplier valid berhasil redirect ke daftar', function () {
    Livewire::test(Form::class)
        ->set('name', 'Toko Bahan Kue Makmur')
        ->set('contact_name', 'Pak Budi')
        ->set('phone', '081234567890')
        ->set('street', 'Jl. Pasar Baru No. 5')
        ->set('maps_link', 'https://maps.google.com/abc')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('supplier'));

    expect(Supplier::where('name', 'Toko Bahan Kue Makmur')->exists())->toBeTrue();
});

// TC-026 – Nama kosong
test('TC-026 - menambah supplier dengan nama kosong menampilkan pesan validasi', function () {
    Livewire::test(Form::class)
        ->set('name', '')
        ->set('phone', '081234567890')
        ->call('save')
        ->assertHasErrors(['name'])
        ->assertSee('Nama toko wajib diisi.');
});

// TC-027 – URL maps tidak valid
test('TC-027 - menambah supplier dengan URL maps tidak valid menampilkan pesan validasi', function () {
    Livewire::test(Form::class)
        ->set('name', 'Toko ABC')
        ->set('maps_link', 'bukan-url-valid')
        ->call('save')
        ->assertHasErrors(['maps_link'])
        ->assertSee('Link Google Maps harus berupa URL yang valid.');
});

// TC-028 – Gambar > 2MB
test('TC-028 - menambah supplier dengan gambar lebih dari 2MB menampilkan pesan validasi', function () {
    // Create a fake image that exceeds 2MB (2049 KB)
    $oversizedImage = UploadedFile::fake()->image('foto_toko.jpg')->size(2049);

    Livewire::test(Form::class)
        ->set('name', 'Toko ABC')
        ->set('image', $oversizedImage)
        ->assertHasErrors(['image'])
        ->assertSee('Ukuran gambar maksimal 2MB.');
});

// TC-028 – Gambar format tidak valid (mimes check - must be image but wrong extension like .gif)
test('format gambar tidak valid menampilkan pesan validasi', function () {
    // GIF passes the 'image' rule but fails 'mimes:jpg,jpeg,png'
    $invalidImage = UploadedFile::fake()->image('foto_toko.gif');

    Livewire::test(Form::class)
        ->set('name', 'Toko ABC')
        ->set('image', $invalidImage)
        ->assertHasErrors(['image'])
        ->assertSee('Format gambar yang diizinkan adalah jpg, jpeg, png.');
});

// TC-029 – Edit supplier valid
test('TC-029 - mengubah data supplier berhasil redirect ke daftar', function () {
    $supplier = Supplier::create([
        'name' => 'Toko Lama',
        'phone' => '081234567890',
    ]);

    Livewire::test(Form::class, ['id' => $supplier->id])
        ->set('name', 'Toko Bahan Kue Sentosa')
        ->set('phone', '089876543210')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('supplier'));

    expect(Supplier::find($supplier->id)->name)->toBe('Toko Bahan Kue Sentosa');
});

// Security – tanpa permission tidak bisa create
test('pengguna tanpa permission tidak bisa membuat supplier', function () {
    $userTanpaPermission = User::factory()->create();
    $this->actingAs($userTanpaPermission);

    Livewire::test(Form::class)
        ->set('name', 'Supplier Ilegal')
        ->call('save')
        ->assertForbidden();
});
