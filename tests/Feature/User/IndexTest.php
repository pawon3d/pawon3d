<?php

declare(strict_types=1);

use App\Livewire\User\Index;
use App\Models\SpatieRole;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'manajemen.pekerja.kelola']);

    $adminRole = SpatieRole::firstOrCreate(['name' => 'Admin']);
    $adminRole->givePermissionTo('manajemen.pekerja.kelola');

    $this->admin = User::factory()->create([
        'is_active' => true,
        'activated_at' => now(),
    ]);
    $this->admin->assignRole($adminRole);

    $this->actingAs($this->admin);
});

// TC-085: Admin mengakses halaman pekerja
test('admin can access users index page', function () {
    $this->get(route('user'))
        ->assertOk()
        ->assertSeeLivewire(Index::class);
});

// TC-094: Admin mengubah status aktif pengguna lain
test('can toggle another user active status', function () {
    $user = User::factory()->create([
        'is_active' => true,
        'activated_at' => now(),
    ]);

    Livewire::actingAs($this->admin)
        ->test(Index::class)
        ->call('toggleActive', (string) $user->id);

    $user->refresh();
    expect($user->is_active)->toBeFalse();
});

// TC-095: Admin mencoba menonaktifkan akun sendiri
test('cannot toggle own active status', function () {
    Livewire::actingAs($this->admin)
        ->test(Index::class)
        ->call('toggleActive', (string) $this->admin->id);

    // Admin's own is_active should remain unchanged
    $this->admin->refresh();
    expect($this->admin->is_active)->toBeTrue();
});

// TC-096: Admin mengirim ulang undangan pada akun yang sudah aktif
test('cannot resend invitation to already activated user', function () {
    Notification::fake();

    $activatedUser = User::factory()->create([
        'is_active' => true,
        'activated_at' => now(),
    ]);

    Livewire::actingAs($this->admin)
        ->test(Index::class)
        ->call('resendInvitation', (string) $activatedUser->id);

    // No invitation notification should be sent to an already-activated user
    Notification::assertNothingSentTo($activatedUser);
});
