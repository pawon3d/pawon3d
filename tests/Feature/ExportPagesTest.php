<?php

declare(strict_types=1);

use App\Models\StoreProfile;
use App\Models\User;
use Illuminate\Support\Facades\View;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $profile = StoreProfile::firstOrCreate(
        ['id' => 1],
        ['name' => 'Test Store', 'address' => 'Test Address', 'phone' => '08123456789']
    );
    View::share('storeProfile', $profile);
});

it('renders export pages and includes pdf and excel buttons', function () {
    $user = User::factory()->create();
    // Ensure permissions exist and user has access
    \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'kasir.laporan.kelola']);
    \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'produksi.laporan.kelola']);
    \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'inventori.laporan.kelola']);
    $user->givePermissionTo('kasir.laporan.kelola', 'produksi.laporan.kelola', 'inventori.laporan.kelola');
    actingAs($user);

    // Export Kasir
    $response = $this->get(route('laporan-kasir.export'));
    $response->assertStatus(200);
    $response->assertSee('Unduh Laporan Kasir');
    $response->assertSee('Unduh PDF');
    $response->assertSee('Unduh Excel');
    $response->assertSee('Metode Penjualan');

    // Export Produksi
    $response = $this->get(route('laporan-produksi.export'));
    $response->assertStatus(200);
    $response->assertSee('Unduh Laporan Produksi');
    $response->assertSee('Unduh PDF');
    $response->assertSee('Unduh Excel');

    // Export Inventori
    $response = $this->get(route('laporan-inventori.export'));
    $response->assertStatus(200);
    $response->assertSee('Unduh Laporan Inventori');
    $response->assertSee('Unduh PDF');
    $response->assertSee('Unduh Excel');
});
