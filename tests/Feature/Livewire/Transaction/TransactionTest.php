<?php

declare(strict_types=1);

use App\Livewire\Transaction\BuatPesanan;
use App\Livewire\Transaction\Edit;
use App\Livewire\Transaction\Index;
use App\Models\Payment;
use App\Models\PaymentChannel;
use App\Models\Product;
use App\Models\Shift;
use App\Models\Transaction;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    foreach ([
        'kasir.pesanan.kelola',
        'kasir.laporan.kelola',
    ] as $perm) {
        Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
    }

    $this->user = User::factory()->create();
    $this->user->givePermissionTo(['kasir.pesanan.kelola', 'kasir.laporan.kelola']);
});

// ─────────────────────────────────────────────
// Helpers
// ─────────────────────────────────────────────

/** Create an active open Shift for today, return the Shift model. */
function makeShift(mixed $userId): Shift
{
    return Shift::create([
        'opened_by' => (string) $userId,
        'start_time' => now(),
        'status' => 'open',
        'initial_cash' => 500000,
    ]);
}

/** Create a simple is_recipe Product with a given stock. */
function makeProduct(int $stock = 5, string $method = 'siap-beli'): Product
{
    return Product::create([
        'name' => 'Produk Test '.uniqid(),
        'price' => 30000,
        'stock' => $stock,
        'is_recipe' => true,
        'method' => [$method],
    ]);
}

/** Create a Transaction + optionally attach product details. Return Transaction. */
function makeTransaction(mixed $userId, string $method = 'pesanan-reguler', mixed $shiftId = null): Transaction
{
    return Transaction::factory()->create([
        'user_id' => (string) $userId,
        'method' => $method,
        'status' => 'temp',
        'payment_status' => 'Belum Lunas',
        'created_by_shift' => $shiftId ? (string) $shiftId : null,
        'total_amount' => 120000,
    ]);
}

// ─────────────────────────────────────────────
// TC-067: Buka sesi (open shift)
// ─────────────────────────────────────────────

it('TC-067: opens shift and creates shift record', function () {
    $component = Livewire::actingAs($this->user)
        ->test(Index::class)
        ->set('initialCash', 500000)
        ->call('openShift');

    $component->assertHasNoErrors();

    expect(Shift::where('status', 'open')->whereDate('start_time', now())->exists())->toBeTrue();
});

// ─────────────────────────────────────────────
// TC-068: Access transaction list
// ─────────────────────────────────────────────

it('TC-068: transaction index page renders for kasir user', function () {
    $this->actingAs($this->user)
        ->get(route('transaksi'))
        ->assertStatus(200);
});

// ─────────────────────────────────────────────
// TC-069: Create pesanan-kotak transaction (checkout → redirect to buat-pesanan)
// ─────────────────────────────────────────────

it('TC-069: checkout for pesanan-kotak creates transaction and redirects', function () {
    $product = makeProduct(10, 'pesanan-kotak');
    $shift = makeShift($this->user->id);

    $component = Livewire::actingAs($this->user)
        ->test(Index::class)
        ->set('method', 'pesanan-kotak')
        ->set('todayShiftId', (string) $shift->id)
        ->call('addToCart', (string) $product->id)
        ->call('checkout');

    $component->assertRedirect(route('transaksi.buat-pesanan', ['id' => Transaction::latest()->first()->id]));

    expect(Transaction::where('method', 'pesanan-kotak')->where('status', 'temp')->exists())->toBeTrue();
});

// ─────────────────────────────────────────────
// TC-070: Create pesanan-reguler transaction
// ─────────────────────────────────────────────

it('TC-070: checkout for pesanan-reguler creates transaction and redirects', function () {
    $product = makeProduct(10, 'pesanan-reguler');
    $shift = makeShift($this->user->id);

    $component = Livewire::actingAs($this->user)
        ->test(Index::class)
        ->set('method', 'pesanan-reguler')
        ->set('todayShiftId', (string) $shift->id)
        ->call('addToCart', (string) $product->id)
        ->call('checkout');

    $component->assertRedirect();

    expect(Transaction::where('method', 'pesanan-reguler')->where('status', 'temp')->exists())->toBeTrue();
});

// ─────────────────────────────────────────────
// TC-071: Create siap-beli transaction with stock
// ─────────────────────────────────────────────

it('TC-071: checkout for siap-beli creates transaction when product has stock', function () {
    $product = makeProduct(5, 'siap-beli');
    $shift = makeShift($this->user->id);

    $component = Livewire::actingAs($this->user)
        ->test(Index::class)
        ->set('method', 'siap-beli')
        ->set('todayShiftId', (string) $shift->id)
        ->call('addToCart', (string) $product->id)
        ->call('checkout');

    $component->assertRedirect();

    expect(Transaction::where('method', 'siap-beli')->where('status', 'temp')->exists())->toBeTrue();
});

// ─────────────────────────────────────────────
// TC-072: Add siap-beli product with stock=0 → alert warning
// ─────────────────────────────────────────────

