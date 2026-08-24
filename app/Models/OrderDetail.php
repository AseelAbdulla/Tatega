<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetail extends Model
{
    /**
     * الحقول القابلة للتعبئة
     */
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

    /**
     * تحويل أنواع البيانات
     */
    protected $casts = [
        'product_name_snapshot' => 'array',
        'unit_name_snapshot' => 'array',

        'quantity' => 'integer',

        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * =========================================================
     * ORDER
     * =========================================================
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * =========================================================
     * PRODUCT
     * =========================================================
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * =========================================================
     * PRODUCT UNIT
     * =========================================================
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(
            ProductUnit::class,
            'unit_id'
        );
    }
}
