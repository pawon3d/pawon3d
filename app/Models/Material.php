<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Material extends Model
{
    use LogsActivity;

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'materials';

    protected $guarded = [
        'id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('materials')
            ->logOnly(['name', 'is_active', 'status', 'minimum', 'description'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(function (string $eventName) {
                $namaKategori = $this->name;

                $terjemahan = [
                    'created' => 'ditambahkan',
                    'updated' => 'diperbarui',
                    'deleted' => 'dihapus',
                    'restored' => 'dipulihkan',
                ];

                return "{$namaKategori} {$terjemahan[$eventName]}";
            })
            ->dontSubmitEmptyLogs();
    }

    public function material_details()
    {
        return $this->hasMany(MaterialDetail::class);
    }

    public function ingredientCategoryDetails()
    {
        return $this->hasMany(IngredientCategoryDetail::class);
    }

    public function expenseDetails()
    {
        return $this->hasMany(ExpenseDetail::class, 'material_id', 'id');
    }

    public function batches()
    {
        return $this->hasMany(MaterialBatch::class, 'material_id', 'id');
    }

    /**
     * Get total available quantity in a specific unit (with auto-conversion)
     *
     * @param  Unit  $targetUnit  Satuan tujuan untuk hasil
     * @return float Total quantity yang tersedia dalam satuan target
     */
    public function getTotalQuantityInUnit(Unit $targetUnit): float
    {
        // Fresh load to ensure we have latest batch data
        $this->load(['batches' => function ($query) {
            $query->with('unit');
        }]);

        $totalInTargetUnit = 0;
        $today = now()->startOfDay();

        foreach ($this->batches as $batch) {
            // Skip expired batches (date kurang dari hari ini)
            $batchDate = \Carbon\Carbon::parse($batch->date)->startOfDay();
            if ($batchDate->lt($today)) {
                continue;
            }

            $batchUnit = $batch->unit;
            if (! $batchUnit) {
                continue;
            }

            // Jika unit batch sama dengan target unit, langsung tambahkan
            // Gunakan == untuk UUID comparison (string)
            if ($batchUnit->id == $targetUnit->id) {
                $totalInTargetUnit += $batch->batch_quantity;

                continue;
            }

            // Coba konversi jika dalam grup yang sama
            $convertedQty = $batchUnit->convertTo($batch->batch_quantity, $targetUnit);
            if ($convertedQty !== null) {
                $totalInTargetUnit += $convertedQty;
            }
        }

        return $totalInTargetUnit;
    }

    /**
     * Reduce material quantity from batches (FIFO) with auto-conversion
     *
     * @param  float  $requiredQuantity  Jumlah yang dibutuhkan
     * @param  Unit  $requiredUnit  Satuan dari quantity yang dibutuhkan
     * @param  array  $logData  Data untuk inventory log
     * @return bool True jika berhasil dikurangi
     */
    public function reduceQuantity(float $requiredQuantity, Unit $requiredUnit, array $logData = []): bool
    {
        $this->load(['batches.unit']);

        // Ambil batches yang valid (tidak expired) dan urutkan FIFO
        $today = now()->startOfDay()->format('Y-m-d');
        $availableBatches = $this->batches()
            ->with('unit')
            ->whereDate('date', '>=', $today)
            ->where('batch_quantity', '>', 0)
            ->orderBy('date')
            ->get();

        // Cek apakah stok cukup
        $totalAvailable = $this->getTotalQuantityInUnit($requiredUnit);
        if ($totalAvailable < $requiredQuantity) {
            return false;
        }

        $remaining = $requiredQuantity;

        foreach ($availableBatches as $batch) {
            if ($remaining <= 0) {
                break;
            }

            $batchUnit = $batch->unit;
            if (! $batchUnit) {
                continue;
            }

            // Konversi remaining ke satuan batch
            $remainingInBatchUnit = $requiredUnit->convertTo($remaining, $batchUnit);
            if ($remainingInBatchUnit === null) {
                // Tidak bisa konversi, skip batch ini
                continue;
            }

            $quantityBefore = $batch->batch_quantity;
            $quantityUsed = 0;

            if ($batch->batch_quantity >= $remainingInBatchUnit) {
                // Batch ini cukup
                $quantityUsed = $remainingInBatchUnit;
                $batch->batch_quantity -= $remainingInBatchUnit;
                $batch->save();

                // Konversi quantity used kembali ke satuan required untuk menghitung remaining
                $usedInRequiredUnit = $batchUnit->convertTo($quantityUsed, $requiredUnit);
                $remaining -= $usedInRequiredUnit ?? 0;
            } else {
                // Batch ini tidak cukup, habiskan batch ini
                $quantityUsed = $batch->batch_quantity;
                $batch->batch_quantity = 0;
                $batch->save();

                // Konversi quantity used kembali ke satuan required
                $usedInRequiredUnit = $batchUnit->convertTo($quantityUsed, $requiredUnit);
                $remaining -= $usedInRequiredUnit ?? 0;
            }

            // Create inventory log jika data log disediakan
            if (! empty($logData)) {
                \App\Models\InventoryLog::create([
                    'material_id' => $this->id,
                    'material_batch_id' => $batch->id,
                    'user_id' => $logData['user_id'] ?? Auth::user()->id,
                    'action' => $logData['action'] ?? 'produksi',
                    'quantity_change' => -$quantityUsed,
                    'quantity_after' => $batch->batch_quantity,
                    'reference_type' => $logData['reference_type'] ?? null,
                    'reference_id' => $logData['reference_id'] ?? null,
                    'note' => $logData['note'] ?? '',
                ]);
            }
        }

        // Recalculate status setelah pengurangan
        $this->recalculateStatus();

        return true;
    }

    /**
     * Recalculate and update material status based on batches
     */
    public function recalculateStatus(): void
    {
        $this->load('batches');

        $hasExpiredBatch = $this->batches->contains(fn($batch) => $batch->date < now()->format('Y-m-d'));
        $totalQuantity = $this->batches->sum('batch_quantity');

        if ($hasExpiredBatch) {
            $status = 'Expired';
        } elseif ($totalQuantity <= 0) {
            $status = 'Kosong';
        } elseif ($totalQuantity <= $this->minimum) {
            $status = 'Habis';
        } elseif ($totalQuantity > $this->minimum * 2) {
            $status = 'Tersedia';
        } else {
            $status = 'Hampir Habis';
        }

        if ($this->status !== $status) {
            $this->update(['status' => $status]);
        }
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }
}