it('TC-072: adding siap-beli product with stock 0 shows warning and does not add to cart', function () {
    $product = makeProduct(0, 'siap-beli');

    $component = Livewire::actingAs($this->user)
        ->test(Index::class)
        ->set('method', 'siap-beli')
        ->call('addToCart', (string) $product->id);

    // Cart should remain empty
    expect($component->get('cart'))->toBeEmpty();
});

// ─────────────────────────────────────────────
// TC-073: Add product to transaction → appears in details
// ─────────────────────────────────────────────

it('TC-073: adding product to cart appears in cart with quantity 1', function () {
    $product = makeProduct(10, 'pesanan-reguler');

    $component = Livewire::actingAs($this->user)
        ->test(Index::class)
        ->set('method', 'pesanan-reguler')
        ->call('addToCart', (string) $product->id);

    $cart = $component->get('cart');
    expect($cart)->not->toBeEmpty();
    expect($cart[(string) $product->id]['quantity'])->toBe(1);
});

// ─────────────────────────────────────────────
// TC-074: Customer phone lookup in BuatPesanan
// ─────────────────────────────────────────────

it('TC-074: entering known customer phone auto-fills customer name in BuatPesanan', function () {
    $customer = \App\Models\Customer::create([
        'name' => 'Pelanggan Test',
        'phone' => '081234567890',
    ]);

    $transaction = makeTransaction($this->user->id, 'pesanan-reguler');

    Livewire::actingAs($this->user)
        ->test(BuatPesanan::class, ['id' => (string) $transaction->id])
        ->set('phone', '081234567890')
        ->assertSet('name', 'Pelanggan Test');
});

// ─────────────────────────────────────────────
// TC-075: Payment >= 50% for pesanan → transaction created
// ─────────────────────────────────────────────

it('TC-075: paying at least 50% total for pesanan processes successfully', function () {
    $product = makeProduct(10, 'pesanan-reguler');
    $transaction = makeTransaction($this->user->id, 'pesanan-reguler');
    $transaction->details()->create([
        'product_id' => (string) $product->id,
        'quantity' => 1,
        'price' => 120000,
    ]);

    Livewire::actingAs($this->user)
        ->test(BuatPesanan::class, ['id' => (string) $transaction->id])
        ->set('name', 'Budi Santoso')
        ->set('phone', '081234567890')
        ->set('date', now()->format('d M Y'))
        ->set('time', '10:00')
        ->set('paymentGroup', 'tunai')
        ->set('paymentMethod', 'tunai')
        ->set('paidAmount', 120000)
        ->call('pay')
        ->assertHasNoErrors()
        ->assertRedirect(route('transaksi.rincian-pesanan', ['id' => (string) $transaction->id]));
});

// ─────────────────────────────────────────────
// TC-076: Payment < 50% for pesanan → error
// ─────────────────────────────────────────────

it('TC-076: paying less than 50% total for pesanan shows warning and does not redirect', function () {
    $product = makeProduct(10, 'pesanan-reguler');
    $transaction = makeTransaction($this->user->id, 'pesanan-reguler');
    $transaction->details()->create([
        'product_id' => (string) $product->id,
        'quantity' => 1,
        'price' => 240000,
    ]);

    $component = Livewire::actingAs($this->user)
        ->test(BuatPesanan::class, ['id' => (string) $transaction->id])
        ->set('name', 'Budi Santoso')
        ->set('phone', '081234567890')
        ->set('date', now()->format('d M Y'))
        ->set('time', '10:00')
        ->set('paymentGroup', 'tunai')
        ->set('paymentMethod', 'tunai')
        ->set('paidAmount', 80000); // Less than 50% of 240000

    // Calling pay with insufficient amount triggers Laravel warning (not a redirect)
    $component->call('pay');

    // Transaction status should remain 'temp' / unchanged (no redirect happened)
    $transaction->refresh();
    expect($transaction->status)->toBe('temp');
});

// ─────────────────────────────────────────────
// TC-077: Siap-beli payment < total → error
// ─────────────────────────────────────────────

it('TC-077: siap-beli payment less than total shows warning and does not complete', function () {
    $product = makeProduct(5, 'siap-beli');
    $transaction = makeTransaction($this->user->id, 'siap-beli');
    $transaction->details()->create([
        'product_id' => (string) $product->id,
        'quantity' => 1,
        'price' => 120000,
    ]);

    $component = Livewire::actingAs($this->user)
        ->test(BuatPesanan::class, ['id' => (string) $transaction->id])
        ->set('paymentGroup', 'tunai')
        ->set('paymentMethod', 'tunai')
        ->set('paidAmount', 100000); // less than 120000

    $component->call('pay');

    // Should not redirect — transaction status stays 'temp'
    $transaction->refresh();
    expect($transaction->status)->toBe('temp');
});

// ─────────────────────────────────────────────
// TC-078: Payment without name/phone → validation error
// ─────────────────────────────────────────────

