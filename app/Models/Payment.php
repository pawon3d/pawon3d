<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'payments';

    protected $guarded = [
        'id',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function channel()
    {
        return $this->belongsTo(PaymentChannel::class, 'payment_channel_id');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = \Illuminate\Support\Str::uuid();

            // Generate receipt number if not provided
            if (empty($model->receipt_number)) {
                $date = now();
                $datePrefix = $date->format('ymd'); // YYMMDD format

                // Get the last receipt number for today
                $lastReceipt = Payment::where('receipt_number', 'like', $datePrefix.'-%')
                    ->orderBy('receipt_number', 'desc')
                    ->first();

                if ($lastReceipt) {
                    $lastNumber = (int) substr($lastReceipt->receipt_number, -4);
                    $newNumber = $lastNumber + 1;
                } else {
                    $newNumber = 1;
                }

                $model->receipt_number = $datePrefix.'-'.str_pad($newNumber, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
