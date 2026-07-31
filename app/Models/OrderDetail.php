<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class OrderDetail extends Model
{
    //  // تم تصحيح اسماء الحقول بما يتناسب مع الجداول
    protected $fillable = [
        'order_id',
        'product_id',
        'unit_id',
        'product_name_snapshot',
        'unit_name_snapshot',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'product_name_snapshot' => 'array',
        'unit_name_snapshot' => 'array',
    ];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(ProductUnit::class);
    }
}
