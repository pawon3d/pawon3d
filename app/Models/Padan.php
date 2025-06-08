<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Padan extends Model
{
    use LogsActivity;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'padans';
    protected $guarded = [
        'id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('padans')
            ->logAll()
            ->logOnlyDirty()
            ->setDescriptionForEvent(function (string $eventName) {
                $nomorPadan = $this->padan_number;

                $terjemahan = [
                    'created' => 'ditambahkan',
                    'updated' => 'diperbarui',
                    'deleted' => 'dihapus',
                    'restored' => 'dipulihkan',
                ];

                return "HP nomor {$nomorPadan} {$terjemahan[$eventName]}";
            })
            ->dontSubmitEmptyLogs();
    }

    public function details()
    {
        return $this->hasMany(PadanDetail::class, 'padan_id', 'id');
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
                $lastPadan = DB::table('padans')
                    ->lockForUpdate()
                    ->where('padan_number', 'like', $prefix . '-%')
                    ->orderByDesc('padan_number')
                    ->first();

                // Ambil nomor urutan terakhir di hari ini
                $lastNumber = 0;
                if ($lastPadan) {
                    // Contoh hasil: PS-250522-0010 → ambil "0010"
                    $lastNumber = (int) substr($lastPadan->padan_number, -4);
                }

                $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                $model->padan_number = $prefix . '-' . $nextNumber;
            });
        });
    }
}
