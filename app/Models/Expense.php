<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\LogsActivity;

class Expense extends Model
{
    use LogsActivity;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'expenses';
    protected $guarded = [
        'id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('expenses')
            ->logAll()
            ->logOnlyDirty()
            ->setDescriptionForEvent(function (string $eventName) {
                $nomorBelanja = $this->expense_number;

                $terjemahan = [
                    'created' => 'ditambahkan',
                    'updated' => 'diperbarui',
                    'deleted' => 'dihapus',
                    'restored' => 'dipulihkan',
                ];

                return "Belanja nomor {$nomorBelanja} {$terjemahan[$eventName]}";
            })
            ->dontSubmitEmptyLogs();
    }

    public function expenseDetails()
    {
        return $this->hasMany(ExpenseDetail::class, 'expense_id', 'id');
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }


    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
            DB::transaction(function () use ($model) {
                $today = Carbon::now()->format('ymd'); // YYMMDD
                $prefix = 'BP-' . $today;

                // Ambil produksi terakhir untuk hari ini
                $lastExpense = DB::table('expenses')
                    ->lockForUpdate()
                    ->where('expense_number', 'like', $prefix . '-%')
                    ->orderByDesc('expense_number')
                    ->first();

                // Ambil nomor urutan terakhir di hari ini
                $lastNumber = 0;
                if ($lastExpense) {
                    // Contoh hasil: PS-250522-0010 → ambil "0010"
                    $lastNumber = (int) substr($lastExpense->expense_number, -4);
                }

                $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                $model->expense_number = $prefix . '-' . $nextNumber;
            });
        });
    }
}
