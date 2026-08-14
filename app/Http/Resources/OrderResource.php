<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'status' => $this->status?->value,

            'order_type' => $this->order_type,

            'customer' => [
                'name' => $this->customer_name,
                'phone' => $this->customer_phone,
                'email' => $this->customer_email,
            ],

            'address' => $this->when(
                $this->relationLoaded('address'),
                function () {
                    if (!$this->address) {
                        return null;
                    }

                    return [
                        'id' => $this->address->id,
                        'country' => $this->address->country,
                        'city' => $this->address->city,
                        'region' => $this->address->region,
                        'street' => $this->address->street,
                        'building' => $this->address->building,
                        'notes' => $this->address->notes,
                    ];
                }
            ),

            'items' => OrderDetailResource::collection(
                $this->whenLoaded('details')
            ),

            'payment' => [
                'method' => $this->payment_method?->value,
                'status' => $this->payment_status?->value,
                'receipt' => $this->payment_receipt,
            ],

            'pricing' => [
                'subtotal' => (float) $this->subtotal,
                'discount' => (float) $this->discount,
                'tax' => (float) $this->tax,
                'total' => (float) $this->total_price,
                'shipping_fee' => (float) $this->shipping_fee,
            ],

            'notes' => $this->notes,

            'rejection_reason' =>
                $this->rejection_reason,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
