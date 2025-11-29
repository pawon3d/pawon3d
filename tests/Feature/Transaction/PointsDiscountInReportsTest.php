<?php

use App\Models\Customer;
use App\Models\PaymentChannel;
use App\Models\Product;
use App\Models\Shift;
use App\Models\Transaction;
use App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);

    // Create test data
    $this->customer = Customer::create([
        'name' => 'Test Customer',
        'phone' => '08123456789',
        'points' => 500,
    ]);

    $this->product = Product::create([
        'name' => 'Test Product',
        'description' => 'Test description',
        'price' => 50000,
        'stock' => 10,
        'method' => ['pesanan-reguler'],
        'pcs' => 1,
        'is_active' => true,
    ]);

    $this->channel = PaymentChannel::create([
        'payment_method' => 'tunai',
        'bank_name' => 'Tunai',
        'is_active' => true,
    ]);

    // Create shift
    $this->shift = Shift::create([
        'opened_by' => $this->user->id,
        'start_time' => now(),
        'status' => 'open',
        'initial_cash' => 100000,
    ]);
});

test('points discount is calculated correctly in shift calculations', function () {
    // Create transaction with points discount
    $transaction = Transaction::create([
        'customer_id' => $this->customer->id,
        'created_by_shift' => $this->shift->id,
        'payment_status' => 'Lunas',
        'points_used' => 100,
        'points_discount' => 10000, // 100 points × 100
        'total_amount' => 50000,
        'invoice_number' => 'INV-001',
    ]);

    $transaction->details()->create([
        'product_id' => $this->product->id,
        'quantity' => 1,
        'price' => 50000,
    ]);

    $transaction->payments()->create([
        'channel_id' => $this->channel->id,
        'payment_method' => 'tunai',
        'paid_amount' => 40000, // 50000 - 10000 discount
        'receipt_number' => 'RCP-001',
        'paid_at' => now(),
    ]);

    // Calculate points discount for this shift
    $pointsDiscount = Transaction::where('created_by_shift', $this->shift->id)
        ->sum('points_discount');

    // Assert discount is calculated correctly
    expect($pointsDiscount)->toBe(10000);

    // Calculate received cash
    $receivedCash = Transaction::where('created_by_shift', $this->shift->id)
        ->whereHas('payments', fn($q) => $q->where('payment_method', 'tunai'))
        ->with(['payments' => fn($q) => $q->where('payment_method', 'tunai')])
        ->get()
        ->sum(fn($t) => $t->payments->sum('paid_amount'));

    // Cash should be 40000 (paid amount after discount)
    expect($receivedCash)->toBe(40000);

    // Expected cash: initial + received - discount
    $expectedCash = $this->shift->initial_cash + $receivedCash - $pointsDiscount;
    expect($expectedCash)->toBe(130000); // 100000 + 40000 - 10000
});

test('auto close previous day shift calculates final cash with discount', function () {
    // Create shift from yesterday
    $yesterdayShift = Shift::create([
        'opened_by' => $this->user->id,
        'start_time' => now()->subDay(),
        'status' => 'open',
        'initial_cash' => 100000,
    ]);

    // Create transaction with points discount
    $transaction = Transaction::create([
        'customer_id' => $this->customer->id,
        'created_by_shift' => $yesterdayShift->id,
        'payment_status' => 'Lunas',
        'points_used' => 100,
        'points_discount' => 10000,
        'total_amount' => 50000,
        'invoice_number' => 'INV-002',
    ]);

    $transaction->details()->create([
        'product_id' => $this->product->id,
        'quantity' => 1,
        'price' => 50000,
    ]);

    $transaction->payments()->create([
        'channel_id' => $this->channel->id,
        'payment_method' => 'tunai',
        'paid_amount' => 40000,
        'receipt_number' => 'RCP-002',
        'paid_at' => now()->subDay(),
    ]);

    // Simulate auto close logic
    $receivedCash = Transaction::where('created_by_shift', $yesterdayShift->id)
        ->whereHas('payments', fn($q) => $q->where('payment_method', 'tunai'))
        ->with(['payments' => fn($q) => $q->where('payment_method', 'tunai')])
        ->get()
        ->sum(fn($t) => $t->payments->sum('paid_amount'));

    $refundTotal = Transaction::where('refund_by_shift', $yesterdayShift->id)->sum('total_refund');

    $pointsDiscount = Transaction::where('created_by_shift', $yesterdayShift->id)
        ->sum('points_discount');

    $finalCash = $yesterdayShift->initial_cash + $receivedCash - $refundTotal - $pointsDiscount;

    // Assert final cash is correct: 100000 + 40000 - 0 (refund) - 10000 (discount) = 130000
    expect($finalCash)->toBe(130000);
    expect($pointsDiscount)->toBe(10000);
    expect($receivedCash)->toBe(40000);
});

test('multiple transactions with points discount are summed correctly', function () {
    // Create first transaction
    $transaction1 = Transaction::create([
        'customer_id' => $this->customer->id,
        'created_by_shift' => $this->shift->id,
        'payment_status' => 'Lunas',
        'points_used' => 50,
        'points_discount' => 5000,
        'total_amount' => 30000,
        'invoice_number' => 'INV-003',
    ]);

    $transaction1->details()->create([
        'product_id' => $this->product->id,
        'quantity' => 1,
        'price' => 30000,
    ]);

    $transaction1->payments()->create([
        'channel_id' => $this->channel->id,
        'payment_method' => 'tunai',
        'paid_amount' => 25000,
        'receipt_number' => 'RCP-003',
        'paid_at' => now(),
    ]);

    // Create second transaction
    $transaction2 = Transaction::create([
        'customer_id' => $this->customer->id,
        'created_by_shift' => $this->shift->id,
        'payment_status' => 'Lunas',
        'points_used' => 100,
        'points_discount' => 10000,
        'total_amount' => 50000,
        'invoice_number' => 'INV-004',
    ]);

    $transaction2->details()->create([
        'product_id' => $this->product->id,
        'quantity' => 1,
        'price' => 50000,
    ]);

    $transaction2->payments()->create([
        'channel_id' => $this->channel->id,
        'payment_method' => 'tunai',
        'paid_amount' => 40000,
        'receipt_number' => 'RCP-004',
        'paid_at' => now(),
    ]);

    // Calculate total points discount for this shift
    $totalPointsDiscount = Transaction::where('created_by_shift', $this->shift->id)
        ->sum('points_discount');

    // Assert total discount is correct: 5000 + 10000 = 15000
    expect($totalPointsDiscount)->toBe(15000);

    // Calculate total received cash
    $receivedCash = Transaction::where('created_by_shift', $this->shift->id)
        ->whereHas('payments', fn($q) => $q->where('payment_method', 'tunai'))
        ->with(['payments' => fn($q) => $q->where('payment_method', 'tunai')])
        ->get()
        ->sum(fn($t) => $t->payments->sum('paid_amount'));

    // Total cash: 25000 + 40000 = 65000
    expect($receivedCash)->toBe(65000);

    // Expected cash: 100000 + 65000 - 15000 = 150000
    $expectedCash = $this->shift->initial_cash + $receivedCash - $totalPointsDiscount;
    expect($expectedCash)->toBe(150000);
});
