<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Hitung extends Model
{
    use LogsActivity;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'hitungs';
    protected $guarded = [
        'id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('hitungs')
            ->logAll()
            ->logOnlyDirty()
            ->setDescriptionForEvent(function (string $eventName) {
                $nomorhitung = $this->hitung_number;

                $terjemahan = [
                    'created' => 'ditambahkan',
                    'updated' => 'diperbarui',
                    'deleted' => 'dihapus',
                    'restored' => 'dipulihkan',
                ];

                return "HC nomor {$nomorhitung} {$terjemahan[$eventName]}";
            })
            ->dontSubmitEmptyLogs();
    }

    public function details()
    {
        return $this->hasMany(HitungDetail::class, 'hitung_id', 'id');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
            DB::transaction(function () use ($model) {
                $today = Carbon::now()->format('ymd'); // YYMMDD
                $prefix = 'HC-' . $today;

                // Ambil produksi terakhir untuk hari ini
                $lastHitung = DB::table('hitungs')
                    ->lockForUpdate()
                    ->where('hitung_number', 'like', $prefix . '-%')
                    ->orderByDesc('hitung_number')
                    ->first();

                // Ambil nomor urutan terakhir di hari ini
                $lastNumber = 0;
                if ($lastHitung) {
                    // Contoh hasil: PS-250522-0010 → ambil "0010"
                    $lastNumber = (int) substr($lastHitung->hitung_number, -4);
                }

                $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                $model->hitung_number = $prefix . '-' . $nextNumber;
            });
        });
    }
}
