<?php

use App\Models\StoreProfile;
use App\Models\User;
use App\Notifications\UserInvitationNotification;
use Illuminate\Support\Facades\Notification;
use Livewire\Volt\Volt;

beforeEach(function () {
    // Buat role jika belum ada
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Kasir']);

    // Buat store profile untuk layout
    StoreProfile::firstOrCreate(['id' => 1], [
        'name' => 'Test Store',
        'address' => 'Test Address',
        'phone' => '08123456789',
    ]);
});

test('user dapat dibuat tanpa password dan mengirim invitation', function () {
    Notification::fake();

    $owner = User::factory()->create([
        'is_active' => true,
        'activated_at' => now(),
    ]);
    $owner->assignRole('Kasir');

    $this->actingAs($owner);

    $response = Livewire\Livewire::test(\App\Livewire\User\Tambah::class)
        ->set('name', 'John Doe')
        ->set('email', 'john@example.com')
        ->set('phone', '08123456789')
        ->set('gender', 'Laki-laki')
        ->set('role', 'Kasir')
        ->call('createUser');

    $response->assertHasNoErrors();

    $user = User::where('email', 'john@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->is_active)->toBeFalse();
    expect($user->activated_at)->toBeNull();
    expect($user->invitation_token)->not->toBeNull();

    Notification::assertSentTo($user, UserInvitationNotification::class);
});

test('user dengan akun nonaktif tidak bisa login', function () {
    $user = User::factory()->create([
        'is_active' => false,
        'activated_at' => now(),
    ]);

    Volt::test('auth.login')
        ->set('email', $user->email)
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors(['email']);
});

test('user yang belum aktivasi tidak bisa login', function () {
    $user = User::factory()->create([
        'is_active' => false,
        'activated_at' => null,
        'invitation_token' => \Illuminate\Support\Str::random(64),
    ]);

    Volt::test('auth.login')
        ->set('email', $user->email)
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors(['email']);
});

test('halaman aktivasi menampilkan form dengan token valid', function () {
    $user = User::factory()->create([
        'is_active' => false,
        'activated_at' => null,
        'invitation_token' => 'valid-token-123',
        'invitation_sent_at' => now(),
    ]);

    Livewire\Livewire::test(\App\Livewire\ActivateAccount::class, ['token' => $user->invitation_token])
        ->assertSet('tokenValid', true)
        ->assertSet('alreadyActivated', false)
        ->assertSet('tokenExpired', false)
        ->assertSee($user->name);
});

test('halaman aktivasi menampilkan error untuk token invalid', function () {
    Livewire\Livewire::test(\App\Livewire\ActivateAccount::class, ['token' => 'invalid-token-xyz'])
        ->assertSet('tokenValid', false);
});

test('halaman aktivasi menampilkan error untuk token expired', function () {
    $user = User::factory()->create([
        'is_active' => false,
        'activated_at' => null,
        'invitation_token' => 'expired-token-123',
        'invitation_sent_at' => now()->subDays(8), // 8 hari lalu
    ]);

    Livewire\Livewire::test(\App\Livewire\ActivateAccount::class, ['token' => $user->invitation_token])
        ->assertSet('tokenExpired', true);
});

test('user dapat mengaktifkan akun dengan password baru', function () {
    $user = User::factory()->create([
        'is_active' => false,
        'activated_at' => null,
        'invitation_token' => 'activation-token-123',
        'invitation_sent_at' => now(),
    ]);

    Livewire\Livewire::test(\App\Livewire\ActivateAccount::class, ['token' => $user->invitation_token])
        ->set('password', 'Password123')
        ->set('password_confirmation', 'Password123')
        ->call('activate')
        ->assertRedirect(route('dashboard'));

    $user->refresh();
    expect($user->is_active)->toBeTrue();
    expect($user->activated_at)->not->toBeNull();
    expect($user->invitation_token)->toBeNull();
});

test('owner dapat toggle status aktif pekerja', function () {
    $owner = User::factory()->create([
        'is_active' => true,
        'activated_at' => now(),
    ]);

    $worker = User::factory()->create([
        'is_active' => true,
        'activated_at' => now(),
    ]);

    $this->actingAs($owner);

    Livewire\Livewire::test(\App\Livewire\User\Index::class)
        ->call('toggleActive', $worker->id);

    $worker->refresh();
    expect($worker->is_active)->toBeFalse();

    // Toggle kembali
    Livewire\Livewire::test(\App\Livewire\User\Index::class)
        ->call('toggleActive', $worker->id);

    $worker->refresh();
    expect($worker->is_active)->toBeTrue();
});

test('owner tidak bisa menonaktifkan diri sendiri', function () {
    $owner = User::factory()->create([
        'is_active' => true,
        'activated_at' => now(),
    ]);

    $this->actingAs($owner);

    // Panggil toggleActive dan pastikan akun tetap aktif
    Livewire\Livewire::test(\App\Livewire\User\Index::class)
        ->call('toggleActive', $owner->id);

    $owner->refresh();
    expect($owner->is_active)->toBeTrue(); // Tetap aktif karena ditolak
});

test('owner dapat mengirim ulang invitation', function () {
    Notification::fake();

    $owner = User::factory()->create([
        'is_active' => true,
        'activated_at' => now(),
    ]);

    $worker = User::factory()->create([
        'is_active' => false,
        'activated_at' => null,
        'invitation_token' => 'old-token',
        'invitation_sent_at' => now()->subDays(5),
    ]);

    $this->actingAs($owner);

    Livewire\Livewire::test(\App\Livewire\User\Index::class)
        ->call('resendInvitation', $worker->id);

    $worker->refresh();
    expect($worker->invitation_token)->not->toBe('old-token');

    Notification::assertSentTo($worker, UserInvitationNotification::class);
});
