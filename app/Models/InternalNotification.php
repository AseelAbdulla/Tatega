<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class InternalNotification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'is_read',
        'sent_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

