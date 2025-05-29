<?php

namespace App\Models;

use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
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
            // Generate expense number with prefix 'BB' and 4 digit incrementing number
            $lastExpense = self::latest('created_at')->first();
            $lastNumber = $lastExpense ? (int) substr($lastExpense->expense_number, 2) : 0;
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

            $model->expense_number = 'BB' . $nextNumber;
        });
    }
}