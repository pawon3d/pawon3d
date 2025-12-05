<?php

use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('user tanpa role diarahkan ke halaman menunggu peran', function () {
    // Buat user tanpa role apapun
    $user = User::factory()->create([
        'is_active' => true,
    ]);

    // User login dan akses dashboard
    $response = $this->actingAs($user)->get(route('ringkasan-umum'));

    // Harus redirect ke halaman no-role
    $response->assertRedirect(route('no-role'));
});

test('halaman menunggu peran dapat diakses oleh user tanpa role', function () {
    $user = User::factory()->create([
        'is_active' => true,
    ]);

    $response = $this->actingAs($user)->get(route('no-role'));

    $response->assertStatus(200);
    $response->assertSee('Menunggu Penugasan Peran');
    $response->assertSee($user->name);
    $response->assertSee($user->email);
});

test('user dengan role tidak diarahkan ke halaman menunggu peran', function () {
    $user = User::factory()->create([
        'is_active' => true,
    ]);
    $user->givePermissionTo('kasir.laporan.kelola');

    $response = $this->actingAs($user)->get(route('ringkasan-umum'));

    // Harus redirect ke laporan-kasir, bukan no-role
    $response->assertRedirect(route('laporan-kasir'));
});

test('guest tidak bisa akses halaman menunggu peran', function () {
    $response = $this->get(route('no-role'));

    $response->assertRedirect(route('login'));
});
