<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentMethod extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'cardholder_name',
        'last_four',
        'expiry_month',
        'expiry_year',
        'is_default',
        'status',
    ];

    protected $casts = [
        'expiry_month' => 'integer',
        'expiry_year' => 'integer',
        'is_default' => 'boolean',
    ];

    /**
     * صاحب طريقة الدفع
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
