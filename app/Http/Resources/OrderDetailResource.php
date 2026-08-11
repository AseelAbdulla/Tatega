<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'product' => [
                'id' => $this->product_id,
                'name' => $this->product_name_snapshot,
            ],

            'unit' => [
                'id' => $this->unit_id,
                'name' => $this->unit_name_snapshot,
            ],

            'quantity' => (int) $this->quantity,

            'unit_price' => (float) $this->unit_price,

            'total_price' => (float) $this->total_price,

            'created_at' => $this->created_at,
        ];
    }
}
