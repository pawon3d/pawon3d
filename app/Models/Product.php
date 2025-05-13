<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Support\Str;
use App\Models\ProductComposition;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'products';

    protected $guarded = [
        'id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function productions()
    {
        return $this->hasMany(Production::class);
    }

    public function product_compositions()
    {
        return $this->hasMany(ProductComposition::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    public function transactions()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }
}