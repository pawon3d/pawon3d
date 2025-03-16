<?php

use App\Http\Controllers\PdfController;
use App\Livewire\User\Index;
use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    $categories = \App\Models\Category::with('products')->get();
    return view('welcome', compact('categories'));
})->name('home');

Route::get('dashboard', Dashboard::class)
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Route::get('/pengguna', Index::class)->name('pengguna');

    Route::get('/kategori', App\Livewire\Category\Index::class)->name('kategori');
    Route::get('/produk', App\Livewire\Product\Index::class)->name('produk');
    Route::get('/bahan-baku', App\Livewire\Material\Index::class)->name('bahan-baku');
    Route::get('/bahan-olahan', App\Livewire\ProcessedMaterial\Index::class)->name('bahan-olahan');
    Route::get('/pos', App\Livewire\Transaction\Pos::class)->name('pos');
    Route::get('/transaksi', App\Livewire\Transaction\Index::class)->name('transaksi');
    Route::get('/transaksi/{id}/edit', App\Livewire\Transaction\Edit::class)->name('transaksi.edit');
    Route::get('/produksi', App\Livewire\Production\Index::class)->name('produksi');
    Route::get('/transaksi/{id}/print', [PdfController::class, 'print'])->name('transaksi.cetak');
});

require __DIR__ . '/auth.php';
