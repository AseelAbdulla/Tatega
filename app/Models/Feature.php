<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Feature extends Model
{
    protected $fillable = [
        'icon',
        'title',
        'description',
        'sort_order',
        'status',
    ];

      protected $casts = [
        'title' => 'array',
        'description' => 'array', 
    ];
}

