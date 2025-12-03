<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    // =============================================
    // KASIR NOTIFICATIONS
    // =============================================

    /**
     * Notifikasi: Pesanan masuk ke antrian
     */
    public static function orderQueued(string $invoiceNumber): void
    {
        $body = 'Pesanan <span class="font-bold">' . $invoiceNumber . '</span> telah masuk ke <span class="font-bold text-[#3fa2f7]">Antrian Pesanan</span>.';

        self::createForPermission($body, 'kasir', 'kasir.pesanan.kelola');
    }

    /**
     * Notifikasi: Pesanan sedang diproses
     */
    public static function orderProcessing(string $invoiceNumber): void
    {
        $body = 'Pesanan <span class="font-bold">' . $invoiceNumber . '</span> <span class="font-bold text-[#ffc400]">sedang diproses</span>.';

        self::createForPermission($body, 'kasir', 'kasir.pesanan.kelola');
    }

    /**
     * Notifikasi: Pesanan dapat diambil
     */
    public static function orderReadyForPickup(string $invoiceNumber): void
    {
        $body = 'Pesanan <span class="font-bold">' . $invoiceNumber . '</span> <span class="font-bold text-[#6f42c1]">dapat diambil</span>.';

        self::createForPermission($body, 'kasir', 'kasir.pesanan.kelola');
    }

    /**
     * Notifikasi: Pesanan selesai
     */
    public static function orderCompleted(string $invoiceNumber): void
    {
        $body = 'Pesanan <span class="font-bold">' . $invoiceNumber . '</span> telah <span class="font-bold text-[#56c568]">Selesai</span>.';

        self::createForPermission($body, 'kasir', 'kasir.pesanan.kelola');
    }

    /**
     * Notifikasi: Pesanan dibatalkan
     */
    public static function orderCancelled(string $invoiceNumber, string $paymentStatus): void
    {
        $statusClass = $paymentStatus === 'Lunas' ? 'text-[#56c568]' : 'text-[#ffc400]';
        $body = 'Pesanan <span class="font-bold">' . $invoiceNumber . '</span> telah <span class="font-bold text-[#eb5757]">dibatalkan</span> dengan status <span class="font-bold ' . $statusClass . '">' . $paymentStatus . '</span>.';

        self::createForPermission($body, 'kasir', 'kasir.pesanan.kelola');
    }

    /**
     * Notifikasi: Pembayaran uang muka diterima
     */
    public static function paymentDownPayment(string $invoiceNumber, int $amount): void
    {
        $body = 'Transaksi <span class="font-bold text-[#ffc400]">Uang Muka</span> sebesar <span class="font-bold">Rp' . number_format($amount, 0, ',', '.') . '</span> untuk pesanan <span class="font-bold">' . $invoiceNumber . '</span> diterima.';

        self::createForPermission($body, 'kasir', 'kasir.pesanan.kelola');
    }

    /**
     * Notifikasi: Pembayaran lunas
     */
    public static function paymentCompleted(string $invoiceNumber, int $amount): void
    {
        $body = 'Transaksi <span class="font-bold text-[#56c568]">Lunas</span> sebesar <span class="font-bold">Rp' . number_format($amount, 0, ',', '.') . '</span> untuk pesanan <span class="font-bold">' . $invoiceNumber . '</span> diterima.';

        self::createForPermission($body, 'kasir', 'kasir.pesanan.kelola');
    }

    /**
     * Notifikasi: Refund diserahkan
     */
    public static function refundProcessed(string $invoiceNumber, int $amount): void
    {
        $body = 'Transaksi <span class="font-bold text-[#eb5757]">Refund</span> sebesar <span class="font-bold">Rp' . number_format($amount, 0, ',', '.') . '</span> dari pesanan <span class="font-bold">' . $invoiceNumber . '</span> diserahkan.';

        self::createForPermission($body, 'kasir', 'kasir.pesanan.kelola');
    }

    /**
     * Notifikasi: Struk dicetak
     */
    public static function receiptPrinted(string $receiptNumber, string $invoiceNumber): void
    {
        $body = 'Struk <span class="font-bold">' . $receiptNumber . '</span> dari pesanan <span class="font-bold">' . $invoiceNumber . '</span> berhasil dicetak.';

        self::createForPermission($body, 'kasir', 'kasir.pesanan.kelola');
    }

    // =============================================
    // PRODUKSI NOTIFICATIONS
    // =============================================

    /**
     * Notifikasi: Produksi direncanakan
     */
    public static function productionPlanned(string $productionNumber): void
    {
        $body = 'Produksi <span class="font-bold">' . $productionNumber . '</span> telah <span class="font-bold text-[#3fa2f7]">direncanakan</span>.';

        self::createForPermission($body, 'produksi', 'produksi.rencana.kelola');
    }

    /**
     * Notifikasi: Produksi diproses
     */
    public static function productionProcessing(string $productionNumber): void
    {
        $body = 'Produksi <span class="font-bold">' . $productionNumber . '</span> sedang <span class="font-bold text-[#ffc400]">diproses</span>.';

        self::createForPermission($body, 'produksi', 'produksi.mulai');
    }

    /**
     * Notifikasi: Produksi selesai
     */
    public static function productionCompleted(string $productionNumber): void
    {
        $body = 'Produksi <span class="font-bold">' . $productionNumber . '</span> telah <span class="font-bold text-[#56c568]">selesai</span>.';

        self::createForPermission($body, 'produksi', 'produksi.mulai');
    }

    /**
     * Notifikasi: Produksi dibatalkan
     */
    public static function productionCancelled(string $productionNumber): void
    {
        $body = 'Produksi <span class="font-bold">' . $productionNumber . '</span> telah <span class="font-bold text-[#eb5757]">dibatalkan</span>.';

        self::createForPermission($body, 'produksi', 'produksi.rencana.kelola');
    }

    /**
     * Notifikasi: Pesanan masuk antrian produksi
     */
    public static function orderInProductionQueue(string $invoiceNumber): void
    {
        $body = 'Pesanan <span class="font-bold">' . $invoiceNumber . '</span> masuk ke <span class="font-bold text-[#3fa2f7]">antrian pesanan</span>.';

        self::createForPermission($body, 'produksi', 'produksi.rencana.kelola');
    }

    /**
     * Notifikasi: Pesanan sedang diproduksi
     */
    public static function orderInProduction(string $invoiceNumber): void
    {
        $body = 'Pesanan <span class="font-bold">' . $invoiceNumber . '</span> sedang <span class="font-bold text-[#ffc400]">diproses</span>.';

        self::createForPermission($body, 'produksi', 'produksi.mulai');
    }

    /**
     * Notifikasi: Pesanan selesai diproduksi
     */
    public static function orderProductionCompleted(string $invoiceNumber): void
    {
        $body = 'Pesanan <span class="font-bold">' . $invoiceNumber . '</span> telah <span class="font-bold text-[#56c568]">selesai</span> dan dapat diambil.';

        self::createForPermission($body, 'produksi', 'produksi.mulai');
    }

    /**
     * Notifikasi: Pengingat deadline pesanan
     */
    public static function orderDeadlineReminder(string $invoiceNumber, int $daysRemaining): void
    {
        $body = 'Tanggal pengambilan pesanan <span class="font-bold">' . $invoiceNumber . '</span> tersisa <span class="font-bold text-[#eb5757]">' . $daysRemaining . ' hari lagi</span>. Ayo mulai produksi!';

        self::createForPermission($body, 'produksi', 'produksi.rencana.kelola');
    }

    // =============================================
    // INVENTORI NOTIFICATIONS
    // =============================================

    /**
     * Notifikasi: Stok hampir habis
     */
    public static function stockLow(string $materialName, int|float $currentStock, int|float $minimum, string $unit = ''): void
    {
        $unitText = $unit ? ' ' . $unit : '';
        $body = 'Stok <span class="font-bold">' . $materialName . '</span> <span class="font-bold text-[#eb5757]">hampir habis</span>. Sisa <span class="font-bold">' . number_format($currentStock, 0, ',', '.') . $unitText . '</span> (minimum: ' . number_format($minimum, 0, ',', '.') . ').';

        self::createForPermission($body, 'inventori', 'inventori.persediaan.kelola');
    }

    /**
     * Notifikasi: Stok ditambahkan
     */
    public static function stockAdded(string $materialName, string $quantity, string $unit): void
    {
        $body = 'Stok <span class="font-bold">' . $materialName . '</span> telah <span class="font-bold text-[#56c568]">ditambahkan</span> sebanyak ' . $quantity . ' ' . $unit . '.';

        self::createForPermission($body, 'inventori', 'inventori.persediaan.kelola');
    }

    /**
     * Notifikasi: Pembelian bahan baku diterima
     */
    public static function purchaseReceived(string $purchaseNumber): void
    {
        $body = 'Pembelian bahan baku <span class="font-bold">' . $purchaseNumber . '</span> telah <span class="font-bold text-[#56c568]">diterima</span>.';

        self::createForPermission($body, 'inventori', 'inventori.belanja.mulai');
    }

    /**
     * Notifikasi: Penghitungan stok selesai
     */
    public static function stockCountCompleted(string $countNumber): void
    {
        $body = 'Penghitungan stok <span class="font-bold">' . $countNumber . '</span> telah <span class="font-bold text-[#56c568]">selesai</span>.';

        self::createForPermission($body, 'inventori', 'inventori.hitung.kelola');
    }

    /**
     * Notifikasi: Bahan baku akan kadaluarsa
     */
    public static function materialExpiringSoon(string $materialName, int $daysRemaining): void
    {
        $body = 'Bahan baku <span class="font-bold">' . $materialName . '</span> akan <span class="font-bold text-[#ffc400]">kadaluarsa</span> dalam ' . $daysRemaining . ' hari.';

        self::createForPermission($body, 'inventori', 'inventori.persediaan.kelola');
    }

    /**
     * Notifikasi: Rencana belanja dibuat
     */
    public static function shoppingPlanCreated(string $planNumber): void
    {
        $body = 'Rencana belanja <span class="font-bold">' . $planNumber . '</span> telah <span class="font-bold text-[#3fa2f7]">dibuat</span>.';

        self::createForPermission($body, 'inventori', 'inventori.belanja.rencana.kelola');
    }

    /**
     * Notifikasi: Belanja dimulai
     */
    public static function shoppingStarted(string $expenseNumber): void
    {
        $body = 'Belanja <span class="font-bold">' . $expenseNumber . '</span> <span class="font-bold text-[#ffc400]">sedang berlangsung</span>.';

        self::createForPermission($body, 'inventori', 'inventori.belanja.mulai');
    }

    /**
     * Notifikasi: Belanja selesai
     */
    public static function shoppingCompleted(string $expenseNumber): void
    {
        $body = 'Belanja <span class="font-bold">' . $expenseNumber . '</span> telah <span class="font-bold text-[#56c568]">selesai</span>.';

        self::createForPermission($body, 'inventori', 'inventori.belanja.mulai');
    }

    /**
     * Notifikasi: Belanja dibatalkan
     */
    public static function shoppingCancelled(string $expenseNumber): void
    {
        $body = 'Belanja <span class="font-bold">' . $expenseNumber . '</span> telah <span class="font-bold text-[#eb5757]">dibatalkan</span>.';

        self::createForPermission($body, 'inventori', 'inventori.belanja.rencana.kelola');
    }

    /**
     * Notifikasi: Penghitungan stok direncanakan
     */
    public static function stockCountPlanned(string $countNumber): void
    {
        $body = 'Penghitungan stok <span class="font-bold">' . $countNumber . '</span> telah <span class="font-bold text-[#3fa2f7]">direncanakan</span>.';

        self::createForPermission($body, 'inventori', 'inventori.hitung.kelola');
    }

    /**
     * Notifikasi: Penghitungan stok dimulai
     */
    public static function stockCountStarted(string $countNumber): void
    {
        $body = 'Penghitungan stok <span class="font-bold">' . $countNumber . '</span> <span class="font-bold text-[#ffc400]">sedang berlangsung</span>.';

        self::createForPermission($body, 'inventori', 'inventori.hitung.kelola');
    }

    /**
     * Notifikasi: Penghitungan stok dibatalkan
     */
    public static function stockCountCancelled(string $countNumber): void
    {
        $body = 'Penghitungan stok <span class="font-bold">' . $countNumber . '</span> telah <span class="font-bold text-[#eb5757]">dibatalkan</span>.';

        self::createForPermission($body, 'inventori', 'inventori.hitung.kelola');
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Buat notifikasi untuk semua user dengan permission tertentu.
     */
    private static function createForPermission(string $body, string $type, string $permission): void
    {
        $users = User::permission($permission)->get();

        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'title' => '',
                'body' => $body,
                'type' => $type,
                'status' => 0,
                'is_read' => false,
            ]);
        }
    }

    /**
     * Buat notifikasi untuk user tertentu.
     */
    public static function createForUser(string $userId, string $body, string $type): void
    {
        Notification::create([
            'user_id' => $userId,
            'title' => '',
            'body' => $body,
            'type' => $type,
            'status' => 0,
            'is_read' => false,
        ]);
    }
}
