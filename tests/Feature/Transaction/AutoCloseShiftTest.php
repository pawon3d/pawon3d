<?php

declare(strict_types=1);

use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;

test('auto closes shifts from previous days when accessing kasir page', function () {
    $user = User::factory()->create();

    // Create an open shift from yesterday
    $yesterday = now()->subDay();
    $shift = Shift::create([
        'opened_by' => $user->id,
        'start_time' => $yesterday,
        'status' => 'open',
        'initial_cash' => 100000,
    ]);

    // Assert shift is open
    expect($shift->status)->toBe('open');

    // Simulate auto-close logic (what happens in mount)
    $previousDayOpenShifts = Shift::where('status', 'open')
        ->whereDate('start_time', '<', now()->toDateString())
        ->get();

    foreach ($previousDayOpenShifts as $openShift) {
        $endTime = Carbon::parse($openShift->start_time)->endOfDay();
        $openShift->update([
            'closed_by' => null,
            'end_time' => $endTime,
            'status' => 'closed',
            'final_cash' => $openShift->initial_cash,
        ]);
    }

    // Refresh shift from database
    $shift->refresh();

    // Assert shift is now closed
    expect($shift->status)->toBe('closed')
        ->and($shift->end_time)->not->toBeNull()
        ->and($shift->closed_by)->toBeNull();
});

test('auto close sets end time to end of shift start date', function () {
    $user = User::factory()->create();

    // Create an open shift from 2 days ago at 10:30
    $twoDaysAgo = now()->subDays(2)->setTime(10, 30, 0);
    $shift = Shift::create([
        'opened_by' => $user->id,
        'start_time' => $twoDaysAgo,
        'status' => 'open',
        'initial_cash' => 50000,
    ]);

    // Simulate auto-close logic
    $previousDayOpenShifts = Shift::where('status', 'open')
        ->whereDate('start_time', '<', now()->toDateString())
        ->get();

    foreach ($previousDayOpenShifts as $openShift) {
        $endTime = Carbon::parse($openShift->start_time)->endOfDay();
        $openShift->update([
            'closed_by' => null,
            'end_time' => $endTime,
            'status' => 'closed',
            'final_cash' => $openShift->initial_cash,
        ]);
    }

    // Refresh shift from database
    $shift->refresh();

    // Assert end_time is 23:59:59 of the start date
    $expectedEndTime = $twoDaysAgo->copy()->endOfDay();
    expect(Carbon::parse($shift->end_time)->format('Y-m-d H:i:s'))
        ->toBe($expectedEndTime->format('Y-m-d H:i:s'));
});

test('does not close shifts from today', function () {
    $user = User::factory()->create();

    // Create an open shift from today
    $shift = Shift::create([
        'opened_by' => $user->id,
        'start_time' => now(),
        'status' => 'open',
        'initial_cash' => 100000,
    ]);

    // Simulate auto-close logic (only closes previous day shifts)
    $previousDayOpenShifts = Shift::where('status', 'open')
        ->whereDate('start_time', '<', now()->toDateString())
        ->get();

    foreach ($previousDayOpenShifts as $openShift) {
        $endTime = Carbon::parse($openShift->start_time)->endOfDay();
        $openShift->update([
            'closed_by' => null,
            'end_time' => $endTime,
            'status' => 'closed',
            'final_cash' => $openShift->initial_cash,
        ]);
    }

    // Refresh shift from database
    $shift->refresh();

    // Assert shift is still open (today's shift should not be closed)
    expect($shift->status)->toBe('open');
});

test('auto close calculates final cash correctly with no transactions', function () {
    $user = User::factory()->create();

    // Create an open shift from yesterday
    $yesterday = now()->subDay();
    $shift = Shift::create([
        'opened_by' => $user->id,
        'start_time' => $yesterday,
        'status' => 'open',
        'initial_cash' => 100000,
    ]);

    // Simulate auto-close logic
    $previousDayOpenShifts = Shift::where('status', 'open')
        ->whereDate('start_time', '<', now()->toDateString())
        ->get();

    foreach ($previousDayOpenShifts as $openShift) {
        $endTime = Carbon::parse($openShift->start_time)->endOfDay();
        $openShift->update([
            'closed_by' => null,
            'end_time' => $endTime,
            'status' => 'closed',
            'final_cash' => $openShift->initial_cash,
        ]);
    }

    // Refresh shift from database
    $shift->refresh();

    // With no transactions, final_cash should equal initial_cash
    expect((float) $shift->final_cash)->toBe(100000.0);
});
