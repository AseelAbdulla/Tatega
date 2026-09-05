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

    // 👈 جلب أحدث عنوان يخص هذا المستخدم تحديداً من المجموعة المحملة
    $primaryAddress = $this->user?->addresses()->latest()->first();

    $subtotal = (float) $this->details->sum(function ($item) {
        return $item->price * $item->quantity;
    });

    $shippingFee = ($this->details && $this->details->count() > 0) ? 1000.00 : 0.00;
    $grandTotal = $subtotal + $shippingFee;

    return [
        'id' => $this->id,

        'user' => [
            'id' => $this->user->id,
            'name' => $this->user->name,
            'email' => $this->user->email,
            'phone' => $this->user->phone,
            'address' => $primaryAddress ? [
                'id' => $primaryAddress->id,
                'city' => $primaryAddress->city,
                'region' => $primaryAddress->region,
                'street' => $primaryAddress->street,
                'full_address' => "{$primaryAddress->country},{$primaryAddress->city}، {$primaryAddress->region} - {$primaryAddress->street}"
            ] : null,
        ],

        'items' => $items,
        'items_count' => $this->details->count(),
        'total_quantity' => $this->details->sum('quantity'),
        'subtotal' => $subtotal,
        'shipping_fee' => $shippingFee,
        'grand_total' => $grandTotal,
        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,
    ];
}


}

