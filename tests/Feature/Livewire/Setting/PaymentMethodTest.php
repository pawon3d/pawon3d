<?php

declare(strict_types=1);

use App\Livewire\Setting\PaymentMethod;
use App\Models\PaymentChannel;
use App\Models\SpatieRole;
use App\Models\StoreProfile;
use App\Models\User;
use Illuminate\Support\Facades\View;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    // Seed StoreProfile so AppServiceProvider View::share doesn't fail
    $profile = StoreProfile::firstOrCreate(
        ['id' => 1],
        ['name' => 'Test Store', 'address' => 'Test Address', 'phone' => '08123456789']
    );
    View::share('storeProfile', $profile);

    // Create permissions
    Permission::firstOrCreate(['name' => 'manajemen.pembayaran.kelola']);

    // Create admin role with permission
    $adminRole = SpatieRole::firstOrCreate(['name' => 'Admin']);
    $adminRole->givePermissionTo('manajemen.pembayaran.kelola');

    $this->admin = User::factory()->create();
    $this->admin->assignRole($adminRole);

    $this->actingAs($this->admin);
});

// TC-107: Admin dapat mengakses halaman metode pembayaran
test('admin can access payment method page', function () {
    $this->get(route('metode-pembayaran'))
        ->assertOk()
        ->assertSeeLivewire(PaymentMethod::class);
});

// TC-108: Admin menambah metode pembayaran dengan data valid
test('can add new payment method with valid data', function () {
    Livewire::test(PaymentMethod::class)
        ->call('openModal')
        ->set('bankName', 'BCA')
        ->set('group', 'Transfer Bank')
        ->set('accountNumber', '1234567890')
        ->set('accountName', 'Pawon3D')
        ->set('isActive', true)
        ->call('save')
        ->assertHasNoErrors();

    expect(PaymentChannel::where('bank_name', 'BCA')->exists())->toBeTrue();
});

// TC-109: Admin menambah metode pembayaran dengan nama bank kosong
test('validates bank name is required', function () {
    Livewire::test(PaymentMethod::class)
        ->call('openModal')
        ->set('bankName', '')
        ->set('group', 'Transfer Bank')
        ->call('save')
        ->assertHasErrors(['bankName']);
});

// TC-110: Admin menambah metode pembayaran tanpa memilih grup
test('validates group is required', function () {
    Livewire::test(PaymentMethod::class)
        ->call('openModal')
        ->set('bankName', 'BCA')
        ->set('group', '')
        ->call('save')
        ->assertHasErrors(['group']);
});

// TC-111: Admin mengubah data metode pembayaran
test('can update existing payment method', function () {
    $channel = PaymentChannel::create([
        'bank_name' => 'BNI',
        'group' => 'Transfer Bank',
        'account_number' => '111222333',
        'is_active' => true,
    ]);

    Livewire::test(PaymentMethod::class)
        ->call('openModal', true, $channel->id)
        ->assertSet('bankName', 'BNI')
        ->set('accountNumber', '0987654321')
        ->call('save')
        ->assertHasNoErrors();

    $channel->refresh();
    expect($channel->account_number)->toBe('0987654321');
});
