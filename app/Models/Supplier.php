<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Supplier extends Model
{
    use LogsActivity;

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'suppliers';

    protected $guarded = [
        'id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('suppliers')
            ->logOnly(['name', 'description', 'contact_name', 'phone', 'street', 'landmark', 'maps_link', 'image'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(function (string $eventName) {
                $namaToko = $this->name;

                $terjemahan = [
                    'created' => 'ditambahkan',
                    'updated' => 'diperbarui',
                    'deleted' => 'dihapus',
                    'restored' => 'dipulihkan',
                ];

                return "Toko {$namaToko} {$terjemahan[$eventName]}";
            })
            ->dontSubmitEmptyLogs();
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'supplier_id', 'id');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }
}
