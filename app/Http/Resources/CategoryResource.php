<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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


            'image' => $this->image
                ? asset('storage/'.$this->image)
                : null,


            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,

        ];

    }

}