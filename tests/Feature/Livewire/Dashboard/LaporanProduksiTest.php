<?php

use App\Livewire\Dashboard\LaporanProduksi;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('laporan produksi component renders correctly', function () {
    $user = User::factory()->create();

    Livewire\Livewire::actingAs($user)
        ->test(LaporanProduksi::class)
        ->assertStatus(200)
        ->assertSee('Laporan Produksi')
        ->assertSee('10 Produksi Tertinggi')
        ->assertSee('Metode Produksi Teratas')
        ->assertSee('Produksi Produk');
});

it('chart update event dispatched when method changes', function () {
    $user = User::factory()->create();

    $component = Livewire\Livewire::actingAs($user)
        ->test(LaporanProduksi::class);

    // change method - should schedule chart update
    $component->set('selectedMethod', 'pesanan-reguler');

    $component->assertDispatched('update-charts');
});

it('chart update event dispatched when filter period changes', function () {
    $user = User::factory()->create();

    $component = Livewire\Livewire::actingAs($user)
        ->test(LaporanProduksi::class);

    $component->call('setFilterPeriod', 'Bulan');

    $component->assertDispatched('update-charts');
});

it('can select date from calendar', function () {
    $user = User::factory()->create();

    $component = Livewire\Livewire::actingAs($user)
        ->test(LaporanProduksi::class);

    $component->call('selectDate', now()->subDay()->toDateString());

    $component->assertSet('selectedDate', now()->subDay()->toDateString())
        ->assertDispatched('update-charts');
});

it('can search products by name', function () {
    $user = User::factory()->create();

    $component = Livewire\Livewire::actingAs($user)
        ->test(LaporanProduksi::class);

    $component->set('search', 'test')
        ->assertSet('search', 'test')
        ->assertSet('currentPage', 1);
});

it('resets page when search changes', function () {
    $user = User::factory()->create();

    $component = Livewire\Livewire::actingAs($user)
        ->test(LaporanProduksi::class)
        ->set('currentPage', 2)
        ->set('search', 'new search');

    $component->assertSet('currentPage', 1);
});
