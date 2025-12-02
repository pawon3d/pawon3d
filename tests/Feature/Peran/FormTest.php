<?php

use App\Livewire\Peran\Form;
use App\Models\SpatieRole;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    // Seed permissions
    $permissions = PermissionSeeder::getAllPermissionNames();
    foreach ($permissions as $permissionName) {
        Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
    }

    // Create user with required permission
    $this->user = User::factory()->create();
    $this->user->givePermissionTo('manajemen.peran.kelola');
});

test('tambah peran page can be rendered', function () {
    $response = $this->actingAs($this->user)->get(route('role.tambah'));

    $response->assertStatus(200);
    $response->assertSeeLivewire(Form::class);
});

test('tambah peran page shows correct title', function () {
    Livewire::actingAs($this->user)
        ->test(Form::class)
        ->assertSee('Tambah Peran');
});

test('rincian peran page can be rendered', function () {
    $role = SpatieRole::create(['name' => 'Test Role']);

    $response = $this->actingAs($this->user)->get(route('role.edit', $role->id));

    $response->assertStatus(200);
    $response->assertSeeLivewire(Form::class);
});

test('rincian peran page shows correct title', function () {
    $role = SpatieRole::create(['name' => 'Test Role']);

    Livewire::actingAs($this->user)
        ->test(Form::class, ['id' => $role->id])
        ->assertSee('Rincian Peran');
});

test('can create new role', function () {
    Livewire::actingAs($this->user)
        ->test(Form::class)
        ->set('roleName', 'Koki Baru')
        ->set('selectedPermissions', ['kasir.pesanan.kelola', 'kasir.laporan.kelola'])
        ->call('save')
        ->assertRedirect(route('role'));

    $this->assertDatabaseHas('roles', ['name' => 'Koki Baru']);

    $role = SpatieRole::where('name', 'Koki Baru')->first();
    expect($role->hasPermissionTo('kasir.pesanan.kelola'))->toBeTrue();
    expect($role->hasPermissionTo('kasir.laporan.kelola'))->toBeTrue();
});

test('cannot create role with duplicate name', function () {
    SpatieRole::create(['name' => 'Existing Role']);

    Livewire::actingAs($this->user)
        ->test(Form::class)
        ->set('roleName', 'Existing Role')
        ->call('save')
        ->assertHasErrors(['roleName']);
});

test('can update existing role', function () {
    $role = SpatieRole::create(['name' => 'Old Name']);
    $role->syncPermissions(['kasir.pesanan.kelola']);

    Livewire::actingAs($this->user)
        ->test(Form::class, ['id' => $role->id])
        ->set('roleName', 'New Name')
        ->set('selectedPermissions', ['produksi.mulai', 'produksi.laporan.kelola'])
        ->call('save')
        ->assertRedirect(route('role'));

    $role->refresh();
    expect($role->name)->toBe('New Name');
    expect($role->hasPermissionTo('produksi.mulai'))->toBeTrue();
    expect($role->hasPermissionTo('kasir.pesanan.kelola'))->toBeFalse();
});

test('can delete role without users', function () {
    $role = SpatieRole::create(['name' => 'Role To Delete']);

    Livewire::actingAs($this->user)
        ->test(Form::class, ['id' => $role->id])
        ->call('delete')
        ->assertRedirect(route('role'));

    $this->assertDatabaseMissing('roles', ['name' => 'Role To Delete']);
});

test('cannot delete role with users', function () {
    $role = SpatieRole::create(['name' => 'Role With Users']);
    $user = User::factory()->create();
    $user->assignRole($role);

    Livewire::actingAs($this->user)
        ->test(Form::class, ['id' => $role->id])
        ->call('delete');

    // Role should still exist because it has users
    $this->assertDatabaseHas('roles', ['name' => 'Role With Users']);
});

test('toggle category enables all permissions in category', function () {
    Livewire::actingAs($this->user)
        ->test(Form::class)
        ->set('categoryToggles.kasir', true)
        ->call('toggleCategory', 'kasir')
        ->assertSet('selectedPermissions', ['kasir.pesanan.kelola', 'kasir.laporan.kelola']);
});

test('toggle category disables all permissions in category', function () {
    Livewire::actingAs($this->user)
        ->test(Form::class)
        ->set('selectedPermissions', ['kasir.pesanan.kelola', 'kasir.laporan.kelola'])
        ->set('categoryToggles.kasir', false)
        ->call('toggleCategory', 'kasir')
        ->assertSet('selectedPermissions', []);
});

test('can toggle individual permission', function () {
    Livewire::actingAs($this->user)
        ->test(Form::class)
        ->call('togglePermission', 'kasir.pesanan.kelola')
        ->assertSet('selectedPermissions', ['kasir.pesanan.kelola'])
        ->call('togglePermission', 'kasir.pesanan.kelola')
        ->assertSet('selectedPermissions', []);
});

test('isEditMode returns false for new role', function () {
    $component = Livewire::actingAs($this->user)->test(Form::class);

    expect($component->instance()->isEditMode())->toBeFalse();
});

test('isEditMode returns true for existing role', function () {
    $role = SpatieRole::create(['name' => 'Test Role']);

    $component = Livewire::actingAs($this->user)->test(Form::class, ['id' => $role->id]);

    expect($component->instance()->isEditMode())->toBeTrue();
});

test('can create role with max_users limit', function () {
    Livewire::actingAs($this->user)
        ->test(Form::class)
        ->set('roleName', 'Limited Role')
        ->set('maxUsers', 5)
        ->call('save')
        ->assertRedirect(route('role'));

    $role = SpatieRole::where('name', 'Limited Role')->first();
    expect($role->max_users)->toBe(5);
});

test('role name is required', function () {
    Livewire::actingAs($this->user)
        ->test(Form::class)
        ->set('roleName', '')
        ->call('save')
        ->assertHasErrors(['roleName' => 'required']);
});
