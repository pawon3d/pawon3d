<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class ProductionDetail extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'production_details';
    protected $guarded = [
        'id',
    ];

    public function production()
    {
        return $this->belongsTo(Production::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }
}
