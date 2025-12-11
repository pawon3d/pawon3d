<?php

use App\Livewire\Transaction\RincianPesanan;
use App\Models\Customer;
use App\Models\PointsHistory;
use App\Models\Transaction;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('records points history when paying with additional points via pay()', function () {
    $customer = Customer::factory()->create([
        'phone' => '081234567810',
        'points' => 100,
    ]);

    $transaction = Transaction::factory()->create([
        'customer_id' => $customer->id,
        'phone' => $customer->phone,
        'total_amount' => 100000,
        'points_used' => 0,
        'points_discount' => 0,
    ]);

    $component = new RincianPesanan;
    $component->transactionId = $transaction->id;
    $component->transaction = $transaction;
    $component->customer = $customer;

    // Simulate entering points at payment time
    $component->pointsUsed = 10;
    $component->paidAmount = 50000; // fulfilling min required for non-siap-beli flow

    $component->pay();

    $customer->refresh();

    expect($customer->points)->toBe(90);

    $history = PointsHistory::where('phone', $customer->phone)
        ->where('transaction_id', $transaction->id)
        ->where('points', -10)
        ->first();

    expect($history)->not->toBeNull();
});
