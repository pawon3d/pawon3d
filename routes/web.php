<?php

use App\Livewire\User\Index;
use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
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
});

require __DIR__ . '/auth.php';