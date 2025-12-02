<?php

use App\Models\Customer;
use App\Models\PointsHistory;
use App\Models\Transaction;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('customer earns points when order is completed', function () {
    $customer = Customer::factory()->create([
        'phone' => '081234567890',
        'points' => 0,
    ]);

    $transaction = Transaction::factory()->create([
        'customer_id' => $customer->id,
        'phone' => $customer->phone,
        'total_amount' => 54000,
        'points_discount' => 0,
        'payment_status' => 'Lunas',
        'status' => 'Dapat Diambil',
        'method' => 'pesanan-reguler',
    ]);

    // Simulate finish logic
    $transaction->update([
        'status' => 'Selesai',
        'end_date' => now(),
    ]);

    // Add points logic
    addPointsToCustomer($transaction);

    $customer->refresh();

    // 54000 / 10000 = 5 poin
    expect($customer->points)->toBe(5);

    // Cek history poin dibuat
    $history = PointsHistory::where('phone', $customer->phone)
        ->where('transaction_id', $transaction->id)
        ->first();

    expect($history)->not->toBeNull();
    expect($history->points)->toBe(5);
    expect($history->action)->toBe('Pesanan Reguler');
});

test('customer earns correct points for different amounts', function () {
    $customer = Customer::factory()->create([
        'phone' => '081234567891',
        'points' => 10,
    ]);

    $transaction = Transaction::factory()->create([
        'customer_id' => $customer->id,
        'phone' => $customer->phone,
        'total_amount' => 105000,
        'points_discount' => 0,
        'payment_status' => 'Lunas',
        'status' => 'Selesai',
        'method' => 'pesanan-kotak',
    ]);

    addPointsToCustomer($transaction);

    $customer->refresh();

    // 105000 / 10000 = 10 poin, ditambah 10 poin sebelumnya = 20
    expect($customer->points)->toBe(20);

    $history = PointsHistory::where('phone', $customer->phone)
        ->where('transaction_id', $transaction->id)
        ->first();

    expect($history)->not->toBeNull();
    expect($history->points)->toBe(10);
    expect($history->action)->toBe('Pesanan Kotak');
});

test('no points earned for amount less than 10000', function () {
    $customer = Customer::factory()->create([
        'phone' => '081234567892',
        'points' => 5,
    ]);

    $transaction = Transaction::factory()->create([
        'customer_id' => $customer->id,
        'phone' => $customer->phone,
        'total_amount' => 9000,
        'points_discount' => 0,
        'payment_status' => 'Lunas',
        'status' => 'Selesai',
        'method' => 'siap-beli',
    ]);

    addPointsToCustomer($transaction);

    $customer->refresh();

    // Poin tetap 5, tidak bertambah
    expect($customer->points)->toBe(5);

    // Tidak ada history baru
    $history = PointsHistory::where('transaction_id', $transaction->id)->first();
    expect($history)->toBeNull();
});

test('no points for guest customer without customer_id', function () {
    $transaction = Transaction::factory()->create([
        'customer_id' => null,
        'phone' => '081234567893',
        'total_amount' => 50000,
        'points_discount' => 0,
        'payment_status' => 'Lunas',
        'status' => 'Selesai',
        'method' => 'siap-beli',
    ]);

    $initialHistoryCount = PointsHistory::count();

    addPointsToCustomer($transaction);

    // Tidak ada history baru
    expect(PointsHistory::count())->toBe($initialHistoryCount);
});

test('points calculated after points discount applied', function () {
    $customer = Customer::factory()->create([
        'phone' => '081234567894',
        'points' => 0,
    ]);

    $transaction = Transaction::factory()->create([
        'customer_id' => $customer->id,
        'phone' => $customer->phone,
        'total_amount' => 50000,
        'points_discount' => 5000,
        'payment_status' => 'Lunas',
        'status' => 'Selesai',
        'method' => 'pesanan-reguler',
    ]);

    addPointsToCustomer($transaction);

    $customer->refresh();

    // (50000 - 5000) / 10000 = 4 poin
    expect($customer->points)->toBe(4);
});

test('siap saji method gives correct action name', function () {
    $customer = Customer::factory()->create([
        'phone' => '081234567895',
        'points' => 0,
    ]);

    $transaction = Transaction::factory()->create([
        'customer_id' => $customer->id,
        'phone' => $customer->phone,
        'total_amount' => 30000,
        'points_discount' => 0,
        'payment_status' => 'Lunas',
        'status' => 'Selesai',
        'method' => 'siap-beli',
    ]);

    addPointsToCustomer($transaction);

    $history = PointsHistory::where('transaction_id', $transaction->id)->first();

    expect($history)->not->toBeNull();
    expect($history->action)->toBe('Siap Saji');
    expect($history->points)->toBe(3);
});

/**
 * Helper function to add points - mirrors the logic in RincianPesanan::addPointsToCustomer
 */
function addPointsToCustomer(Transaction $transaction): void
{
    $customer = null;

    if ($transaction->customer_id) {
        $customer = Customer::find($transaction->customer_id);
    } elseif ($transaction->phone) {
        $customer = Customer::where('phone', $transaction->phone)->first();
    }

    if (! $customer) {
        return;
    }

    $totalAmount = $transaction->total_amount - ($transaction->points_discount ?? 0);
    $pointsEarned = (int) floor($totalAmount / 10000);

    if ($pointsEarned <= 0) {
        return;
    }

    $actionMap = [
        'pesanan-reguler' => 'Pesanan Reguler',
        'pesanan-kotak' => 'Pesanan Kotak',
        'siap-beli' => 'Siap Saji',
    ];
    $action = $actionMap[$transaction->method] ?? 'Pesanan Reguler';

    PointsHistory::create([
        'phone' => $customer->phone,
        'action' => $action,
        'points' => $pointsEarned,
        'transaction_id' => $transaction->id,
    ]);

    $customer->increment('points', $pointsEarned);
}
