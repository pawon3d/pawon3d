<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function ringkasan()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Ordered list: most-specific permission → target route
        $redirectMap = [
            // Laporan pages (require specific laporan permission)
            'kasir.laporan.kelola' => 'laporan-kasir',
            'produksi.laporan.kelola' => 'laporan-produksi',
            'inventori.laporan.kelola' => 'laporan-inventori',
            // Operational pages when laporan is not accessible
            'kasir.pesanan.kelola' => 'transaksi',
            'produksi.rencana.kelola' => 'produksi',
            'produksi.mulai' => 'produksi',
            'inventori.persediaan.kelola' => 'bahan-baku',
            'inventori.belanja.rencana.kelola' => 'belanja',
            'inventori.hitung.kelola' => 'hitung',
            'inventori.produk.kelola' => 'produk',
            'inventori.alur.lihat' => 'alur-persediaan',
            'manajemen.pekerja.kelola' => 'user',
            'manajemen.peran.kelola' => 'role',
            'manajemen.pelanggan.kelola' => 'customer',
        ];

        foreach ($redirectMap as $permission => $routeName) {
            if ($user->hasPermissionTo($permission)) {
                return redirect()->route($routeName);
            }
        }

        // User has no recognised permissions
        return redirect()->route('no-role');
    }
}
