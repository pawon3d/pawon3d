<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;

class Transaction extends Model
{
    use \Spatie\Activitylog\Traits\LogsActivity;

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'transactions';

    protected $guarded = [
        'id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function production()
    {
        return $this->hasOne(Production::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function refund()
    {
        return $this->hasOne(Refund::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('transactions')
            ->logAll()
            ->logOnlyDirty()
            ->setDescriptionForEvent(function (string $eventName) {
                $nomorTransaksi = $this->invoice_number;

                $terjemahan = [
                    'created' => 'ditambahkan',
                    'updated' => 'diperbarui',
                    'deleted' => 'dihapus',
                    'restored' => 'dipulihkan',
                ];

                return "Pesanan {$nomorTransaksi} {$terjemahan[$eventName]}";
            })
            ->dontSubmitEmptyLogs();
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
                    'pesanan-kotak' => 'OK',
                    'pesanan-reguler' => 'OR',
                    'siap-beli' => 'OS',
                ];

                // Ambil metode dari model, pastikan lowercase kalau perlu
                $method = $model->method ?? 'default';
                $basePrefix = $prefixMap[$method] ?? 'OR'; // fallback ke 'PS' kalau tidak cocok

                $prefix = $basePrefix.'-'.$today;

                // Cari nomor terakhir untuk kombinasi metode + tanggal
                $lastTransaction = DB::table('transactions')
                    ->lockForUpdate()
                    ->where('invoice_number', 'like', $prefix.'-%')
                    ->orderByDesc('invoice_number')
                    ->first();

                $lastNumber = 0;
                if ($lastTransaction) {
                    $lastNumber = (int) substr($lastTransaction->invoice_number, -4);
                }

                $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                $model->invoice_number = $prefix.'-'.$nextNumber;
            });
        });
    }
}
