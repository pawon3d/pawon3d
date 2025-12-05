<?php

use App\Livewire\Dashboard\LaporanKasir;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('laporan kasir component renders correctly', function () {
    $user = User::factory()->create();

    Livewire\Livewire::actingAs($user)
        ->test(LaporanKasir::class)
        ->assertStatus(200)
        ->call('loadData') // Trigger lazy loading
        ->assertSee('Laporan Kasir')
        ->assertSee('10 Produk Terlaris')
        ->assertSee('Penjualan Produk')
        ->assertSee('Rincian Penjualan');
});

it('can search products by name', function () {
    $user = User::factory()->create();

    $component = Livewire\Livewire::actingAs($user)
        ->test(LaporanKasir::class);

    $component->set('searchProduct', 'test')
        ->assertSet('searchProduct', 'test')
        ->assertSet('currentPage', 1);
});

it('can search monthly reports', function () {
    $user = User::factory()->create();

    $component = Livewire\Livewire::actingAs($user)
        ->test(LaporanKasir::class);

    $component->set('searchReport', 'Jan')
        ->assertSet('searchReport', 'Jan')
        ->assertSet('currentPage', 1);
});

it('resets page when search product changes', function () {
    $user = User::factory()->create();

    $component = Livewire\Livewire::actingAs($user)
        ->test(LaporanKasir::class)
        ->set('currentPage', 2)
        ->set('searchProduct', 'new search');

    $component->assertSet('currentPage', 1);
});

it('resets page when search report changes', function () {
    $user = User::factory()->create();

    $component = Livewire\Livewire::actingAs($user)
        ->test(LaporanKasir::class)
        ->set('currentPage', 2)
        ->set('searchReport', 'new search');

    $component->assertSet('currentPage', 1);
});

it('can change filter period', function () {
    $user = User::factory()->create();

    $component = Livewire\Livewire::actingAs($user)
        ->test(LaporanKasir::class)
        ->call('loadData'); // Trigger lazy loading first

    $component->call('setFilterPeriod', 'Bulan');

    $component->assertSet('filterPeriod', 'Bulan')
        ->assertDispatched('update-charts');
});

it('can select date from calendar', function () {
    $user = User::factory()->create();

    $component = Livewire\Livewire::actingAs($user)
        ->test(LaporanKasir::class)
        ->call('loadData'); // Trigger lazy loading first

    $component->call('selectDate', now()->subDay()->toDateString());

    $component->assertSet('selectedDate', now()->subDay()->toDateString())
        ->assertDispatched('update-charts');
});
