<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Order extends Model
{
    protected $fillable = [
        'user_id',
        'address_id',
        'status',
        'subtotal',
        'discount',
        'tax',
        'total_price',
        'rejection_reason',
        'order_date',
        'updated_date',
        'customer_name',
        'customer_phone',
        'notes',
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

