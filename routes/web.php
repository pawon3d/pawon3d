<?php

use App\Http\Controllers\PdfController;
use App\Livewire\User\Index;
use App\Livewire\Dashboard;
use App\Livewire\Review\ReviewForm;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    $categories = \App\Models\Category::with('products')->get();
    $reviews = \App\Models\Review::with('product')->where('visible', true)->get();
    $products = \App\Models\Product::with('category', 'reviews')
        ->withCount('reviews')
        ->where('reviews_count', '>', 0)
        ->get()
        ->sortByDesc(function ($product) {
            return $product->reviews->avg('rating');
        })
        ->take(4);
    $productReviews = \App\Models\Product::with('reviews')
        ->withCount('reviews')
        ->where('reviews_count', '>', 0)
        ->get()
        ->sortByDesc(fn($p) => $p->reviews->avg('rating'))->take(4);

    return view('landing.index', compact('categories', 'reviews', 'products', 'productReviews'));
})->name('home');

Route::get('dashboard', Dashboard::class)
    ->middleware(['auth'])
    ->name('dashboard');

Route::get('/ulasan/{transaction_id}', ReviewForm::class)
    ->name('ulasan');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Route::get('/pengguna', Index::class)->name('pengguna');

    Route::post('/read-notification/{id}', function ($id) {
        $notification = \App\Models\Notification::find($id);
        $notification->update(['is_read' => true]);
        return response()->json(['message' => 'Notification has been read']);
    })->name('read-notification');

    Route::get('/kategori', App\Livewire\Category\Index::class)->name('kategori');
    Route::get('/produk', App\Livewire\Product\Index::class)->name('produk');
    Route::get('/bahan-baku', App\Livewire\Material\Index::class)->name('bahan-baku');
    Route::get('/bahan-olahan', App\Livewire\ProcessedMaterial\Index::class)->name('bahan-olahan');
    Route::get('/pos', App\Livewire\Transaction\Pos::class)->name('pos');
    Route::get('/transaksi', App\Livewire\Transaction\Index::class)->name('transaksi');
    Route::get('/transaksi/{id}/edit', App\Livewire\Transaction\Edit::class)->name('transaksi.edit');
    Route::get('/produksi', App\Livewire\Production\Index::class)->name('produksi');
    Route::get('/transaksi/{id}/print', function () {
        return view('pdf.pdf', [
            'transaction' => \App\Models\Transaction::find(request()->id)
        ]);
    })->name('transaksi.cetak');
    Route::get('/ulasan', App\Livewire\Review\Index::class)->name('review');
    Route::get('/hadiah', App\Livewire\Prize\Index::class)->name('hadiah');
    Route::get('/penukaran', App\Livewire\Prize\Exchange::class)->name('penukaran');
    Volt::route('/hadiah/didapat', 'prize.get')->name('hadiah.didapat');
    Volt::route('/hadiah/ditukar', 'prize.redeem')->name('hadiah.ditukar');
    Route::get('/pengaturan', App\Livewire\StoreSetting\Index::class)->name('pengaturan');


    Route::get('transaksi/laporan', [PDFController::class, 'printReport'])->name('transaksi.laporan');
});

require __DIR__ . '/auth.php';