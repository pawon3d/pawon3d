<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PdfController;
use App\Livewire\User\Index;
use App\Livewire\Dashboard;
use App\Livewire\Review\ReviewForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', App\Livewire\Landing\Index::class)->name('home');
Route::get('/landing-cara-pesan', App\Livewire\Landing\Pesan::class)->name('landing-cara-pesan');
Route::get('/landing-produk', App\Livewire\Landing\Produk::class)->name('landing-produk');


Route::get('/tes', function () {
    return view('tes');
});

Route::get('dashboard', Dashboard::class)
    ->middleware(['auth'])
    ->name('dashboard');
Route::get('/ringkasan-kasir', App\Livewire\Dashboard\RingkasanKasir::class)
    ->middleware(['auth'])
    ->name('ringkasan-kasir');
Route::get('/ringkasan-produksi', App\Livewire\Dashboard\RingkasanProduksi::class)
    ->middleware(['auth'])
    ->name('ringkasan-produksi');
Route::get('/ringkasan-inventori', App\Livewire\Dashboard\RingkasanInventori::class)
    ->middleware(['auth'])
    ->name('ringkasan-inventori');
Route::get('/ringkasan-umum', [DashboardController::class, 'ringkasan'])
    ->middleware('auth')
    ->name('ringkasan-umum');

