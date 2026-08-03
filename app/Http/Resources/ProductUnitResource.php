<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class ProductUnitResource extends JsonResource
{

    public function toArray(Request $request): array
    {

        return [

            'id' => $this->id,


            'unit_name' => [

                'ar' => $this->unit_name['ar'] ?? null,

                'en' => $this->unit_name['en'] ?? null,

            ],


            'price' => $this->price,


            'stock' => $this->stock,


            'product' => $this->whenLoaded(
                'product',
                function(){

                    return [

                        'id' => $this->product->id,

                        'name' => $this->product->name,

                    ];

                }
            ),


            'created_at' => $this->created_at,

        ];

    }

}