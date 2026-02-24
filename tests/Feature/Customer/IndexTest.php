<?php

declare(strict_types=1);

use App\Livewire\Customer\Index;
use App\Models\Customer;
use App\Models\StoreProfile;
use App\Models\User;
use Illuminate\Support\Facades\View;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $profile = StoreProfile::firstOrCreate(
        ['id' => 1],
        ['name' => 'Test Store', 'address' => 'Test Address', 'phone' => '08123456789']
    );
    View::share('storeProfile', $profile);

    // Create permission for customer management
    $permission = Permission::firstOrCreate(['name' => 'manajemen.pelanggan.kelola', 'guard_name' => 'web']);

    $this->user = User::factory()->create();
    $this->user->givePermissionTo($permission);
});

test('customer index page can be rendered', function () {
    $response = $this->actingAs($this->user)->get(route('customer'));

    $response->assertStatus(200);
    $response->assertSeeLivewire(Index::class);
});

test('customer index page shows correct title', function () {
    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->assertSee('Daftar Pelanggan');
});

test('customer index page shows empty message when no data', function () {
    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->assertSee('Tidak ada pelanggan yang tersedia.');
});

test('customer index page displays customers', function () {
    Customer::create([
        'name' => 'Fani',
        'phone' => '081122334455',
        'points' => 65,
    ]);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->assertSee('Fani')
        ->assertSee('081122334455')
        ->assertSee('65');
});

test('can search customers by name', function () {
    Customer::create([
        'name' => 'Fani',
        'phone' => '081122334455',
    ]);

    Customer::create([
        'name' => 'Erwin',
        'phone' => '085544332211',
    ]);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->set('search', 'Fani')
        ->assertSee('Fani')
        ->assertDontSee('Erwin');
});

test('can search customers by phone number', function () {
    Customer::create([
        'name' => 'Fani',
        'phone' => '081122334455',
    ]);

    Customer::create([
        'name' => 'Erwin',
        'phone' => '085544332211',
    ]);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->set('search', '085544332211')
        ->assertSee('Erwin')
        ->assertSee('085544332211');
});

// TC-113: Admin menambah pelanggan baru dengan data valid
test('can add customer with valid data', function () {
    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->call('showModalTambah')
        ->set('name', 'Ibu Sari')
        ->set('phone', '081234567890')
        ->call('addCustomer')
        ->assertHasNoErrors();

    expect(Customer::where('phone', '081234567890')->exists())->toBeTrue();
});

// TC-114: Admin menambah pelanggan dengan telepon kosong
test('validates phone is required when adding customer', function () {
    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->call('showModalTambah')
        ->set('name', 'Ibu Sari')
        ->set('phone', '')
        ->call('addCustomer')
        ->assertHasErrors(['phone']);
});

// TC-115: Admin menambah pelanggan dengan nomor telepon duplikat
test('validates phone must be unique when adding customer', function () {
    Customer::create(['name' => 'Existing', 'phone' => '081234567890']);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->call('showModalTambah')
        ->set('name', 'Ibu Ani')
        ->set('phone', '081234567890')
        ->call('addCustomer')
        ->assertHasErrors(['phone']);
});

// TC-117: Pencarian pelanggan dengan nomor tidak ditemukan
test('search returns empty when no matching customer found', function () {
    Customer::create(['name' => 'Fani', 'phone' => '081122334455']);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->set('search', '099999999999')
        ->assertSee('Tidak ada pelanggan yang tersedia.');
});
