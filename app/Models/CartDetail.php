<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class CartDetail extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
        'unit_id',
        'quantity',
        'price',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(ProductUnit::class, 'unit_id');
    }
}

