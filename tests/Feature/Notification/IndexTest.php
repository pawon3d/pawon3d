<?php

declare(strict_types=1);

use App\Livewire\Notification\Index;
use App\Models\Notification;
use App\Models\StoreProfile;
use App\Models\User;
use Illuminate\Support\Facades\View;
use Livewire\Livewire;

beforeEach(function () {
    $profile = StoreProfile::firstOrCreate(
        ['id' => 1],
        ['name' => 'Test Store', 'address' => 'Test Address', 'phone' => '08123456789']
    );
    View::share('storeProfile', $profile);

    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

// TC-123: Pengguna dapat mengakses halaman notifikasi
test('user can access notifications page', function () {
    $this->get(route('notifikasi'))
        ->assertOk()
        ->assertSeeLivewire(Index::class);
});

// TC-124: Pengguna menandai satu notifikasi sebagai dibaca
test('can mark single notification as read', function () {
    $notification = Notification::create([
        'user_id' => $this->user->id,
        'title' => 'Test Notification',
        'body' => 'Test Body',
        'type' => 'kasir',
        'is_read' => false,
    ]);

    Livewire::test(Index::class)
        ->call('markAsRead', $notification->id)
        ->assertRedirect(route('notifikasi'));

    $notification->refresh();
    expect($notification->is_read)->toBeTrue();
});

// TC-125: Pengguna menandai semua notifikasi sebagai dibaca
test('can mark all notifications as read', function () {
    Notification::create([
        'user_id' => $this->user->id,
        'title' => 'Notification 1',
        'body' => 'Body 1',
        'type' => 'kasir',
        'is_read' => false,
    ]);
    Notification::create([
        'user_id' => $this->user->id,
        'title' => 'Notification 2',
        'body' => 'Body 2',
        'type' => 'kasir',
        'is_read' => false,
    ]);

    Livewire::test(Index::class)
        ->call('markAllAsRead')
        ->assertRedirect(route('notifikasi'));

    $unreadCount = Notification::where('user_id', $this->user->id)
        ->where('is_read', false)
        ->count();
    expect($unreadCount)->toBe(0);
});
