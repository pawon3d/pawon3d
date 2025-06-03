<?php

use App\Http\Controllers\PdfController;
use App\Livewire\User\Index;
use App\Livewire\Dashboard;
use App\Livewire\Review\ReviewForm;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    $categories = \App\Models\Category::all();
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

Route::get('/tes', function () {
    return view('tes');
});

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
    Route::get('/kategori/tambah', App\Livewire\Category\Tambah::class)->name('kategori.tambah');
    Route::get('/kategori/{id}/rincian', App\Livewire\Category\Rincian::class)->name('kategori.edit');
    Route::get('/kategori/cetak', [PdfController::class, 'generateCategoryPDF'])
        ->name('kategori.pdf');
    Route::get('/kategori-persediaan', App\Livewire\IngredientCategory\Index::class)->name('kategori-persediaan');
    Route::get('/kategori-persediaan/tambah', App\Livewire\IngredientCategory\Tambah::class)->name('kategori-persediaan.tambah');
    Route::get('/kategori-persediaan/{id}/rincian', App\Livewire\IngredientCategory\Rincian::class)->name('kategori-persediaan.edit');
    Route::get('/kategori-persediaan/cetak', [PdfController::class, 'generateIngredientCategoryPDF'])
        ->name('kategori-persediaan.pdf');
    Route::get('/produk', App\Livewire\Product\Index::class)->name('produk');
    Route::get('/produk/tambah/{method}', App\Livewire\Product\Tambah::class)->name('produk.tambah');
    Route::get('/produk/salin/{method}', App\Livewire\Product\Salin::class)->name('produk.salin');
    Route::get('/produk/{id}/rincian', App\Livewire\Product\Rincian::class)->name('produk.edit');
    Route::get('/produk/cetak', [PdfController::class, 'generateProductPDF'])
        ->name('produk.pdf');
    Route::get('/supplier', App\Livewire\Supplier\Index::class)->name('supplier');
    Route::get('/supplier/tambah', App\Livewire\Supplier\Tambah::class)->name('supplier.tambah');
    Route::get('/supplier/{id}/rincian', App\Livewire\Supplier\Rincian::class)->name('supplier.edit');
    Route::get('/supplier/cetak', [PdfController::class, 'generateSupplierPDF'])
        ->name('supplier.pdf');
    Route::get('/bahan-baku', App\Livewire\Material\Index::class)->name('bahan-baku');
    Route::get('/bahan-baku/tambah', App\Livewire\Material\Tambah::class)->name('bahan-baku.tambah');
    Route::get('/bahan-baku/{id}/rincian', App\Livewire\Material\Rincian::class)->name('bahan-baku.edit');
    Route::get('/bahan-baku/cetak', [PdfController::class, 'generateMaterialPDF'])
        ->name('bahan-baku.pdf');
    Route::get('/belanja', App\Livewire\Expense\Index::class)->name('belanja');
    Route::get('/belanja/tambah', App\Livewire\Expense\Tambah::class)->name('belanja.tambah');
    Route::get('/belanja/{id}/edit', App\Livewire\Expense\Edit::class)->name('belanja.edit');
    Route::get('/belanja/{id}/rincian', App\Livewire\Expense\Rincian::class)->name('belanja.rincian');
    Route::get('/belanja/{id}/dapatkan-belanja', App\Livewire\Expense\Mulai::class)->name('belanja.dapatkan-belanja');
    Route::get('/belanja/riwayat', App\Livewire\Expense\Riwayat::class)->name('belanja.riwayat');
    Route::get('/belanja/{status}/cetak', [PdfController::class, 'generateExpensePDF'])
        ->name('belanja.pdf');
    Route::get('/belanja/cetak/{id}', [PdfController::class, 'generateExpenseDetailPDF'])
        ->name('rincian-belanja.pdf');
    Route::get('/padan', App\Livewire\Padan\Index::class)->name('padan');
    Route::get('/padan/tambah', App\Livewire\Padan\Tambah::class)->name('padan.tambah');
    Route::get('/padan/{id}/edit', App\Livewire\Padan\Edit::class)->name('padan.edit');
    Route::get('/padan/{id}/rincian', App\Livewire\Padan\Rincian::class)->name('padan.rincian');
    Route::get('/padan/{id}/mulai-aksi', App\Livewire\Padan\Mulai::class)->name('padan.mulai');
    Route::get('/padan/riwayat', App\Livewire\Padan\Riwayat::class)->name('padan.riwayat');
    Route::get('/padan/{status}/cetak', [PdfController::class, 'generatePadanPDF'])
        ->name('padan.pdf');
    Route::get('/padan/cetak/{id}', [PdfController::class, 'generatePadanDetailPDF'])
        ->name('rincian-padan.pdf');
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
