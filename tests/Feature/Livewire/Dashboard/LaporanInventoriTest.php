<?php

use App\Livewire\Dashboard\LaporanInventori;
use App\Models\User;
use Carbon\Carbon;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('laporan inventori component renders correctly', function () {
    $user = User::factory()->create();

    Livewire\Livewire::actingAs($user)
        ->test(LaporanInventori::class)
        ->assertStatus(200)
        ->assertSee('Laporan Inventori')
        ->assertSee('Sesi Belanja Persediaan')
        ->assertSee('Nilai Persediaan')
        ->assertSee('Nilai Persediaan Terpakai')
        ->assertSee('Nilai Persediaan Saat Ini')
        ->assertSee('10 Persediaan Banyak Digunakan')
        ->assertSee('Persediaan Banyak Digunakan')
        ->assertSee('Persediaan Sedikit Digunanakan')
        ->assertSee('Persediaan');
});

test('custom calendar can navigate months', function () {
    $user = User::factory()->create();

    $component = Livewire\Livewire::actingAs($user)
        ->test(LaporanInventori::class);

    $initialMonth = Carbon::now()->startOfMonth()->toDateString();
    $component->assertSet('currentMonth', $initialMonth);

    // Navigate to previous month
    $component->call('previousMonth');
    $expectedPrevMonth = Carbon::now()->subMonth()->startOfMonth()->toDateString();
    $component->assertSet('currentMonth', $expectedPrevMonth);

    // Navigate to next month (back to current)
    $component->call('nextMonth');
    $component->assertSet('currentMonth', $initialMonth);
});

test('custom calendar can select date', function () {
    $user = User::factory()->create();

    $component = Livewire\Livewire::actingAs($user)
        ->test(LaporanInventori::class);

    // Select a specific date
    $newDate = '2025-11-15';
    $component->call('selectDate', $newDate);

    $component->assertSet('selectedDate', $newDate);
    $component->assertSet('showCalendar', false);
});

test('calendar toggle works correctly', function () {
    $user = User::factory()->create();

    $component = Livewire\Livewire::actingAs($user)
        ->test(LaporanInventori::class);

    $component->assertSet('showCalendar', false);

    $component->call('toggleCalendar');
    $component->assertSet('showCalendar', true);

    $component->call('toggleCalendar');
    $component->assertSet('showCalendar', false);
});

test('filter period can be changed', function () {
    $user = User::factory()->create();

    $component = Livewire\Livewire::actingAs($user)
        ->test(LaporanInventori::class);

    // Default is 'Hari'
    $component->assertSet('filterPeriod', 'Hari');

    // Change to 'Minggu'
    $component->call('setFilterPeriod', 'Minggu');
    $component->assertSet('filterPeriod', 'Minggu');

    // Change to 'Bulan'
    $component->call('setFilterPeriod', 'Bulan');
    $component->assertSet('filterPeriod', 'Bulan');

    // Change to 'Tahun'
    $component->call('setFilterPeriod', 'Tahun');
    $component->assertSet('filterPeriod', 'Tahun');
});

test('chart update event dispatched when date changes', function () {
    $user = User::factory()->create();

    $component = Livewire\Livewire::actingAs($user)
        ->test(LaporanInventori::class);

    // Select a new date - this should trigger chart update
    $component->call('selectDate', '2025-01-15');

    $component->assertDispatched('update-charts');
});

test('chart update event dispatched when worker changes', function () {
    $user = User::factory()->create();

    $component = Livewire\Livewire::actingAs($user)
        ->test(LaporanInventori::class);

    // Change worker - this should trigger chart update
    $component->set('selectedWorker', 'all');

    $component->assertDispatched('update-charts');
});

test('chart update event dispatched when filter period changes', function () {
    $user = User::factory()->create();

    $component = Livewire\Livewire::actingAs($user)
        ->test(LaporanInventori::class);

    // Change filter period - this should trigger chart update
    $component->call('setFilterPeriod', 'Bulan');

    $component->assertDispatched('update-charts');
});
