<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;


class Order extends Model
{
    // تم تصحيح اسماء الحقول بما يتناسب مع الجداول
    protected $fillable = [
        'user_id',
        'address_id',
        'status',
        'subtotal',
        'discount',
        'tax',
        'total_price',
        'rejection_reason',
        'order_type',
        'updated_date',
        'customer_name',
        'customer_phone',
        'customer_email',
        'notes',
    ];

    protected $casts = [
        'payment_method' => PaymentMethod::class,
        'payment_status' => PaymentStatus::class,
        'status' => OrderStatus::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }
}

