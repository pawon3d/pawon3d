<?php

declare(strict_types=1);

use App\Livewire\Setting\StoreProfile;
use App\Models\SpatieRole;
use App\Models\StoreDocument;
use App\Models\StoreProfile as StoreProfileModel;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    // Create permission
    Permission::firstOrCreate(['name' => 'manajemen.profil_usaha.kelola']);

    // Create admin role with permission
    $adminRole = SpatieRole::firstOrCreate(['name' => 'Admin']);
    $adminRole->givePermissionTo('manajemen.profil_usaha.kelola');

    $this->admin = User::factory()->create();
    $this->admin->assignRole($adminRole);

    $this->actingAs($this->admin);
});

test('can render store profile page', function () {
    Livewire::test(StoreProfile::class)
        ->assertStatus(200)
        ->assertSee('Profil Usaha');
});

test('can update store profile basic information', function () {
    Livewire::test(StoreProfile::class)
        ->set('name', 'Toko Test')
        ->set('tagline', 'Tagline Test')
        ->set('type', 'UMK')
        ->set('product', 'Kue')
        ->set('description', 'Deskripsi Test')
        ->call('updateStore')
        ->assertHasNoErrors();

    $storeProfile = StoreProfileModel::first();
    expect($storeProfile->name)->toBe('Toko Test');
    expect($storeProfile->tagline)->toBe('Tagline Test');
    expect($storeProfile->type)->toBe('UMK');
    expect($storeProfile->product)->toBe('Kue');
    expect($storeProfile->description)->toBe('Deskripsi Test');
});

test('can update store profile address and contact', function () {
    Livewire::test(StoreProfile::class)
        ->set('location', 'https://maps.app.goo.gl/test')
        ->set('address', 'Jl. Test No. 123')
        ->set('contact', '08123456789')
        ->set('email', 'test@example.com')
        ->set('website', 'www.test.com')
        ->call('updateStore')
        ->assertHasNoErrors();

    $storeProfile = StoreProfileModel::first();
    expect($storeProfile->location)->toBe('https://maps.app.goo.gl/test');
    expect($storeProfile->address)->toBe('Jl. Test No. 123');
    expect($storeProfile->contact)->toBe('08123456789');
    expect($storeProfile->email)->toBe('test@example.com');
    expect($storeProfile->website)->toBe('www.test.com');
});

test('can update store profile social media', function () {
    Livewire::test(StoreProfile::class)
        ->set('social_instagram', '@tokoku')
        ->set('social_facebook', '@tokoku_fb')
        ->set('social_whatsapp', '08123456789')
        ->call('updateStore')
        ->assertHasNoErrors();

    $storeProfile = StoreProfileModel::first();
    expect($storeProfile->social_instagram)->toBe('@tokoku');
    expect($storeProfile->social_facebook)->toBe('@tokoku_fb');
    expect($storeProfile->social_whatsapp)->toBe('08123456789');
});

test('can add document', function () {
    Livewire::test(StoreProfile::class)
        ->call('addModal')
        ->assertSet('showModal', true)
        ->assertSet('edit', false)
        ->set('documentName', 'SIUP Test')
        ->set('documentNumber', '123/SIUP/2024')
        ->set('validFrom', '2024-01-01')
        ->set('validUntil', '2025-01-01')
        ->call('storeDocument')
        ->assertHasNoErrors();

    $document = StoreDocument::where('document_name', 'SIUP Test')->first();
    expect($document)->not->toBeNull();
    expect($document->document_number)->toBe('123/SIUP/2024');
});

test('can edit document', function () {
    $document = StoreDocument::create([
        'document_name' => 'Original Document',
        'document_number' => 'ORIG-001',
        'valid_from' => '2024-01-01',
        'valid_until' => '2025-01-01',
    ]);

    Livewire::test(StoreProfile::class)
        ->call('editModal', $document->id)
        ->assertSet('showModal', true)
        ->assertSet('edit', true)
        ->assertSet('documentName', 'Original Document')
        ->set('documentName', 'Updated Document')
        ->call('updateDocument')
        ->assertHasNoErrors();

    $document->refresh();
    expect($document->document_name)->toBe('Updated Document');
});

test('can delete document', function () {
    $document = StoreDocument::create([
        'document_name' => 'To Be Deleted',
        'document_number' => 'DEL-001',
    ]);

    Livewire::test(StoreProfile::class)
        ->call('editModal', $document->id)
        ->assertSet('documentId', $document->id)
        ->call('delete')
        ->assertHasNoErrors();

    expect(StoreDocument::find($document->id))->toBeNull();
});

test('validates document name is required', function () {
    Livewire::test(StoreProfile::class)
        ->call('addModal')
        ->set('documentName', '')
        ->call('storeDocument')
        ->assertHasErrors(['documentName']);
});

test('validates valid_until must be after valid_from', function () {
    Livewire::test(StoreProfile::class)
        ->call('addModal')
        ->set('documentName', 'Test Document')
        ->set('validFrom', '2025-01-01')
        ->set('validUntil', '2024-01-01')
        ->call('storeDocument')
        ->assertHasErrors(['validUntil']);
});

test('loads existing store profile data on mount', function () {
    StoreProfileModel::create([
        'name' => 'Existing Store',
        'tagline' => 'Existing Tagline',
        'type' => 'Existing Type',
        'product' => 'Existing Product',
        'description' => 'Existing Description',
        'social_instagram' => '@existing',
    ]);

    Livewire::test(StoreProfile::class)
        ->assertSet('name', 'Existing Store')
        ->assertSet('tagline', 'Existing Tagline')
        ->assertSet('type', 'Existing Type')
        ->assertSet('product', 'Existing Product')
        ->assertSet('description', 'Existing Description')
        ->assertSet('social_instagram', '@existing');
});

test('can sort documents by name', function () {
    StoreDocument::create(['document_name' => 'B Document']);
    StoreDocument::create(['document_name' => 'A Document']);

    $component = Livewire::test(StoreProfile::class);

    // Sort ascending
    $component->call('sortBy', 'document_name')
        ->assertSet('sortField', 'document_name')
        ->assertSet('sortDirection', 'asc');

    // Sort descending (toggle)
    $component->call('sortBy', 'document_name')
        ->assertSet('sortDirection', 'desc');
});
