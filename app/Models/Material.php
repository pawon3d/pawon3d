<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Models\ProcessedMaterialDetail;
use Illuminate\Database\Eloquent\Model;
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

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }
}
