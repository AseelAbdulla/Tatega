<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImportRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id'                   => $this->id,
            'status'               => $this->status,
            'currency'             => $this->currency,
            'phone'                => $this->phone,
            'address_details'      => $this->address_details,
            'notes'                => $this->notes,
            'rejection_reason'     => $this->rejection_reason,
            'shipping_method'      => $this->shipping_method,
            'offered_shipping_fee' => $this->offered_shipping_fee,
            'offered_items_total'  => $this->offered_items_total,
            'offered_grand_total'  => $this->offered_grand_total,
            'offer_expires_at'     => $this->offer_expires_at?->toIso8601String(),
            'user'                 => [
                'id'    => $this->user?->id,
                'name'  => $this->user?->name,
                'email' => $this->user?->email,
            ],
            'items'                => $this->items->map(function ($item) {
                $firstImage = $item->product?->images?->first()?->image_path;
                $imageUrl = $firstImage ? asset('storage/' . ltrim($firstImage, '/')) : null;
                return [
                    'id'                 => $item->id,
                    'product_image'      => $imageUrl,
                    'product_id'         => $item->product_id,
                    'product_name'       => $item->product?->name,
                    'unit_id'            => $item->unit_id,
                    'unit_name'          => $item->unit?->name,
                    'quantity'           => $item->quantity,
                    'offered_unit_price' => $item->offered_unit_price,
                    'offered_subtotal'   => $item->offered_subtotal,
                ];
            }),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
