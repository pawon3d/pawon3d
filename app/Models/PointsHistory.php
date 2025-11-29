<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PointsHistory extends Model
{
    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'points_histories';

    protected $guarded = [
        'id',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
            DB::transaction(function () use ($model) {
                $today = Carbon::now()->format('ymd'); // YYMMDD

                // Tentukan prefix berdasarkan aksi
                $prefixMap = [
                    'Story Instagram' => 'SM',
                    'Rating Gmaps' => 'RG',
                    'Tukar Poin' => 'TP',
                    'Pesanan Reguler' => 'OR',
                    'Pesanan Kotak' => 'OK',
                    'Siap Saji' => 'OS',
                ];

                // Ambil aksi dari model, pastikan lowercase kalau perlu
                $action = $model->action ?? 'Story Instagram';
                $basePrefix = $prefixMap[$action] ?? 'SM'; // fallback ke 'SM' kalau tidak cocok

                $prefix = $basePrefix.'-'.$today;

                // Cari nomor terakhir untuk kombinasi metode + tanggal
                $lastAction = DB::table('points_histories')
                    ->lockForUpdate()
                    ->where('action_id', 'like', $prefix.'-%')
                    ->orderByDesc('action_id')
                    ->first();

                $lastNumber = 0;
                if ($lastAction) {
                    $lastNumber = (int) substr($lastAction->action_id, -4);
                }

                $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                $model->action_id = $prefix.'-'.$nextNumber;
            });
        });
    }
}
