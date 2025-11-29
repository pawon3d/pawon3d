<?php

use App\Livewire\Hitung\Form;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $permission = Permission::firstOrCreate(['name' => 'Inventori']);
    $user = User::factory()->create();
    $user->givePermissionTo($permission);
    $this->actingAs($user);
});

it('renders the tambah page successfully', function () {
    $this->get(route('hitung.tambah'))
        ->assertStatus(200)
        ->assertSeeLivewire(Form::class);
});

it('has initial hitung details array with one empty item', function () {
    Livewire::test(Form::class)
        ->assertSet('action', '')
        ->assertSet('hitung_date', '')
        ->assertSet('note', '')
        ->assertSet('grand_total', 0)
        ->assertCount('hitung_details', 1);
});

it('can add detail row', function () {
    Livewire::test(Form::class)
        ->assertCount('hitung_details', 1)
        ->call('addDetail')
        ->assertCount('hitung_details', 2)
        ->call('addDetail')
        ->assertCount('hitung_details', 3);
});

it('can remove detail row but keeps at least one', function () {
    Livewire::test(Form::class)
        ->call('addDetail')
        ->assertCount('hitung_details', 2)
        ->call('removeDetail', 0)
        ->assertCount('hitung_details', 1)
        ->call('removeDetail', 0)
        ->assertCount('hitung_details', 1); // tetap 1, tidak bisa kurang
});

it('validates action is required on save', function () {
    Livewire::test(Form::class)
        ->set('action', '')
        ->call('save')
        ->assertHasErrors(['action' => 'required']);
});

it('is in create mode when no id is provided', function () {
    Livewire::test(Form::class)
        ->assertSet('hitung_id', null);
});
