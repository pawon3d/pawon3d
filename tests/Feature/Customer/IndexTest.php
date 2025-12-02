<?php

use App\Livewire\Customer\Index;
use App\Models\Customer;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
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
