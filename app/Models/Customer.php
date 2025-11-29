<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Customer extends Model
{
    use LogsActivity;

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'customers';

    protected $guarded = [
        'id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('customers')
            ->logOnly(['name', 'phone', 'points'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(function (string $eventName) {
                $namaPelanggan = $this->name;
                $pointChange = '';

                if ($eventName === 'updated' && $this->isDirty('points')) {
                    $oldPoints = $this->getOriginal('points') ?? 0;
                    $newPoints = $this->points ?? 0;
                    $difference = $newPoints - $oldPoints;

                    if ($difference > 0) {
                        $pointChange = " (+{$difference} poin)";
                    } elseif ($difference < 0) {
                        $pointChange = " ({$newPoints} poin)";
                    }
                } elseif ($eventName === 'created' && $this->points) {
                    $pointChange = " (Poin: {$this->points})";
                }

                $namaPelanggan .= $pointChange;

                if ($eventName == 'updated' && $this->isDirty('points')) {
                    $terjemahan = [
                        'created' => 'ditambahkan',
                        'updated' => '',
                        'deleted' => 'dihapus',
                        'restored' => 'dipulihkan',
                    ];
                } else {
                    $terjemahan = [
                        'created' => 'ditambahkan',
                        'updated' => 'diperbarui',
                        'deleted' => 'dihapus',
                        'restored' => 'dipulihkan',
                    ];
                }

                return "Pelanggan {$namaPelanggan} {$terjemahan[$eventName]}";
            })
            ->dontSubmitEmptyLogs();
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'phone', 'phone');
    }

    public function pointsHistories()
    {
        return $this->hasMany(PointsHistory::class, 'phone', 'phone');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }
}
