<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Partner extends Model
{
    protected $fillable = [
        'name',
        'logo',
        'website_url',
        'slogan',
        'sort_order',
        'status',
        'lng',
        'lat',
        
    ];

      protected $casts = [
        'name' => 'array'
    ];
}

