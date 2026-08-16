<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use HasTranslations;
    
    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'base_price',
        'has_discount',
        'discount',
        'stock',
        'low_stock_threshold',
        'status',
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
    ];
public $translatable = ['name'];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function units()
    {
        return $this->hasMany(ProductUnit::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
