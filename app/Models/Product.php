<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
   protected $fillable = [
    'category_id',
    'name',
    'description',
    'sku',
    'base_price',
    'has_discount',
    'discount_price',
    'stock',
    'low_stock_threshold',
    'status',
];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'has_discount' => 'boolean',
        'base_price' => 'decimal:2',
        'discount_price' => 'decimal:2',
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