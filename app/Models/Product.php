<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends Model
{
    use LogsActivity;

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'products';

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'method' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('products')
            ->logOnly(['name', 'category_id', 'price', 'stock', 'is_ready', 'product_image'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(function (string $eventName) {
                $namaProduk = $this->name;

                $terjemahan = [
                    'created' => 'ditambahkan',
                    'updated' => 'diperbarui',
                    'deleted' => 'dihapus',
                    'restored' => 'dipulihkan',
                ];

                return "Produk {$namaProduk} {$terjemahan[$eventName]}";
            })
            ->dontSubmitEmptyLogs();
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function product_categories()
    {
        return $this->hasMany(ProductCategory::class);
    }

    public function productions()
    {
        return $this->hasMany(Production::class);
    }

    public function product_compositions()
    {
        return $this->hasMany(ProductComposition::class);
    }

    public function transactions()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function other_costs()
    {
        return $this->hasMany(OtherCost::class);
    }

    /**
     * Get available stock for this product
     * For recipe products: return stock field
     * For non-recipe products: get from material batches (excluding expired)
     */
    public function getAvailableStock(): int
    {
        // Jika produk memiliki resep, gunakan stok produk
        if ($this->is_recipe) {
            return $this->stock ?? 0;
        }

        // Jika produk tidak memiliki resep (ready-to-sell), ambil dari bahan baku
        // Load product compositions to get the material
        $this->load('product_compositions.material.material_details.unit');
        
        $composition = $this->product_compositions->first();
        if (!$composition || !$composition->material) {
            return 0;
        }

        $material = $composition->material;
        
        // Get main unit for the material
        $mainDetail = $material->material_details->firstWhere('is_main', true);
        if (!$mainDetail || !$mainDetail->unit) {
            return 0;
        }

        // Get total quantity in main unit (excludes expired batches)
        $totalQuantity = $material->getTotalQuantityInUnit($mainDetail->unit);
        
        return (int) floor($totalQuantity);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }
}
