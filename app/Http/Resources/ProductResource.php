<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class ProductResource extends JsonResource
{

    public function toArray(Request $request): array
    {

        return [

            'id' => $this->id,


            'name' => [

                'ar' => $this->name['ar'] ?? null,

                'en' => $this->name['en'] ?? null,

            ],


            'slug' => $this->slug,


            'sku' => $this->sku,


            'price' => $this->base_price,


            'discount_price' => $this->discount_price,


            'has_discount' => $this->has_discount,


            'stock' => $this->stock,


            'low_stock_threshold' =>
                $this->low_stock_threshold,


            'status' => $this->status,



            'category' => $this->whenLoaded(
                'category',
                function () {

                    return [

                        'id' => $this->category->id,

                        'name' => $this->category->name,

                    ];

                }
            ),



            'created_at' => $this->created_at,

        ];

    }

}