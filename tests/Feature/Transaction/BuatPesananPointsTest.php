<?php

declare(strict_types=1);

use App\Livewire\Transaction\BuatPesanan;
use App\Models\Customer;
use App\Models\StoreProfile;
use App\Models\User;
use Illuminate\Support\Facades\View;

beforeEach(function () {
    $profile = StoreProfile::firstOrCreate(['id' => 1], [
        'name' => 'Test Store',
        'address' => 'Test Address',
        'phone' => '08123456789',
    ]);
    View::share('storeProfile', $profile);

    $this->user = User::factory()->create([
        'is_active' => true,
        'activated_at' => now(),
    ]);

    $this->customer = Customer::factory()->create([
        'phone' => '081234599001',
        'points' => 50,
    ]);
});

// TC-162: Kasir menggunakan poin pelanggan saat transaksi (valid, kelipatan 10)
test('kasir can use valid customer points as discount', function () {
    $component = new BuatPesanan;
    $component->availablePoints = 50;
    $component->details = [
        'item1' => ['price' => 100000, 'quantity' => 1, 'name' => 'Test', 'product_id' => '1', 'stock' => 10],
    ];
    $component->pointsUsed = 0;

    $component->updatedPointsUsed(30);

    expect($component->pointsUsed)->toBe(30);
});

// TC-163: Kasir menggunakan poin bukan kelipatan 10 (auto-rounded down)
test('points not multiple of 10 are rounded down automatically', function () {
    $component = new BuatPesanan;
    $component->availablePoints = 50;
    $component->details = [
        'item1' => ['price' => 100000, 'quantity' => 1, 'name' => 'Test', 'product_id' => '1', 'stock' => 10],
    ];
    $component->pointsUsed = 0;

    $component->updatedPointsUsed(15);

    expect($component->pointsUsed)->toBe(10);
});

// TC-164: Kasir menggunakan poin melebihi poin tersedia (auto-capped ke available)
test('points used cannot exceed available points', function () {
    $component = new BuatPesanan;
    $component->availablePoints = 50;
    $component->details = [
        'item1' => ['price' => 100000, 'quantity' => 1, 'name' => 'Test', 'product_id' => '1', 'stock' => 10],
    ];
    $component->pointsUsed = 0;

    $component->updatedPointsUsed(80);

    expect($component->pointsUsed)->toBe(50);
});
