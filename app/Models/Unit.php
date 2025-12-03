<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Unit extends Model
{
    use LogsActivity;

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'units';

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'conversion_factor' => 'decimal:10',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('units')
            ->logOnly(['name', 'alias', 'group', 'base_unit_id', 'conversion_factor'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(function (string $eventName) {
                $namaKategori = $this->name;

                $terjemahan = [
                    'created' => 'ditambahkan',
                    'updated' => 'diperbarui',
                    'deleted' => 'dihapus',
                    'restored' => 'dipulihkan',
                ];

                return "Satuan {$namaKategori} {$terjemahan[$eventName]}";
            })
            ->dontSubmitEmptyLogs();
    }

    public function material_details()
    {
        return $this->hasMany(MaterialDetail::class);
    }

    /**
     * Relasi ke satuan dasar (base unit) dalam grup yang sama
     */
    public function baseUnit()
    {
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }

    /**
     * Relasi ke satuan-satuan turunan
     */
    public function derivedUnits()
    {
        return $this->hasMany(Unit::class, 'base_unit_id');
    }

    /**
     * Cek apakah satuan ini adalah satuan dasar (tidak punya base_unit)
     */
    public function isBaseUnit(): bool
    {
        return is_null($this->base_unit_id);
    }

    /**
     * Cek apakah satuan ini memiliki tangga konversi (punya grup dan base_unit atau derived units)
     */
    public function hasConversionLadder(): bool
    {
        if (empty($this->group)) {
            return false;
        }

        // Cek apakah ada satuan lain dalam grup yang sama
        return self::where('group', $this->group)
            ->where('id', '!=', $this->id)
            ->exists();
    }

    /**
     * Cek apakah dua satuan berada dalam tangga konversi yang sama
     */
    public function canAutoConvertTo(Unit $targetUnit): bool
    {
        // Jika salah satu tidak punya grup, tidak bisa auto convert
        if (empty($this->group) || empty($targetUnit->group)) {
            return false;
        }

        // Jika grup berbeda, tidak bisa auto convert
        if ($this->group !== $targetUnit->group) {
            return false;
        }

        // Jika dalam grup yang sama dan keduanya punya conversion_factor, bisa auto convert
        return true;
    }

    /**
     * Konversi nilai dari satuan ini ke satuan target
     *
     * @param  float  $value  Nilai dalam satuan ini
     * @param  Unit  $targetUnit  Satuan tujuan
     * @return float|null Nilai dalam satuan tujuan, atau null jika tidak bisa dikonversi
     */
    public function convertTo(float $value, Unit $targetUnit): ?float
    {
        if (! $this->canAutoConvertTo($targetUnit)) {
            return null;
        }

        // Konversi ke satuan dasar dulu, lalu ke satuan target
        // Contoh: 500 gram ke kg
        // 500 * 0.001 (factor gram) = 0.5 kg base
        // 0.5 / 1 (factor kg) = 0.5 kg
        $valueInBase = $value * $this->conversion_factor;
        $valueInTarget = $valueInBase / $targetUnit->conversion_factor;

        return $valueInTarget;
    }

    /**
     * Dapatkan faktor konversi dari satuan ini ke satuan target
     *
     * @return float|null Faktor konversi, atau null jika tidak bisa dikonversi
     */
    public function getConversionFactorTo(Unit $targetUnit): ?float
    {
        if (! $this->canAutoConvertTo($targetUnit)) {
            return null;
        }

        // Faktor = conversion_factor_this / conversion_factor_target
        // Contoh: gram ke kg = 0.001 / 1 = 0.001 (1 gram = 0.001 kg)
        // Contoh: kg ke gram = 1 / 0.001 = 1000 (1 kg = 1000 gram)
        return $this->conversion_factor / $targetUnit->conversion_factor;
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }
}
