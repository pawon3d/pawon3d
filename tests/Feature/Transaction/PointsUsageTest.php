<?php

use App\Livewire\Transaction\RincianPesanan;
use App\Models\Customer;
use App\Models\PointsHistory;
use App\Models\Transaction;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('records points history when points are redeemed via applyPoints', function () {
    $customer = Customer::factory()->create([
        'phone' => '081234567800',
        'points' => 100,
    ]);

    $transaction = Transaction::factory()->create([
        'customer_id' => $customer->id,
        'phone' => $customer->phone,
        'total_amount' => 100000,
        'points_used' => 0,
        'points_discount' => 0,
    ]);

    // Call the component method directly to avoid Livewire render serialization issues in tests
    $component = new RincianPesanan;
    $component->transactionId = $transaction->id;
    $component->transaction = $transaction;
    $component->customer = $customer;
    $component->pointsUsed = 50;
    $component->applyPoints();

    $customer->refresh();

    expect($customer->points)->toBe(50);

    $history = PointsHistory::where('phone', $customer->phone)
        ->where('transaction_id', $transaction->id)
        ->where('points', -50)
        ->first();

    expect($history)->not->toBeNull();
});

it('records points history when points are returned via applyPoints', function () {
    $customer = Customer::factory()->create([
        'phone' => '081234567801',
        'points' => 10,
    ]);

    $transaction = Transaction::factory()->create([
        'customer_id' => $customer->id,
        'phone' => $customer->phone,
        'total_amount' => 100000,
        'points_used' => 50,
        'points_discount' => 5000,
    ]);

    // Call the component method directly to avoid Livewire render serialization issues in tests
    $component = new RincianPesanan;
    $component->transactionId = $transaction->id;
    $component->transaction = $transaction;
    $component->customer = $customer;
    $component->pointsUsed = 20;
    $component->applyPoints();

    $customer->refresh();

    // Customer had 10, returned 30 -> 40
    expect($customer->points)->toBe(40);

    $history = PointsHistory::where('phone', $customer->phone)
        ->where('transaction_id', $transaction->id)
        ->where('points', 30)
        ->first();

    expect($history)->not->toBeNull();
});
