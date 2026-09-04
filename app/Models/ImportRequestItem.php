<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportRequestItem extends Model
{
        use HasFactory;

    protected $fillable = [
        'import_request_id',
        'product_id',
        'unit_id',
        'quantity',
        'offered_unit_price',
        'offered_subtotal',
    ];

    public function importRequest()
    {
        return $this->belongsTo(ImportRequest::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(ProductUnit::class);
    }
}
