<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Permission baru yang lebih granular berdasarkan desain Figma.
     */
    protected array $newPermissions = [
        // Kasir
        'kasir.pesanan.kelola' => 'Akses membuat, mengubah, menghapus, dan membatalkan pesanan reguler, pesanan kotak, dan siap saji',
        'kasir.laporan.kelola' => 'Akses mengelola laporan kasir',

        // Produksi (Koki)
        'produksi.rencana.kelola' => 'Akses membuat, mengubah, menghapus, dan membatalkan rencana produksi siap saji',
        'produksi.mulai' => 'Akses memulai produksi pesanan reguler, pesanan kotak, dan siap saji',
        'produksi.laporan.kelola' => 'Akses mengelola laporan produksi',

        // Inventori (Inventaris)
        'inventori.produk.kelola' => 'Akses membuat, mengubah, dan menghapus produk (pesanan reguler, pesanan kotak, dan siap saji)',
        'inventori.persediaan.kelola' => 'Akses membuat, mengubah, dan menghapus persediaan',
        'inventori.belanja.rencana.kelola' => 'Akses membuat, mengubah, menghapus, dan membatalkan rencana belanja persediaan',
        'inventori.toko.kelola' => 'Akses membuat, mengubah, menghapus toko persediaan',
        'inventori.belanja.mulai' => 'Akses memulai belanja persediaan',
        'inventori.hitung.kelola' => 'Akses membuat, mengubah, menghapus, dan memulai rencana hitung, catat rusak, dan catat hilang persediaan',
        'inventori.alur.lihat' => 'Akses melihat alur persediaan',
        'inventori.laporan.kelola' => 'Akses mengelola laporan inventori',

        // Manajemen
        'manajemen.pelanggan.kelola' => 'Akses membuat, mengubah, dan menghapus data pelanggan',
        'manajemen.pekerja.kelola' => 'Akses membuat, mengubah, dan menghapus data pekerja',
        'manajemen.peran.kelola' => 'Akses membuat, mengubah, dan menghapus data peran',
        'manajemen.profil_usaha.kelola' => 'Akses membuat, mengubah, dan menghapus profil usaha',
        'manajemen.pembayaran.kelola' => 'Akses membuat, mengubah, dan menghapus metode pembayaran',
    ];

    /**
     * Mapping dari permission lama ke permission baru.
     */
    protected array $oldToNewMapping = [
        'Kasir' => ['kasir.pesanan.kelola', 'kasir.laporan.kelola'],
        'Produksi' => ['produksi.rencana.kelola', 'produksi.mulai', 'produksi.laporan.kelola'],
        'Inventori' => [
            'inventori.produk.kelola',
            'inventori.persediaan.kelola',
            'inventori.belanja.rencana.kelola',
            'inventori.toko.kelola',
            'inventori.belanja.mulai',
            'inventori.hitung.kelola',
            'inventori.alur.lihat',
            'inventori.laporan.kelola',
        ],
        'Manajemen Sistem' => [
            'manajemen.pelanggan.kelola',
            'manajemen.pekerja.kelola',
            'manajemen.peran.kelola',
            'manajemen.profil_usaha.kelola',
            'manajemen.pembayaran.kelola',
        ],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Buat semua permission baru
        foreach ($this->newPermissions as $name => $description) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
            );
        }

        // 2. Migrasi permission dari role yang sudah ada
        $roles = \App\Models\SpatieRole::with('permissions')->get();

        foreach ($roles as $role) {
            $currentPermissions = $role->permissions->pluck('name')->toArray();
            $newPermissionsToAdd = [];

            foreach ($this->oldToNewMapping as $oldPermission => $newPermissions) {
                if (in_array($oldPermission, $currentPermissions)) {
                    $newPermissionsToAdd = array_merge($newPermissionsToAdd, $newPermissions);
                }
            }

            if (! empty($newPermissionsToAdd)) {
                // Tambahkan permission baru tanpa menghapus yang lama
                $role->givePermissionTo($newPermissionsToAdd);
            }
        }

        // 3. Hapus permission lama setelah migrasi
        Permission::whereIn('name', array_keys($this->oldToNewMapping))->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Buat ulang permission lama
        foreach (array_keys($this->oldToNewMapping) as $oldPermission) {
            Permission::firstOrCreate(
                ['name' => $oldPermission, 'guard_name' => 'web'],
            );
        }

        // 2. Migrasi balik permission ke role yang sudah ada
        $roles = \App\Models\SpatieRole::with('permissions')->get();

        foreach ($roles as $role) {
            $currentPermissions = $role->permissions->pluck('name')->toArray();

            foreach ($this->oldToNewMapping as $oldPermission => $newPermissions) {
                // Jika role memiliki salah satu dari permission baru, berikan permission lama
                if (count(array_intersect($currentPermissions, $newPermissions)) > 0) {
                    $role->givePermissionTo($oldPermission);
                }
            }
        }

        // 3. Hapus permission baru
        Permission::whereIn('name', array_keys($this->newPermissions))->delete();
    }
};
