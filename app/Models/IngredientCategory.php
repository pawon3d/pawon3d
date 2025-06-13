<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class IngredientCategory extends Model
{
    use LogsActivity;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'ingredient_categories';
    protected $guarded = [
        'id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('ingredient_categories')
            ->logOnly(['name', 'is_active'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(function (string $eventName) {
                $namaKategori = $this->name;

                $terjemahan = [
                    'created' => 'ditambahkan',
                    'updated' => 'diperbarui',
                    'deleted' => 'dihapus',
                    'restored' => 'dipulihkan',
                ];

                return "Kategori Persediaan {$namaKategori} {$terjemahan[$eventName]}";
            })
            ->dontSubmitEmptyLogs();
    }

    public function details()
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
