<?php

declare(strict_types=1);

use App\Livewire\Product\Form;
use App\Livewire\Product\Index;
use App\Models\Material;
use App\Models\Product;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    Storage::fake('public');

    $this->user = User::factory()->create();

    $permission = Permission::firstOrCreate(['name' => 'inventori.produk.kelola']);
    $this->user->givePermissionTo($permission);

    $this->actingAs($this->user);
});

// TC-044 – Akses halaman produk
test('TC-044 - halaman produk dapat diakses', function () {
    Livewire::test(Index::class)
        ->assertStatus(200);
});

// TC-045 – Tambah produk dengan metode dan komposisi dasar
test('TC-045 - menambah produk dengan data valid berhasil redirect ke daftar', function () {
    Livewire::test(Form::class)
        ->set('name', 'Brownies Coklat')
        ->set('selectedMethods', ['pesanan-reguler'])
        ->set('pcs', 8)
        ->set('price', 120000)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('produk'));

    expect(Product::where('name', 'Brownies Coklat')->exists())->toBeTrue();
});

// TC-046 – Nama kosong
test('TC-046 - menambah produk dengan nama kosong menampilkan pesan validasi', function () {
    Livewire::test(Form::class)
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name'])
        ->assertSee('Nama produk tidak boleh kosong.');
});

// TC-047 – Tidak memilih metode penjualan
test('TC-047 - menambah produk tanpa metode penjualan menampilkan pesan validasi', function () {
    Livewire::test(Form::class)
        ->set('name', 'Brownies Coklat')
        ->set('selectedMethods', [])
        ->call('save')
        ->assertHasErrors(['selectedMethods'])
        ->assertSee('Pilih minimal satu metode penjualan.');
});

// TC-048 – Jumlah pcs kurang dari 1
test('TC-048 - menambah produk dengan pcs 0 menampilkan pesan validasi', function () {
    // updatedPcs lifecycle hook adds the error when value < 1 and resets pcs back to 1
    Livewire::test(Form::class)
        ->set('name', 'Brownies Coklat')
        ->set('selectedMethods', ['pesanan-reguler'])
        ->set('pcs', 0)
        ->assertHasErrors(['pcs']);
});

// TC-049 – Gambar lebih dari 2MB
test('TC-049 - menambah produk dengan gambar lebih dari 2MB menampilkan pesan validasi', function () {
    $oversizedImage = UploadedFile::fake()->image('brownies.jpg')->size(2049);

    Livewire::test(Form::class)
        ->set('name', 'Brownies Coklat')
        ->set('selectedMethods', ['pesanan-reguler'])
        ->set('product_image', $oversizedImage)
        ->assertHasErrors(['product_image'])
        ->assertSee('Ukuran gambar tidak boleh lebih dari 2 MB.');
});

// TC-050 – Menambah biaya tambahan pada produk
test('TC-050 - menambah biaya tambahan pada produk berhasil disimpan', function () {
    $product = Product::create([
        'name' => 'Produk TC050',
        'method' => ['pesanan-reguler'],
        'pcs' => 1,
        'price' => 50000,
        'stock' => 0,
    ]);

    Livewire::test(Form::class, ['id' => $product->id])
        ->set('other_costs', [[
            'type_cost_id' => '',
            'name' => 'Kemasan',
            'price' => 5000,
        ]])
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('produk'));

    $updated = Product::with('other_costs')->find($product->id);
    expect($updated->other_costs)->toHaveCount(1);
    expect((float) $updated->other_costs->first()->price)->toBe(5000.0);
});

// TC-051 – Mengubah komposisi produk
test('TC-051 - mengubah komposisi produk berhasil disimpan', function () {
    $unit = Unit::create(['name' => 'Kilogram51', 'alias' => 'kg', 'group' => 'Massa']);
    $material = Material::create(['name' => 'Tepung TC051', 'status' => 'Kosong', 'minimum' => 1, 'is_active' => true]);

    $product = Product::create([
        'name' => 'Produk TC051',
        'method' => ['pesanan-reguler'],
        'pcs' => 8,
        'price' => 120000,
        'stock' => 0,
    ]);

    Livewire::test(Form::class, ['id' => $product->id])
        ->set('product_compositions', [[
            'material_id' => (string) $material->id,
            'material_quantity' => 0.75,
            'unit_id' => (string) $unit->id,
            'material_price' => 0,
        ]])
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('produk'));

    $updated = Product::with('product_compositions')->find($product->id);
    expect($updated->product_compositions)->toHaveCount(1);
    expect((float) $updated->product_compositions->first()->material_quantity)->toBe(0.75);
});

// Security – tanpa permission tidak bisa menyimpan produk
test('pengguna tanpa permission tidak bisa menyimpan produk', function () {
    $userTanpaPermission = User::factory()->create();
    $this->actingAs($userTanpaPermission);

    Livewire::test(Form::class)
        ->set('name', 'Produk Ilegal')
        ->call('save')
        ->assertForbidden();
});
