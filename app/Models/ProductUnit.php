<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ProductUnit extends Model
{
    protected $fillable = [
        'product_id',
        'unit_name',
        'price',
        'stock',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

