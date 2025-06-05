<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionWorker extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'production_workers';
    protected $guarded = [
        'id',
    ];

    public function production()
    {
        return $this->belongsTo(Production::class);
    }

    public function worker()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) \Illuminate\Support\Str::uuid();
        });
    }
}
