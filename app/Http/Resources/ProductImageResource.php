<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class ProductImageResource extends JsonResource
{

    public function toArray(Request $request): array
    {

        return [

            'id' => $this->id,


            'image' => $this->image_path
                ? asset('storage/'.$this->image_path)
                : null,


            'is_main' => $this->is_main,


            'sort_order' => $this->sort_order,


            'product' => $this->whenLoaded(
                'product',
                function () {

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