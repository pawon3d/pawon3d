<?php

use App\Models\Product;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    // Create Kasir permission and role
    Permission::create(['name' => 'Kasir']);
    $role = Role::create(['name' => 'Kasir'])->givePermissionTo(['Kasir']);

    $this->user = User::factory()->create();
    $this->user->assignRole($role);
});

test('rincian produk page can be rendered', function () {
    $product = Product::create([
        'name' => 'Test Product',
        'description' => 'Test description',
        'price' => 50000,
        'stock' => 10,
        'method' => ['pesanan-reguler'],
        'pcs' => 1,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->user)->get(route('transaksi.rincian-produk', $product->id));

    $response->assertStatus(200);
    $response->assertSee('Test Product');
    $response->assertSee('Rp50.000');
});

test('rincian produk shows product details', function () {
    $product = Product::create([
        'name' => 'Kemojo Loyang',
        'description' => 'Kue tradisional khas Melayu',
        'price' => 80000,
        'stock' => 10,
        'method' => ['pesanan-reguler'],
        'pcs' => 5,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->user)->get(route('transaksi.rincian-produk', $product->id));

    $response->assertStatus(200);
    $response->assertSee('Kemojo Loyang');
    $response->assertSee('(5 pcs)');
    $response->assertSee('Rp80.000');
    $response->assertSee('Kue tradisional khas Melayu');
});

test('rincian produk shows related products with same method', function () {
    $product = Product::create([
        'name' => 'Main Product',
        'price' => 50000,
        'method' => ['pesanan-reguler'],
        'pcs' => 1,
        'is_active' => true,
    ]);

    $relatedProduct = Product::create([
        'name' => 'Related Product',
        'price' => 60000,
        'method' => ['pesanan-reguler'],
        'pcs' => 1,
        'is_active' => true,
    ]);

    $unrelatedProduct = Product::create([
        'name' => 'Unrelated Product',
        'price' => 70000,
        'method' => ['siap-beli'],
        'pcs' => 1,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->user)->get(route('transaksi.rincian-produk', $product->id));

    $response->assertStatus(200);
    $response->assertSee('Related Product');
    $response->assertDontSee('Unrelated Product');
});

test('rincian produk does not show inactive products in related products', function () {
    $product = Product::create([
        'name' => 'Main Product',
        'price' => 50000,
        'method' => ['pesanan-reguler'],
        'pcs' => 1,
        'is_active' => true,
    ]);

    $inactiveProduct = Product::create([
        'name' => 'Inactive Product',
        'price' => 60000,
        'method' => ['pesanan-reguler'],
        'pcs' => 1,
        'is_active' => false,
    ]);

    $response = $this->actingAs($this->user)->get(route('transaksi.rincian-produk', $product->id));

    $response->assertStatus(200);
    $response->assertDontSee('Inactive Product');
});

test('rincian produk shows related products with multiple methods', function () {
    $product = Product::create([
        'name' => 'Main Product',
        'price' => 50000,
        'method' => ['pesanan-reguler', 'pesanan-kotak'],
        'pcs' => 1,
        'is_active' => true,
    ]);

    $relatedByFirstMethod = Product::create([
        'name' => 'Related By First Method',
        'price' => 60000,
        'method' => ['pesanan-reguler'],
        'pcs' => 1,
        'is_active' => true,
    ]);

    $relatedBySecondMethod = Product::create([
        'name' => 'Related By Second Method',
        'price' => 70000,
        'method' => ['pesanan-kotak'],
        'pcs' => 1,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->user)->get(route('transaksi.rincian-produk', $product->id));

    $response->assertStatus(200);
    $response->assertSee('Related By First Method');
    $response->assertSee('Related By Second Method');
});

test('rincian produk returns 404 for non-existent product', function () {
    $response = $this->actingAs($this->user)->get(route('transaksi.rincian-produk', 'non-existent-id'));

    $response->assertStatus(404);
});
