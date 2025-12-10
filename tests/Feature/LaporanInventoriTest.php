<?php

declare(strict_types=1);

use App\Models\Expense;
use App\Models\User;
use Livewire\Livewire;
use function Pest\Laravel\actingAs;

it('shows correct total expense for day and week filters', function () {
    // Create a user and act as them (if needed by the component)
    $user = User::factory()->create();
    actingAs($user);

    // Date to test
    $date = '2025-12-10';
    $weekSecondDate = '2025-12-11';

    // Create expenses: one on selected date, one on another date in same week
    Expense::create(['expense_date' => $date, 'supplier_id' => null, 'note' => 'Test A', 'grand_total_expect' => 0, 'grand_total_actual' => 0]);
    Expense::create(['expense_date' => $weekSecondDate, 'supplier_id' => null, 'note' => 'Test B', 'grand_total_expect' => 0, 'grand_total_actual' => 0]);

    // Livewire component: day filter
    Livewire::test(App\Livewire\Dashboard\LaporanInventori::class)
        ->call('loadData')
        ->set('selectedDate', $date)
        ->set('filterPeriod', 'Hari')
        ->assertViewHas('totalExpense', 1);

    // Livewire component: week filter
    Livewire::test(App\Livewire\Dashboard\LaporanInventori::class)
        ->call('loadData')
        ->set('selectedDate', $date)
        ->set('filterPeriod', 'Minggu')
        ->assertViewHas('totalExpense', 2);

    // Calendar shows expense dot for day with expense
    Livewire::test(App\Livewire\Dashboard\LaporanInventori::class)
        ->call('loadData')
        ->set('selectedDate', $date)
        ->set('filterPeriod', 'Hari')
        ->set('currentMonth', '2025-12-01')
        ->assertSeeHtml('bg-[#f59e0b]');
});
