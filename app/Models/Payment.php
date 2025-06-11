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
        });
    }
}
