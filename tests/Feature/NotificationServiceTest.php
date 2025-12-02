<?php

use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Clear notifications
    Notification::truncate();
});

test('order queued notification is created for users with kasir permission', function () {
    // Create permission and user
    $permission = Permission::firstOrCreate(['name' => 'kasir.pesanan.kelola', 'guard_name' => 'web']);
    $role = Role::firstOrCreate(['name' => 'Kasir Test', 'guard_name' => 'web']);
    $role->givePermissionTo($permission);

    $user = User::factory()->create();
    $user->assignRole($role);

    // Call the notification service
    NotificationService::orderQueued('OR-250512-0001');

    // Assert notification was created
    expect(Notification::count())->toBe(1);
    expect((string) Notification::first()->user_id)->toBe((string) $user->id);
    expect(Notification::first()->type)->toBe('kasir');
    expect(Notification::first()->body)->toContain('OR-250512-0001');
    expect(Notification::first()->body)->toContain('Antrian Pesanan');
});

test('order completed notification is created', function () {
    $permission = Permission::firstOrCreate(['name' => 'kasir.pesanan.kelola', 'guard_name' => 'web']);
    $role = Role::firstOrCreate(['name' => 'Kasir Test', 'guard_name' => 'web']);
    $role->givePermissionTo($permission);

    $user = User::factory()->create();
    $user->assignRole($role);

    NotificationService::orderCompleted('OR-250512-0002');

    expect(Notification::count())->toBe(1);
    expect(Notification::first()->body)->toContain('Selesai');
});

test('payment notifications are created correctly', function () {
    $permission = Permission::firstOrCreate(['name' => 'kasir.pesanan.kelola', 'guard_name' => 'web']);
    $role = Role::firstOrCreate(['name' => 'Kasir Test', 'guard_name' => 'web']);
    $role->givePermissionTo($permission);

    $user = User::factory()->create();
    $user->assignRole($role);

    // Test down payment
    NotificationService::paymentDownPayment('OR-250512-0003', 100000);

    expect(Notification::count())->toBe(1);
    expect(Notification::first()->body)->toContain('Uang Muka');
    expect(Notification::first()->body)->toContain('Rp100.000');

    Notification::truncate();

    // Test full payment
    NotificationService::paymentCompleted('OR-250512-0003', 200000);

    expect(Notification::first()->body)->toContain('Lunas');
    expect(Notification::first()->body)->toContain('Rp200.000');
});

test('production notification is created for users with production permission', function () {
    $permission = Permission::firstOrCreate(['name' => 'produksi.rencana.kelola', 'guard_name' => 'web']);
    $role = Role::firstOrCreate(['name' => 'Produksi Test', 'guard_name' => 'web']);
    $role->givePermissionTo($permission);

    $user = User::factory()->create();
    $user->assignRole($role);

    NotificationService::productionPlanned('PS-250512-0001');

    expect(Notification::count())->toBe(1);
    expect(Notification::first()->type)->toBe('produksi');
    expect(Notification::first()->body)->toContain('direncanakan');
});

test('inventory notification is created for users with inventory permission', function () {
    $permission = Permission::firstOrCreate(['name' => 'inventori.persediaan.kelola', 'guard_name' => 'web']);
    $role = Role::firstOrCreate(['name' => 'Inventori Test', 'guard_name' => 'web']);
    $role->givePermissionTo($permission);

    $user = User::factory()->create();
    $user->assignRole($role);

    NotificationService::stockLow('Tepung Terigu', '2', 'kg');

    expect(Notification::count())->toBe(1);
    expect(Notification::first()->type)->toBe('inventori');
    expect(Notification::first()->body)->toContain('hampir habis');
    expect(Notification::first()->body)->toContain('Tepung Terigu');
});

test('notification is created for multiple users with same permission', function () {
    $permission = Permission::firstOrCreate(['name' => 'kasir.pesanan.kelola', 'guard_name' => 'web']);
    $role = Role::firstOrCreate(['name' => 'Kasir Test', 'guard_name' => 'web']);
    $role->givePermissionTo($permission);

    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $user1->assignRole($role);
    $user2->assignRole($role);

    NotificationService::orderQueued('OR-250512-0004');

    expect(Notification::count())->toBe(2);
    expect(Notification::where('user_id', $user1->id)->exists())->toBeTrue();
    expect(Notification::where('user_id', $user2->id)->exists())->toBeTrue();
});

test('no notification created when no users have the permission', function () {
    NotificationService::orderQueued('OR-250512-0005');

    expect(Notification::count())->toBe(0);
});
