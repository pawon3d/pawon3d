<?php

namespace App\Console\Commands;

use App\Models\Material;
use App\Models\MaterialBatch;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckInventoryAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:check-alerts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cek stok bahan yang akan habis dan bahan yang akan kadaluarsa, serta update status material';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Memeriksa stok bahan dan update status...');

        $statusUpdated = $this->updateMaterialStatuses();
        $lowStockCount = $this->checkLowStock();
        $expiringCount = $this->checkExpiringMaterials();

        $this->info("Selesai. Status diupdate: {$statusUpdated}, Notifikasi stok rendah: {$lowStockCount}, Notifikasi kadaluarsa: {$expiringCount}");

        return self::SUCCESS;
    }

    /**
     * Update status material berdasarkan stok dan kadaluarsa.
     */
    protected function updateMaterialStatuses(): int
    {
        $count = 0;
        $today = Carbon::today();

        $materials = Material::with('batches')->get();

        foreach ($materials as $material) {
            $oldStatus = $material->status;
            $newStatus = $this->determineStatus($material, $today);

            if ($oldStatus !== $newStatus) {
                $material->update(['status' => $newStatus]);
                $count++;
                $this->line("- {$material->name}: {$oldStatus} → {$newStatus}");
            }
        }

        return $count;
    }

    /**
     * Tentukan status material berdasarkan kondisi.
     */
    protected function determineStatus(Material $material, Carbon $today): string
    {
        // Hitung total stok dari semua batch
        $totalStock = $material->batches->sum('batch_quantity');

        // 1. Cek apakah ada batch yang sudah expired
        $hasExpiredBatch = $material->batches
            ->where('batch_quantity', '>', 0)
            ->filter(function ($batch) use ($today) {
                return $batch->date && Carbon::parse($batch->date)->lt($today);
            })
            ->isNotEmpty();

        if ($hasExpiredBatch) {
            return 'Expired';
        }

        // 2. Cek apakah stok kosong
        if ($totalStock <= 0) {
            return 'Kosong';
        }

        // 3. Cek apakah stok di bawah minimum (hampir habis)
        if ($material->minimum && $totalStock < $material->minimum) {
            return 'Hampir Habis';
        }

        // 4. Stok cukup
        return 'Tersedia';
    }

    /**
     * Cek bahan yang stoknya di bawah minimum.
     */
    protected function checkLowStock(): int
    {
        $count = 0;

        $materials = Material::with('batches')->get();

        foreach ($materials as $material) {
            // Hitung total stok dari semua batch
            $totalStock = $material->batches->sum('batch_quantity');

            // Jika stok di bawah minimum, kirim notifikasi
            if ($material->minimum && $totalStock < $material->minimum) {
                NotificationService::stockLow(
                    $material->name,
                    (int) $totalStock,
                    (int) $material->minimum
                );
                $count++;
                $this->line("  [Notifikasi] {$material->name}: stok {$totalStock} < minimum {$material->minimum}");
            }
        }

        return $count;
    }

    /**
     * Cek bahan yang akan kadaluarsa dalam 7 hari ke depan.
     */
    protected function checkExpiringMaterials(): int
    {
        $count = 0;
        $today = Carbon::today();
        $warningDate = $today->copy()->addDays(7);

        $expiringBatches = MaterialBatch::with('material')
            ->where('batch_quantity', '>', 0) // Hanya batch yang masih ada stok
            ->whereNotNull('date')
            ->whereBetween('date', [$today, $warningDate])
            ->get();

        foreach ($expiringBatches as $batch) {
            $expiryDate = Carbon::parse($batch->date);
            $daysUntilExpiry = $today->diffInDays($expiryDate);

            NotificationService::materialExpiringSoon(
                $batch->material->name,
                $daysUntilExpiry,
                $expiryDate->format('d/m/Y')
            );
            $count++;
            $this->line("  [Notifikasi] {$batch->material->name}: kadaluarsa dalam {$daysUntilExpiry} hari ({$expiryDate->format('d/m/Y')})");
        }

        return $count;
    }
}
