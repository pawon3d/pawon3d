<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first user with kasir permission or any user
        $user = User::first();

        if (! $user) {
            return;
        }

        $userId = $user->id;

        // Clear existing notifications
        Notification::truncate();

        // =====================
        // KASIR NOTIFICATIONS
        // =====================

        // Today (12 Mei 2025 style)
        $today = Carbon::now();

        $kasirNotifications = [
            // Today
            [
                'body' => 'Pesanan <span class="font-bold">OR-250512-0001</span> telah <span class="font-bold text-[#56c568]">Selesai</span>',
                'type' => 'kasir',
                'created_at' => $today->copy()->subMinutes(1),
            ],
            [
                'body' => 'Struk <span class="font-bold">250512-0001</span> dari pesanan <span class="font-bold">OR-250512-0001</span> berhasil dicetak.',
                'type' => 'kasir',
                'created_at' => $today->copy()->subMinutes(1),
            ],
            [
                'body' => 'Transaksi <span class="font-bold text-[#56c568]">Lunas</span> sebesar <span class="font-bold">Rp237.000</span> untuk pesanan <span class="font-bold">OR-250512-0001</span> diterima.',
                'type' => 'kasir',
                'created_at' => $today->copy()->subMinutes(2),
            ],
            [
                'body' => 'Pesanan <span class="font-bold">OR-250512-0001</span> <span class="font-bold text-[#6f42c1]">dapat diambil</span>.',
                'type' => 'kasir',
                'created_at' => $today->copy()->subHour(),
            ],
            [
                'body' => 'Pesanan <span class="font-bold">OR-250512-0003</span> telah <span class="font-bold text-[#eb5757]">dibatalkan</span> dengan status <span class="font-bold text-[#56c568]">Lunas</span>.',
                'type' => 'kasir',
                'created_at' => $today->copy()->subHours(2),
            ],

            // Yesterday
            [
                'body' => 'Pesanan <span class="font-bold">OR-250512-0002</span> telah <span class="font-bold text-[#eb5757]">dibatalkan</span> dengan status <span class="font-bold text-[#ffc400]">Belum Lunas</span>.',
                'type' => 'kasir',
                'created_at' => $today->copy()->subDay(),
            ],

            // 3 days ago
            [
                'body' => 'Transaksi <span class="font-bold text-[#eb5757]">Refund</span> sebesar <span class="font-bold">Rp80.000</span> dari pesanan <span class="font-bold">OR-250507-0001</span> diserahkan.',
                'type' => 'kasir',
                'created_at' => $today->copy()->subDays(3),
            ],
            [
                'body' => 'Pesanan <span class="font-bold">OR-250512-0001</span> <span class="font-bold text-[#ffc400]">sedang diproses</span>.',
                'type' => 'kasir',
                'created_at' => $today->copy()->subDays(3),
            ],
            [
                'body' => 'Pesanan <span class="font-bold">OR-250512-0001</span> telah masuk ke <span class="font-bold text-[#3fa2f7]">Antrian Pesanan</span>.',
                'type' => 'kasir',
                'created_at' => $today->copy()->subDays(3),
            ],
            [
                'body' => 'Struk <span class="font-bold">250509-0002</span> dari pesanan <span class="font-bold">OR-250512-0001</span> berhasil dicetak.',
                'type' => 'kasir',
                'created_at' => $today->copy()->subDays(3),
            ],
            [
                'body' => 'Transaksi <span class="font-bold text-[#ffc400]">Uang Muka</span> sebesar <span class="font-bold">Rp100.000</span> untuk pesanan <span class="font-bold">OR-250512-0001</span> diterima.',
                'type' => 'kasir',
                'created_at' => $today->copy()->subDays(3),
            ],
            [
                'body' => 'Pesanan <span class="font-bold">OK-250512-0001</span> <span class="font-bold text-[#ffc400]">sedang diproses</span>.',
                'type' => 'kasir',
                'created_at' => $today->copy()->subDays(3),
            ],
            [
                'body' => 'Pesanan <span class="font-bold">OK-250512-0001</span> masuk ke <span class="font-bold text-[#3fa2f7]">Antrian Pesanan</span>.',
                'type' => 'kasir',
                'created_at' => $today->copy()->subDays(3),
            ],
            [
                'body' => 'Struk <span class="font-bold">250509-0001</span> dari pesanan <span class="font-bold">OK-250512-0001</span> berhasil dicetak.',
                'type' => 'kasir',
                'created_at' => $today->copy()->subDays(3),
            ],
            [
                'body' => 'Transaksi <span class="font-bold text-[#ffc400]">Uang Muka</span> sebesar <span class="font-bold">Rp100.000</span> untuk pesanan <span class="font-bold">OK-250512-0001</span> diterima.',
                'type' => 'kasir',
                'created_at' => $today->copy()->subDays(3),
            ],
            [
                'body' => 'Struk <span class="font-bold">250509-0003</span> dari pembelian <span class="font-bold">OS-250509-0001</span> berhasil dicetak.',
                'type' => 'kasir',
                'created_at' => $today->copy()->subDays(3),
            ],
            [
                'body' => 'Transaksi <span class="font-bold text-[#56c568]">Lunas</span> sebesar <span class="font-bold">Rp20.000</span> untuk pembelian <span class="font-bold">OS-250509-0001</span> diterima.',
                'type' => 'kasir',
                'created_at' => $today->copy()->subDays(3),
            ],
        ];

        // =====================
        // PRODUKSI NOTIFICATIONS
        // =====================

        $produksiNotifications = [
            // Today
            [
                'body' => 'Pesanan <span class="font-bold">OK-250512-0001</span> telah <span class="font-bold text-[#56c568]">selesai</span> dan dapat diambil.',
                'type' => 'produksi',
                'created_at' => $today->copy()->subMinutes(1),
            ],
            [
                'body' => 'Pesanan <span class="font-bold">OR-250512-0001</span> telah <span class="font-bold text-[#56c568]">selesai</span> dan dapat diambil.',
                'type' => 'produksi',
                'created_at' => $today->copy()->subMinutes(1),
            ],
            [
                'body' => 'Produksi <span class="font-bold">PS-250512-0001</span> telah <span class="font-bold text-[#56c568]">selesai</span>.',
                'type' => 'produksi',
                'created_at' => $today->copy()->subMinutes(1),
            ],
            [
                'body' => 'Produksi <span class="font-bold">PS-250512-0001</span> telah <span class="font-bold text-[#3fa2f7]">direncanakan</span>.',
                'type' => 'produksi',
                'created_at' => $today->copy()->subMinutes(1),
            ],

            // Yesterday
            [
                'body' => 'Produksi <span class="font-bold">PS-250511-0002</span> telah <span class="font-bold text-[#56c568]">selesai</span>.',
                'type' => 'produksi',
                'created_at' => $today->copy()->subDay(),
            ],
            [
                'body' => 'Produksi <span class="font-bold">PS-250511-0002</span> telah <span class="font-bold text-[#3fa2f7]">direncanakan</span>.',
                'type' => 'produksi',
                'created_at' => $today->copy()->subDay(),
            ],
            [
                'body' => 'Produksi <span class="font-bold">PS-250511-0001</span> telah <span class="font-bold text-[#eb5757]">dibatalkan</span>.',
                'type' => 'produksi',
                'created_at' => $today->copy()->subDay(),
            ],
            [
                'body' => 'Produksi <span class="font-bold">PS-250511-0001</span> telah <span class="font-bold text-[#3fa2f7]">direncanakan</span>.',
                'type' => 'produksi',
                'created_at' => $today->copy()->subDay(),
            ],

            // 3 days ago
            [
                'body' => 'Pesanan <span class="font-bold">OK-250512-0001</span> sedang <span class="font-bold text-[#ffc400]">diproses</span>.',
                'type' => 'produksi',
                'created_at' => $today->copy()->subDays(3),
            ],
            [
                'body' => 'Pesanan <span class="font-bold">OR-250512-0001</span> sedang <span class="font-bold text-[#ffc400]">diproses</span>.',
                'type' => 'produksi',
                'created_at' => $today->copy()->subDays(3),
            ],
            [
                'body' => 'Tanggal pengambilan pesanan <span class="font-bold">OK-250512-0001</span> tersisa <span class="font-bold text-[#eb5757]">3 hari lagi</span>. Ayo mulai produksi!',
                'type' => 'produksi',
                'created_at' => $today->copy()->subDays(3),
            ],
            [
                'body' => 'Tanggal pengambilan pesanan <span class="font-bold">OR-250512-0001</span> tersisa <span class="font-bold text-[#eb5757]">3 hari lagi</span>. Ayo mulai produksi!',
                'type' => 'produksi',
                'created_at' => $today->copy()->subDays(3),
            ],
            [
                'body' => 'Produksi <span class="font-bold">PS-250509-0001</span> telah <span class="font-bold text-[#56c568]">selesai</span>.',
                'type' => 'produksi',
                'created_at' => $today->copy()->subDays(3),
            ],
            [
                'body' => 'Pesanan <span class="font-bold">OK-250512-0001</span> masuk ke <span class="font-bold text-[#3fa2f7]">antrian pesanan</span>.',
                'type' => 'produksi',
                'created_at' => $today->copy()->subDays(3),
            ],
            [
                'body' => 'Pesanan <span class="font-bold">OR-250512-0001</span> masuk ke <span class="font-bold text-[#3fa2f7]">antrian pesanan</span>.',
                'type' => 'produksi',
                'created_at' => $today->copy()->subDays(3),
            ],
            [
                'body' => 'Produksi <span class="font-bold">PS-250509-0001</span> telah <span class="font-bold text-[#3fa2f7]">direncanakan</span>.',
                'type' => 'produksi',
                'created_at' => $today->copy()->subDays(3),
            ],
        ];

        // =====================
        // INVENTORI NOTIFICATIONS
        // =====================

        $inventoriNotifications = [
            // Today
            [
                'body' => 'Stok <span class="font-bold">Tepung Terigu</span> <span class="font-bold text-[#eb5757]">hampir habis</span>. Sisa 2 kg.',
                'type' => 'inventori',
                'created_at' => $today->copy()->subMinutes(30),
            ],
            [
                'body' => 'Pembelian bahan baku <span class="font-bold">PB-250512-0001</span> telah <span class="font-bold text-[#56c568]">diterima</span>.',
                'type' => 'inventori',
                'created_at' => $today->copy()->subHours(2),
            ],

            // Yesterday
            [
                'body' => 'Stok <span class="font-bold">Gula Pasir</span> telah <span class="font-bold text-[#56c568]">ditambahkan</span> sebanyak 10 kg.',
                'type' => 'inventori',
                'created_at' => $today->copy()->subDay(),
            ],
            [
                'body' => 'Penghitungan stok <span class="font-bold">HS-250511-0001</span> telah <span class="font-bold text-[#56c568]">selesai</span>.',
                'type' => 'inventori',
                'created_at' => $today->copy()->subDay(),
            ],

            // 3 days ago
            [
                'body' => 'Bahan baku <span class="font-bold">Mentega</span> akan <span class="font-bold text-[#ffc400]">kadaluarsa</span> dalam 5 hari.',
                'type' => 'inventori',
                'created_at' => $today->copy()->subDays(3),
            ],
            [
                'body' => 'Rencana belanja <span class="font-bold">RB-250509-0001</span> telah <span class="font-bold text-[#3fa2f7]">dibuat</span>.',
                'type' => 'inventori',
                'created_at' => $today->copy()->subDays(3),
            ],
        ];

        // Insert all notifications
        $allNotifications = array_merge($kasirNotifications, $produksiNotifications, $inventoriNotifications);

        foreach ($allNotifications as $notification) {
            Notification::create([
                'user_id' => $userId,
                'title' => '',
                'body' => $notification['body'],
                'type' => $notification['type'],
                'status' => 0,
                'is_read' => false,
                'created_at' => $notification['created_at'],
                'updated_at' => $notification['created_at'],
            ]);
        }
    }
}
