<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InventoryLog extends Model
{
    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'inventory_logs';

    protected $guarded = [
        'id',
    ];

    protected function casts(): array
    {
        return [
            'quantity_change' => 'decimal:4',
            'quantity_after' => 'decimal:4',
        ];
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id', 'id');
    }

    public function materialBatch()
    {
        return $this->belongsTo(MaterialBatch::class, 'material_batch_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get action label in Indonesian
     */
    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'belanja' => 'Belanja',
            'terpakai' => 'Terpakai',
            'rusak' => 'Rusak',
            'hilang' => 'Hilang',
            'hitung' => 'Hitung',
            'produksi' => 'Produksi',
            default => ucfirst($this->action),
        };
    }

    /**
     * Get formatted quantity change with sign and unit
     */
    public function getFormattedChangeAttribute(): string
    {
        $unit = $this->materialBatch?->unit?->alias ?? '';
        $value = $this->quantity_change;

        if ($value > 0) {
            return '+'.number_format($value, 2, ',', '.').' '.$unit;
        }

        return number_format($value, 2, ',', '.').' '.$unit;
    }

    /**
     * Get formatted quantity after with unit
     */
    public function getFormattedAfterAttribute(): string
    {
        $unit = $this->materialBatch?->unit?->alias ?? '';

        return number_format($this->quantity_after, 2, ',', '.').' '.$unit;
    }
}
