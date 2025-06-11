<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Production extends Model
{
    use LogsActivity;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'productions';
    protected $guarded = [
        'id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('productions')
            ->logAll()
            ->logOnlyDirty()
            ->setDescriptionForEvent(function (string $eventName) {
                $nomorProduksi = $this->production_number;

                $terjemahan = [
                    'created' => 'ditambahkan',
                    'updated' => 'diperbarui',
                    'deleted' => 'dihapus',
                    'restored' => 'dipulihkan',
                ];

                return "Produksi {$nomorProduksi} {$terjemahan[$eventName]}";
            })
            ->dontSubmitEmptyLogs();
    }

    public function details()
    {
        return $this->hasMany(ProductionDetail::class);
    }
    public function workers()
    {
        return $this->hasMany(ProductionWorker::class);
    }
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
            DB::transaction(function () use ($model) {
                $today = Carbon::now()->format('ymd'); // YYMMDD

                // Tentukan prefix berdasarkan metode
                $prefixMap = [
                    'pesanan-kotak' => 'PK',
                    'pesanan-reguler' => 'PR',
                    'siap-beli' => 'PS',
                ];

                // Ambil metode dari model, pastikan lowercase kalau perlu
                $method = $model->method ?? 'default';
                $basePrefix = $prefixMap[$method] ?? 'PS'; // fallback ke 'PS' kalau tidak cocok

                $prefix = $basePrefix . '-' . $today;

                // Cari nomor terakhir untuk kombinasi metode + tanggal
                $lastProduction = DB::table('productions')
                    ->lockForUpdate()
                    ->where('production_number', 'like', $prefix . '-%')
                    ->orderByDesc('production_number')
                    ->first();

                $lastNumber = 0;
                if ($lastProduction) {
                    $lastNumber = (int) substr($lastProduction->production_number, -4);
                }

                $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                $model->production_number = $prefix . '-' . $nextNumber;
            });
        });
    }
}
