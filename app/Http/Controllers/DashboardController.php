<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function ringkasan()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Check permissions in order of priority
        $permissionGroups = [
            'laporan-kasir' => ['kasir.pesanan.kelola', 'kasir.laporan.kelola'],
            'laporan-produksi' => ['produksi.rencana.kelola', 'produksi.mulai', 'produksi.laporan.kelola'],
            'laporan-inventori' => [
                'inventori.produk.kelola',
                'inventori.persediaan.kelola',
                'inventori.belanja.rencana.kelola',
                'inventori.toko.kelola',
                'inventori.belanja.mulai',
                'inventori.hitung.kelola',
                'inventori.alur.lihat',
                'inventori.laporan.kelola',
            ],
        ];

        foreach ($permissionGroups as $routeName => $permissions) {
            if ($user->hasAnyPermission($permissions)) {
                return redirect()->route($routeName);
            }
        }

        return redirect()->route('home');
    }
}
