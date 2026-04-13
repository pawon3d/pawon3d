<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\StoreProfile;
use App\Models\User;
use Illuminate\Support\Facades\View;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $profile = StoreProfile::firstOrCreate(
        ['id' => 1],
        ['name' => 'Test Store', 'address' => 'Test Address', 'phone' => '08123456789']
    );
    View::share('storeProfile', $profile);

    Permission::firstOrCreate(['name' => 'manajemen.pelanggan.kelola', 'guard_name' => 'web']);

    $this->user = User::factory()->create();
    $this->user->givePermissionTo('manajemen.pelanggan.kelola');
    $this->actingAs($this->user);

    $this->customer = Customer::create([
        'name' => 'Test Customer',
        'phone' => '081234567890',
        'points' => 50,
    ]);
});

test('customer show page can be rendered', function () {
    $response = $this->get(route('customer.show', $this->customer->id));

    $response->assertStatus(200);
});

test('customer show page displays customer details', function () {
    $response = $this->get(route('customer.show', $this->customer->id));

    $response->assertSee('Test Customer');
    $response->assertSee('081234567890');
    $response->assertSee('50');
});

test('customer show page displays page title', function () {
    $response = $this->get(route('customer.show', $this->customer->id));

    $response->assertSee('Rincian Pelanggan');
});

test('customer show page has back button to customer list', function () {
    $response = $this->get(route('customer.show', $this->customer->id));

    $response->assertSee('Kembali');
});

test('customer can be updated from show page', function () {
    Livewire\Livewire::test(\App\Livewire\Customer\Show::class, ['id' => $this->customer->id])
        ->set('name', 'Updated Customer')
        ->call('update')
        ->assertHasNoErrors();

    $this->customer->refresh();
    expect($this->customer->name)->toBe('Updated Customer');
});

test('customer can be deleted from show page', function () {
    Livewire\Livewire::test(\App\Livewire\Customer\Show::class, ['id' => $this->customer->id])
        ->call('delete')
        ->assertRedirect(route('customer'));

    expect(Customer::find($this->customer->id))->toBeNull();
});

test('payment detail modal can be opened without type errors', function () {
    Livewire\Livewire::test(\App\Livewire\Customer\Show::class, ['id' => $this->customer->id])
        ->call('showDetailModal')
        ->assertSet('showPaymentModal', true)
        ->assertSet('payments', [])
        ->assertSet('refunds', [])
        ->assertSet('cancellations', []);
});
