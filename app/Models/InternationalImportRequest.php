<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InternationalImportRequest extends Model
{
    use HasFactory;

    protected $table = 'international_import_requests';

    protected $fillable = [
        'user_id',
        'request_number',
        'title',
        'country',
        'price',
        'description',
        'document_path',
        'status',
        'admin_note',
        'rejection_reason',
        'tracking_number',
        'estimated_delivery',
        'delivered_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'estimated_delivery' => 'date',
        'delivered_at' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | User
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }
}