Route::get('/ulasan/{transaction_id}', ReviewForm::class)
    ->name('ulasan');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Route::group(['middleware' => ['permission:Manajemen Sistem']], function () {
        Route::get('/pekerja', Index::class)->name('user');
        Route::get('/pekerja/tambah', App\Livewire\User\Tambah::class)->name('user.tambah');
        Route::get('/pekerja/{id}/rincian', App\Livewire\User\Rincian::class)->name('user.edit');
        Route::get('/pekerja/cetak', [PdfController::class, 'generateUserPDF'])
            ->name('user.pdf');

        Route::get('/peran', App\Livewire\Peran\Index::class)->name('role');
        Route::get('/peran/tambah', App\Livewire\Peran\Tambah::class)->name('role.tambah');
        Route::get('/peran/{id}/rincian', App\Livewire\Peran\Rincian::class)->name('role.edit');
        Route::get('/peran/cetak', [PdfController::class, 'generateRolePDF'])
            ->name('role.pdf');
        Route::get('/pelanggan', App\Livewire\Customer\Index::class)->name('customer');
    });

    Route::post('/read-notification/{id}', function ($id) {
        $notification = \App\Models\Notification::find($id);
        $notification->update(['is_read' => true]);
        return response()->json(['message' => 'Notification has been read']);
    })->name('read-notification');


    Route::group(['middleware' => ['permission:Inventori']], function () {
        Route::get('/kategori', App\Livewire\Category\Index::class)->name('kategori');
        Route::get('/kategori/tambah', App\Livewire\Category\Tambah::class)->name('kategori.tambah');
        Route::get('/kategori/{id}/rincian', App\Livewire\Category\Rincian::class)->name('kategori.edit');
        Route::get('/kategori/cetak', [PdfController::class, 'generateCategoryPDF'])
            ->name('kategori.pdf');
        Route::get('/satuan-ukur', App\Livewire\Unit\Index::class)->name('satuan-ukur');

        Route::get('/satuan-ukur/cetak', [PdfController::class, 'generateUnitPDF'])
            ->name('satuan-ukur.pdf');
        Route::get('/kategori-persediaan', App\Livewire\IngredientCategory\Index::class)->name('kategori-persediaan');
        Route::get('/kategori-persediaan/tambah', App\Livewire\IngredientCategory\Tambah::class)->name('kategori-persediaan.tambah');
        Route::get('/kategori-persediaan/{id}/rincian', App\Livewire\IngredientCategory\Rincian::class)->name('kategori-persediaan.edit');
        Route::get('/kategori-persediaan/cetak', [PdfController::class, 'generateIngredientCategoryPDF'])
            ->name('kategori-persediaan.pdf');
        Route::get('/produk', App\Livewire\Product\Index::class)->name('produk');
        Route::get('/produk/tambah/', App\Livewire\Product\Tambah::class)->name('produk.tambah');
        // Route::get('/produk/salin/{method}', App\Livewire\Product\Salin::class)->name('produk.salin');
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
        Route::get('/hitung', App\Livewire\Hitung\Index::class)->name('hitung');
        Route::get('/hitung/tambah', App\Livewire\Hitung\Tambah::class)->name('hitung.tambah');
        Route::get('/hitung/{id}/edit', App\Livewire\Hitung\Edit::class)->name('hitung.edit');
        Route::get('/hitung/{id}/rincian', App\Livewire\Hitung\Rincian::class)->name('hitung.rincian');
        Route::get('/hitung/{id}/mulai-aksi', App\Livewire\Hitung\Mulai::class)->name('hitung.mulai');
        Route::get('/hitung/riwayat', App\Livewire\Hitung\Riwayat::class)->name('hitung.riwayat');
        Route::get('/hitung/{status}/cetak', [PdfController::class, 'generateHitungPDF'])
            ->name('hitung.pdf');
        Route::get('/hitung/cetak/{id}', [PdfController::class, 'generateHitungDetailPDF'])
            ->name('rincian-hitung.pdf');
    });

    Route::group(['middleware' => ['permission:Kasir']], function () {
        Route::get('/pos', App\Livewire\Transaction\Pos::class)->name('pos');
        Route::get('/transaksi', App\Livewire\Transaction\Index::class)->name('transaksi');
        Route::get('/transaksi/{id}/edit', App\Livewire\Transaction\Edit::class)->name('transaksi.edit');
        Route::get('/transaksi/{id}/rincian-pesanan', App\Livewire\Transaction\RincianPesanan::class)->name('transaksi.rincian-pesanan');
        Route::get('/transaksi/{id}/rincian-produk', App\Livewire\Transaction\RincianProduk::class)->name('transaksi.rincian-produk');
        Route::get('/transaksi/{method}/pesanan', App\Livewire\Transaction\Pesanan::class)->name('transaksi.pesanan');
        Route::get('/transaksi/{method}/riwayat', App\Livewire\Transaction\Riwayat::class)->name('transaksi.riwayat');
        Route::get('/transaksi/{id}/buat-pesanan', App\Livewire\Transaction\BuatPesanan::class)->name('transaksi.buat-pesanan');
        Route::get('/transaksi/cetak', [PdfController::class, 'generateTransactionPDF'])
            ->name('transaksi.pdf');
        Route::get('/transaksi/cetak/{id}', [PdfController::class, 'generateTransactionDetailPDF'])
            ->name('rincian-transaksi.pdf');
        Route::get('/transaksi/{id}/print', function () {
            return view('pdf.pdf', [
                'transaction' => \App\Models\Transaction::find(request()->id)
            ]);
        })->name('transaksi.cetak');
        Route::get('/cetak-struk/{id}', App\Livewire\Receipt::class)->name('cetak-struk');
    });

    Route::group(['middleware' => ['permission:Produksi']], function () {
        Route::get('/produksi', App\Livewire\Production\Index::class)->name('produksi');
        Route::get('/produksi/tambah/{method}', App\Livewire\Production\Tambah::class)->name('produksi.tambah');
        Route::get('/produksi/{id}/edit', App\Livewire\Production\Edit::class)->name('produksi.edit');
        Route::get('/produksi/{id}/rincian', App\Livewire\Production\Rincian::class)->name('produksi.rincian');
        Route::get('/produksi/{id}/mulai-produksi', App\Livewire\Production\Mulai::class)->name('produksi.mulai');
        Route::get('/produksi/riwayat/{method}', App\Livewire\Production\Riwayat::class)->name('produksi.riwayat');
        Route::get('/produksi/pesanan/{method}', App\Livewire\Production\Pesanan::class)->name('produksi.pesanan');
        Route::get('/produksi/tambah-produksi-pesanan/{id}', App\Livewire\Production\TambahProduksiPesanan::class)
            ->name('produksi.tambah-produksi-pesanan');
        Route::get('/produksi/edit-produksi-pesanan/{id}', App\Livewire\Production\EditProduksiPesanan::class)
            ->name('produksi.edit-produksi-pesanan');
        Route::get('/produksi/{id}/rincian-pesanan', App\Livewire\Production\RincianPesanan::class)->name('produksi.rincian-pesanan');
        Route::get('/produksi/{status}/cetak', [PdfController::class, 'generateProductionPDF'])
            ->name('produksi.pdf');
        Route::get('/produksi/cetak/{id}', [PdfController::class, 'generateProductionDetailPDF'])
            ->name('rincian-produksi.pdf');
    });

    Route::get('/ulasan', App\Livewire\Review\Index::class)->name('review');
    Route::get('/hadiah', App\Livewire\Prize\Index::class)->name('hadiah');
    Route::get('/penukaran', App\Livewire\Prize\Exchange::class)->name('penukaran');
    Volt::route('/hadiah/didapat', 'prize.get')->name('hadiah.didapat');
    Volt::route('/hadiah/ditukar', 'prize.redeem')->name('hadiah.ditukar');

    Route::get('/pengaturan', App\Livewire\Setting\Index::class)->name('pengaturan');
    Route::get('/profil-saya/{id}', App\Livewire\Setting\MyProfile::class)->name('profil-saya');


    Route::group(['middleware' => ['permission:Manajemen Sistem']], function () {
        Route::get('/profil-usaha', App\Livewire\Setting\StoreProfile::class)->name('profil-usaha');
        Route::get('/metode-pembayaran', App\Livewire\Setting\PaymentMethod::class)->name('metode-pembayaran');
    });

    Route::get('/panduan-pengguna', App\Livewire\Setting\UserManual::class)->name('panduan-pengguna');



    Route::get('transaksi/laporan', [PDFController::class, 'printReport'])->name('transaksi.laporan');
});

require __DIR__ . '/auth.php';
