<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $items = CartDetailResource::collection($this->details);

        return [

            'id' => $this->id,

            'user_id' => $this->user_id,

            'items' => $items,

            // عدد أنواع المنتجات
            'items_count' => $this->details->count(),

            // مجموع الكميات
            'total_quantity' => $this->details->sum('quantity'),

            // المبلغ الكلي
            'grand_total' => (float) $this->details->sum(function ($item) {
                return $item->price * $item->quantity;
            }),

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,
        ];
    }
}
