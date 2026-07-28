<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'image_path',
        'slogan',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'slogan' => 'array',
    ];
}
