<?php

declare(strict_types=1);

use App\Livewire\Dashboard\LaporanKasir;
use App\Models\StoreProfile;
use App\Models\User;
use Illuminate\Support\Facades\View;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $profile = StoreProfile::firstOrCreate(
        ['id' => 1],
        ['name' => 'Test Store', 'address' => 'Test Address', 'phone' => '08123456789']
    );
    View::share('storeProfile', $profile);

    Permission::firstOrCreate(['name' => 'kasir.laporan.kelola']);
    Permission::firstOrCreate(['name' => 'produksi.laporan.kelola']);
    Permission::firstOrCreate(['name' => 'inventori.laporan.kelola']);

    $this->user = User::factory()->create();
    $this->user->givePermissionTo('kasir.laporan.kelola', 'produksi.laporan.kelola', 'inventori.laporan.kelola');
    $this->actingAs($this->user);
});

// TC-126: Pengguna dapat mengakses halaman laporan
test('user can access laporan kasir page', function () {
    $this->get(route('laporan-kasir'))
        ->assertOk()
        ->assertSeeLivewire(LaporanKasir::class);
});

test('user can access laporan produksi page', function () {
    $this->get(route('laporan-produksi'))
        ->assertOk()
        ->assertSeeLivewire(\App\Livewire\Dashboard\LaporanProduksi::class);
});

test('user can access laporan inventori page', function () {
    $this->get(route('laporan-inventori'))
        ->assertOk()
        ->assertSeeLivewire(\App\Livewire\Dashboard\LaporanInventori::class);
});

// TC-127: Pengguna memfilter laporan berdasarkan periode
test('can filter laporan by period', function () {
    Livewire::test(LaporanKasir::class)
        ->call('loadData')
        ->set('filterPeriod', 'Bulan')
        ->assertSet('filterPeriod', 'Bulan')
        ->assertHasNoErrors();
});

test('can filter laporan by week period', function () {
    Livewire::test(LaporanKasir::class)
        ->call('loadData')
        ->set('filterPeriod', 'Minggu')
        ->assertSet('filterPeriod', 'Minggu')
        ->assertHasNoErrors();
});

// TC-128: Pengguna memfilter laporan dengan periode Custom
test('can filter laporan by custom date range', function () {
    Livewire::test(LaporanKasir::class)
        ->call('loadData')
        ->set('filterPeriod', 'Custom')
        ->set('customStartDate', '2026-02-01')
        ->set('customEndDate', '2026-02-21')
        ->assertSet('filterPeriod', 'Custom')
        ->assertSet('customStartDate', '2026-02-01')
        ->assertSet('customEndDate', '2026-02-21')
        ->assertHasNoErrors();
});
