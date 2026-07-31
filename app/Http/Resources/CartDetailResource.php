<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'product' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'sku' => $this->product->sku,
            ],

            'unit' => [
                'id' => $this->unit->id,
                'name' => $this->unit->unit_name,
            ],

            'price' => (float) $this->price,

            'quantity' => $this->quantity,

            // إجمالي هذا العنصر
            'subtotal' => (float) ($this->price * $this->quantity),

            // أول صورة للمنتج (إن وجدت)
            'image' => $this->product->images->first()?->image_path,

            'created_at' => $this->created_at,
        ];
    }
}
