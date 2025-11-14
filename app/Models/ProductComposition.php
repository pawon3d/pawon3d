<?php

namespace App\Models;

use App\Models\Product;
use App\Models\Material;
use Illuminate\Support\Str;
use App\Models\ProcessedMaterial;
use Illuminate\Database\Eloquent\Model;

class ProductComposition extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'product_compositions';

    protected $guarded = [
        'id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }
}
