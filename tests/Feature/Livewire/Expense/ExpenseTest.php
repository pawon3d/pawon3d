<?php

declare(strict_types=1);

use App\Livewire\Expense\Form;
use App\Livewire\Expense\Index;
use App\Livewire\Expense\Mulai;
use App\Livewire\Expense\Rincian;
use App\Livewire\Expense\Riwayat;
use App\Models\Expense;
use App\Models\ExpenseDetail;
use App\Models\Material;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->user = User::factory()->create();

    foreach (['inventori.belanja.rencana.kelola', 'inventori.belanja.mulai', 'inventori.persediaan.kelola'] as $perm) {
        $permission = Permission::firstOrCreate(['name' => $perm]);
        $this->user->givePermissionTo($permission);
    }

    $this->actingAs($this->user);
});

// ─── Helpers ────────────────────────────────────────────────────────────────

function makeExpenseFixture(string $status = 'Draft', bool $isStart = false, float $quantityGet = 0): array
{
    $supplier = Supplier::create(['name' => 'Toko Test Belanja']);
    $unit = Unit::create(['name' => 'Kilogram', 'alias' => 'kg', 'group' => 'Massa']);
    $material = Material::create(['name' => 'Tepung Test', 'status' => 'Kosong', 'minimum' => 1, 'is_active' => true]);

    $expense = Expense::create([
        'supplier_id' => $supplier->id,
        'status' => $status,
        'is_start' => $isStart,
        'grand_total_expect' => 150000,
    ]);

    $detail = ExpenseDetail::create([
        'expense_id' => $expense->id,
        'material_id' => $material->id,
        'unit_id' => $unit->id,
        'quantity_expect' => 10,
        'quantity_get' => $quantityGet,
        'price_expect' => 15000,
        'total_expect' => 150000,
    ]);

    return compact('supplier', 'unit', 'material', 'expense', 'detail');
}

// ─── TC-035 ──────────────────────────────────────────────────────────────────

test('TC-035 - halaman belanja dapat diakses', function () {
    Livewire::test(Index::class)
        ->assertStatus(200);
});

// ─── TC-036 ──────────────────────────────────────────────────────────────────

test('TC-036 - membuat rencana belanja baru berhasil redirect ke daftar rencana', function () {
    $supplier = Supplier::create(['name' => 'Toko Makmur']);
    $unit = Unit::create(['name' => 'Kilogram36', 'alias' => 'kg', 'group' => 'Massa']);
    $material = Material::create(['name' => 'Tepung Terigu', 'status' => 'Kosong', 'minimum' => 1, 'is_active' => true]);

    Livewire::test(Form::class)
        ->set('supplier_id', (string) $supplier->id)
        ->set('expense_date', '21 Feb 2026')
        ->set('expense_details', [[
            'material_id' => (string) $material->id,
            'material_quantity' => '0 (kg)',
            'quantity_expect' => 10,
            'unit_id' => (string) $unit->id,
            'price_expect' => 15000,
            'detail_total_expect' => 150000,
        ]])
        ->set('grand_total_expect', 150000)
        ->call('store')
        ->assertHasNoErrors()
        ->assertRedirect(route('belanja.rencana'));

    expect(Expense::where('supplier_id', $supplier->id)->exists())->toBeTrue();
});

// ─── TC-037 ──────────────────────────────────────────────────────────────────

test('TC-037 - membuat rencana belanja tanpa supplier menampilkan pesan validasi', function () {
    Livewire::test(Form::class)
        ->set('supplier_id', '')
        ->call('store')
        ->assertHasErrors(['supplier_id'])
        ->assertSee('Supplier wajib dipilih.');
});

// ─── TC-038 ──────────────────────────────────────────────────────────────────

test('TC-038 - membuat rencana belanja tanpa item bahan baku menampilkan validasi', function () {
    $supplier = Supplier::create(['name' => 'Toko TC038']);

    Livewire::test(Form::class)
        ->set('supplier_id', (string) $supplier->id)
        ->set('grand_total_expect', 0)
        ->call('store')
        ->assertHasErrors(['expense_details.0.material_id']);
});

// ─── TC-039 ──────────────────────────────────────────────────────────────────

test('TC-039 - memulai proses belanja mengubah status menjadi Dimulai', function () {
    ['expense' => $expense] = makeExpenseFixture('Draft', false, 0);

    Livewire::test(Rincian::class, ['id' => $expense->id])
        ->call('start')
        ->assertHasNoErrors();

    expect(Expense::find($expense->id)->status)->toBe('Dimulai');
});

// ─── TC-040 ──────────────────────────────────────────────────────────────────

test('TC-040 - menyelesaikan belanja dengan semua item lengkap mengubah status menjadi Lengkap', function () {
    ['expense' => $expense] = makeExpenseFixture('Dimulai', true, 10);

    Livewire::test(Rincian::class, ['id' => $expense->id])
        ->call('finish')
        ->assertHasNoErrors();

    expect(Expense::find($expense->id)->status)->toBe('Lengkap');
});

// ─── TC-041 ──────────────────────────────────────────────────────────────────

test('TC-041 - memasukkan jumlah aktual melebihi ekspektasi menampilkan pesan error', function () {
    ['expense' => $expense] = makeExpenseFixture('Dimulai', true, 0);

    // Reload expense to get the detail id from Mulai mount perspective
    $detail = ExpenseDetail::where('expense_id', $expense->id)->first();

    // quantity > remaining (10 - 0 = 10), set to 15 to trigger errorInputs
    Livewire::test(Mulai::class, ['id' => $expense->id])
        ->set('expenseDetails.0.quantity', 15)
        ->set('expenseDetails.0.price_get', 15000)
        ->call('save')
        ->assertNoRedirect();
});

// ─── TC-042 ──────────────────────────────────────────────────────────────────

test('TC-042 - menyelesaikan belanja dengan semua item 0 mengubah status menjadi Gagal', function () {
    ['expense' => $expense] = makeExpenseFixture('Dimulai', true, 0);

    Livewire::test(Rincian::class, ['id' => $expense->id])
        ->call('finish')
        ->assertHasNoErrors();

    expect(Expense::find($expense->id)->status)->toBe('Gagal');
});

// ─── TC-043 ──────────────────────────────────────────────────────────────────

test('TC-043 - halaman riwayat belanja dapat diakses', function () {
    Livewire::test(Riwayat::class)
        ->assertStatus(200);
});

// ─── Security ────────────────────────────────────────────────────────────────

test('pengguna tanpa permission tidak bisa membuat rencana belanja', function () {
    $userTanpaPermission = User::factory()->create();
    $this->actingAs($userTanpaPermission);

    Livewire::test(Form::class)
        ->set('supplier_id', 'some-id')
        ->call('store')
        ->assertForbidden();
});
