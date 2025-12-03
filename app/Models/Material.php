<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
