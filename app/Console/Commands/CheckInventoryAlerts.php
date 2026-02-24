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
        // Hanya pertimbangkan batch yang masih memiliki stok
        $activeBatches = $material->batches->where('batch_quantity', '>', 0);

        // 1. Tidak ada batch berisi stok — kosong
        if ($activeBatches->isEmpty()) {
            return 'Kosong';
        }

        // 2. Semua batch berisi stok sudah expired
        $allExpired = $activeBatches->every(function ($batch) use ($today) {
            return $batch->date && Carbon::parse($batch->date)->lt($today);
        });

        if ($allExpired) {
            return 'Expired';
        }

        // 3. Hitung stok dari batch yang valid (belum expired)
        $validQuantity = $activeBatches
            ->filter(function ($batch) use ($today) {
                return $batch->date && ! Carbon::parse($batch->date)->lt($today);
            })
            ->sum('batch_quantity');

        if ($validQuantity <= 0) {
            return 'Kosong';
        }

        // 4. Cek apakah stok di bawah minimum (hampir habis)
        if ($material->minimum && $validQuantity < $material->minimum) {
            return 'Hampir Habis';
        }

        // 5. Stok cukup
        return 'Tersedia';
    }

    /**
     * Cek bahan yang stoknya di bawah minimum.
     */
    protected function checkLowStock(): int
    {
        $count = 0;

        $materials = Material::with(['batches.unit'])->get();

        foreach ($materials as $material) {
            // Hitung total stok dari semua batch
            $totalStock = $material->batches->sum('batch_quantity');

            // Jika stok di bawah minimum, kirim notifikasi
            if ($material->minimum && $totalStock < $material->minimum) {
                // Ambil unit dari batch pertama yang memiliki unit
                $unit = $material->batches->first()?->unit?->alias ?? $material->batches->first()?->unit?->name ?? '';

                NotificationService::stockLow(
                    $material->name,
                    $totalStock,
                    $material->minimum,
                    $unit
                );
                $count++;
                $this->line("  [Notifikasi] {$material->name}: stok {$totalStock} < minimum {$material->minimum}");
            }
        }

        return $count;
    }

    /**
     * Cek bahan yang akan kadaluarsa dalam 7 hari ke depan.
     * Notifikasi dikirim sekali per material (deduplikasi per bahan, bukan per batch).
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

        // Deduplikasi: ambil satu batch per material (yang paling dekat kadaluarsanya)
        $materialsToNotify = $expiringBatches
            ->groupBy('material_id')
            ->map(fn ($batches) => $batches->sortBy('date')->first());

        foreach ($materialsToNotify as $batch) {
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
