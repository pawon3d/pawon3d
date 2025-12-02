<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Daftar permission berdasarkan kategori.
     * Sesuai dengan desain Figma untuk halaman Tambah/Rincian Peran.
     */
    protected array $permissions = [
        'kasir' => [
            'kasir.pesanan.kelola' => 'Akses membuat, mengubah, menghapus, dan membatalkan pesanan reguler, pesanan kotak, dan siap saji.',
            'kasir.laporan.kelola' => 'Akses mengelola laporan kasir.',
        ],
        'produksi' => [
            'produksi.rencana.kelola' => 'Akses membuat, mengubah, menghapus, dan membatalkan rencana produksi siap saji.',
            'produksi.mulai' => 'Akses memulai produksi pesanan reguler, pesanan kotak, dan siap saji.',
            'produksi.laporan.kelola' => 'Akses mengelola laporan produksi.',
        ],
        'inventori' => [
            'inventori.produk.kelola' => 'Akses membuat, mengubah, dan menghapus produk (pesanan reguler, pesanan kotak, dan siap saji).',
            'inventori.persediaan.kelola' => 'Akses membuat, mengubah, dan menghapus persediaan.',
            'inventori.belanja.rencana.kelola' => 'Akses membuat, mengubah, menghapus, dan membatalkan rencana belanja persediaan.',
            'inventori.toko.kelola' => 'Akses membuat, mengubah, menghapus toko persediaan.',
            'inventori.belanja.mulai' => 'Akses memulai belanja persediaan.',
            'inventori.hitung.kelola' => 'Akses membuat, mengubah, menghapus, dan memulai rencana hitung, catat rusak, dan catat hilang persediaan.',
            'inventori.alur.lihat' => 'Akses melihat alur persediaan.',
            'inventori.laporan.kelola' => 'Akses mengelola laporan inventori.',
        ],
        'manajemen' => [
            'manajemen.pelanggan.kelola' => 'Akses membuat, mengubah, dan menghapus data pelanggan.',
            'manajemen.pekerja.kelola' => 'Akses membuat, mengubah, dan menghapus data pekerja.',
            'manajemen.peran.kelola' => 'Akses membuat, mengubah, dan menghapus data peran.',
            'manajemen.profil_usaha.kelola' => 'Akses membuat, mengubah, dan menghapus profil usaha.',
            'manajemen.pembayaran.kelola' => 'Akses membuat, mengubah, dan menghapus metode pembayaran.',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->permissions as $category => $perms) {
            foreach ($perms as $name => $description) {
                Permission::firstOrCreate(
                    ['name' => $name, 'guard_name' => 'web'],
                );
            }
        }

        $this->command->info('Permissions seeded successfully!');
        $this->command->table(
            ['Kategori', 'Total Permission'],
            collect($this->permissions)->map(fn ($perms, $cat) => [$cat, count($perms)])->toArray()
        );
    }

    /**
     * Get all permissions grouped by category.
     * Can be used by other classes to get permission structure.
     */
    public static function getPermissionsByCategory(): array
    {
        return (new self)->permissions;
    }

    /**
     * Get all permission names as flat array.
     */
    public static function getAllPermissionNames(): array
    {
        $all = [];
        foreach ((new self)->permissions as $perms) {
            $all = array_merge($all, array_keys($perms));
        }

        return $all;
    }

    /**
     * Get permission names for a specific category.
     */
    public static function getPermissionNamesByCategory(string $category): array
    {
        $instance = new self;

        return array_keys($instance->permissions[$category] ?? []);
    }
}