it('TC-078: paying pesanan without required name and phone shows validation errors', function () {
    $product = makeProduct(10, 'pesanan-reguler');
    $transaction = makeTransaction($this->user->id, 'pesanan-reguler');
    $transaction->details()->create([
        'product_id' => (string) $product->id,
        'quantity' => 1,
        'price' => 120000,
    ]);

    Livewire::actingAs($this->user)
        ->test(BuatPesanan::class, ['id' => (string) $transaction->id])
        ->set('name', '')
        ->set('phone', '')
        ->set('date', now()->format('d M Y'))
        ->set('time', '10:00')
        ->set('paymentGroup', 'tunai')
        ->set('paymentMethod', 'tunai')
        ->set('paidAmount', 120000)
        ->call('pay')
        ->assertHasErrors(['name', 'phone']);
});

// ─────────────────────────────────────────────
// TC-079: Payment without date/time → validation error
// ─────────────────────────────────────────────

it('TC-079: paying pesanan without date and time shows validation errors', function () {
    $product = makeProduct(10, 'pesanan-reguler');
    $transaction = makeTransaction($this->user->id, 'pesanan-reguler');
    $transaction->details()->create([
        'product_id' => (string) $product->id,
        'quantity' => 1,
        'price' => 120000,
    ]);

    Livewire::actingAs($this->user)
        ->test(BuatPesanan::class, ['id' => (string) $transaction->id])
        ->set('name', 'Budi')
        ->set('phone', '081234567890')
        ->set('date', '')
        ->set('time', '')
        ->set('paymentGroup', 'tunai')
        ->set('paymentMethod', 'tunai')
        ->set('paidAmount', 120000)
        ->call('pay')
        ->assertHasErrors(['date', 'time']);
});

// ─────────────────────────────────────────────
// TC-080: Non-tunai loads payment methods
// ─────────────────────────────────────────────

it('TC-080: selecting non-tunai loads payment methods in BuatPesanan', function () {
    $transaction = makeTransaction($this->user->id, 'pesanan-reguler');

    PaymentChannel::create([
        'type' => 'transfer',
        'group' => 'non-tunai',
        'bank_name' => 'Bank Test',
        'account_number' => '1234567890',
        'account_name' => 'Kasir Test',
        'is_active' => true,
    ]);

    $component = Livewire::actingAs($this->user)
        ->test(BuatPesanan::class, ['id' => (string) $transaction->id])
        ->set('paymentGroup', 'non-tunai');

    $paymentMethods = $component->get('paymentMethods');

    expect($paymentMethods)->toHaveCount(1);
});

// ─────────────────────────────────────────────
// TC-080A: Edit loads payment channels for transfer
// ─────────────────────────────────────────────

it('TC-080A: edit loads payment channels for transfer without type error', function () {
    $transaction = makeTransaction($this->user->id, 'pesanan-reguler');
    $transaction->update([
        'date' => now()->addDays(4)->toDateString(),
        'time' => now()->format('H:i:s'),
    ]);

    $channel = PaymentChannel::create([
        'type' => 'transfer',
        'group' => 'non-tunai',
        'bank_name' => 'Bank Test',
        'account_number' => '1234567890',
        'account_name' => 'Kasir Test',
        'is_active' => true,
    ]);

    Payment::create([
        'transaction_id' => $transaction->id,
        'payment_channel_id' => $channel->id,
        'payment_method' => 'transfer',
        'payment_group' => 'non-tunai',
        'paid_amount' => 100000,
        'paid_at' => now(),
    ]);

    $component = Livewire::actingAs($this->user)
        ->test(Edit::class, ['id' => (string) $transaction->id]);

    $component->assertSet('paymentMethod', 'transfer')
        ->assertSet('paymentChannelId', $channel->id);
});

// ─────────────────────────────────────────────
// TC-081: View transaction history
// ─────────────────────────────────────────────

it('TC-081: transaction riwayat page renders for kasir user', function () {
    $this->actingAs($this->user)
        ->get(route('transaksi.riwayat', ['method' => 'pesanan-reguler']))
        ->assertStatus(200);
});

// ─────────────────────────────────────────────
// TC-082: Close shift
// ─────────────────────────────────────────────

it('TC-082: closing shift sets shift status to closed', function () {
    $shift = makeShift($this->user->id);

    $component = Livewire::actingAs($this->user)
        ->test(Index::class)
        ->set('todayShiftId', (string) $shift->id)
        ->set('finalCash', 750000)
        ->call('closeShift');

    $component->assertHasNoErrors();

    $shift->refresh();
    expect($shift->status)->toBe('closed');
    expect($shift->final_cash)->toBe(750000);
});

// ─────────────────────────────────────────────
// Security: guest cannot checkout
// ─────────────────────────────────────────────

it('guest is redirected away from transaksi page', function () {
    $this->get(route('transaksi'))
        ->assertRedirect();
});

// ─────────────────────────────────────────────
// Security: user without permission gets 403 on checkout
// ─────────────────────────────────────────────

it('user without kasir permission gets 403 on checkout', function () {
    $unauthorised = User::factory()->create();

    $product = makeProduct(10, 'pesanan-reguler');

    Livewire::actingAs($unauthorised)
        ->test(Index::class)
        ->set('method', 'pesanan-reguler')
        ->call('addToCart', (string) $product->id)
        ->call('checkout')
        ->assertForbidden();
});
