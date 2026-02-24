<?php

use App\Models\StoreProfile;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Livewire\Volt\Volt as LivewireVolt;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $profile = StoreProfile::firstOrCreate(['id' => 1], [
        'name' => 'Test Store',
        'address' => 'Test Address',
        'phone' => '08123456789',
    ]);
    View::share('storeProfile', $profile);
});

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create([
        'is_active' => true,
        'activated_at' => now(),
    ]);

    $response = LivewireVolt::test('auth.login')
        ->set('email', $user->email)
        ->set('password', 'password')
        ->call('login');

    $response
        ->assertHasNoErrors()
        ->assertRedirect(route('ringkasan-umum', absolute: false));

    $this->assertAuthenticated();
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/login');
});

// TC-002: Login dengan field email dan password kosong
test('login validation requires email and password', function () {
    LivewireVolt::test('auth.login')
        ->set('email', '')
        ->set('password', '')
        ->call('login')
        ->assertHasErrors(['email', 'password']);
});

// TC-006: Login melebihi batas percobaan (rate limit)
test('users cannot login after exceeding rate limit', function () {
    $user = User::factory()->create();

    // Simulate exceeding the rate limit (5 attempts)
    $throttleKey = Str::transliterate(Str::lower($user->email).'|127.0.0.1');
    for ($i = 0; $i < 5; $i++) {
        RateLimiter::hit($throttleKey);
    }

    LivewireVolt::test('auth.login')
        ->set('email', $user->email)
        ->set('password', 'wrong-password')
        ->call('login')
        ->assertHasErrors(['email']);
});
