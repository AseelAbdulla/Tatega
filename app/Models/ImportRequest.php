<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportRequest extends Model
{
       use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'currency',
        'phone',
        'address_details',
        'notes',
        'admin_notes',
        'rejection_reason',
        'offered_shipping_fee',
        'offered_items_total',
        'offered_grand_total',
        'shipping_method',
        'offer_expires_at',
    ];

    protected $casts = [
        'offer_expires_at' => 'datetime',
        'offered_shipping_fee' => 'float',
        'offered_items_total' => 'float',
        'offered_grand_total' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(ImportRequestItem::class);
    }
}
