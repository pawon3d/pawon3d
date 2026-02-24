<?php

declare(strict_types=1);

use App\Livewire\Setting\MyProfile;
use App\Models\SpatieRole;
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

    $adminRole = SpatieRole::firstOrCreate(['name' => 'Admin']);

    $this->admin = User::factory()->create(['is_active' => true, 'activated_at' => now()]);
    $this->admin->assignRole($adminRole);

    $this->actingAs($this->admin);
});

// TC-119: Pengguna dapat mengakses halaman profil
test('user can access profile page', function () {
    $this->get(route('profil-saya', $this->admin->id))
        ->assertOk()
        ->assertSeeLivewire(MyProfile::class);
});

// TC-120: Pengguna mengubah data profil dengan data valid
test('can update profile with valid data', function () {
    Livewire::test(MyProfile::class, ['id' => $this->admin->id])
        ->set('name', 'Admin Pawon3D')
        ->set('phone', '081234567899')
        ->call('updateUser')
        ->assertRedirect(route('pengaturan'));

    $this->admin->refresh();
    expect($this->admin->name)->toBe('Admin Pawon3D');
    expect($this->admin->phone)->toBe('081234567899');
});

// TC-121: Pengguna mengubah profil dengan email tidak valid
test('validates email format on profile update', function () {
    Livewire::test(MyProfile::class, ['id' => $this->admin->id])
        ->set('name', 'Admin')
        ->set('email', 'bukan-email-valid')
        ->call('updateUser')
        ->assertHasErrors(['email']);
});

// TC-122: Pengguna mengubah password profil kurang dari 8 karakter
test('validates password minimum length on profile update', function () {
    Livewire::test(MyProfile::class, ['id' => $this->admin->id])
        ->set('name', 'Admin')
        ->set('email', $this->admin->email)
        ->set('password', 'Pass1')
        ->call('updateUser')
        ->assertHasErrors(['password']);
});
