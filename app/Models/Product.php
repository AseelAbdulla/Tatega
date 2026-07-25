<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'base_price',
        'has_discount',
        'discount',
        'stock',
        'low_stock_threshold',
        'status',
    ];

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

