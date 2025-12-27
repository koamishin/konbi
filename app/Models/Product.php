<?php

namespace App\Models;

use App\Models\Traits\BelongsToStoreOrGlobal;
use BinaryCats\Sku\HasSku;
use BinaryCats\Sku\Concerns\SkuOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes, BelongsToStoreOrGlobal, HasSku;

    protected $fillable = [
        'store_id',
        'category_id',
        'brand_id',
        'name',
        'sku',
        'barcode',
        'description',
        'image_url',
        'is_active',
        'requires_age_verification',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'requires_age_verification' => 'boolean',
    ];

    public function skuOptions(): SkuOptions
    {
        return SkuOptions::make()
            ->from(['name'])
            ->target('sku')
            ->using('-')
            ->forceUnique(false); // SKU unique per store usually, but let's allow package default
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->barcode)) {
                $product->barcode = static::generateBarcode();
            }
        });
    }

    protected static function generateBarcode()
    {
        // Generate random 13 digit number
        return (string) random_int(1000000000000, 9999999999999);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}
