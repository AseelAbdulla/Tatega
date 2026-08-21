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
        $primaryAddress = $this->user->addresses->first();
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
                    'building' => $primaryAddress->building,
                    'full_address' => "{$primaryAddress->city}، حي {$primaryAddress->region} - شارع {$primaryAddress->street}"
                ] : null,
            ],


            'items' => $items,

            // عدد أنواع المنتجات
            'items_count' => $this->details->count(),

            // مجموع الكميات
            'total_quantity' => $this->details->sum('quantity'),

            // التفاصيل الحسابيه المحدثة 
            'subtotal' => $subtotal, //المجموع الفرعي للمنتجات
            // 
            'shipping_fee' => $shippingFee, //رسوم شحن ثابته 1000 ريال

               // المبلغ الكلي
            'grand_total' => $grandTotal,

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,
        ];
    }
}
