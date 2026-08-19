<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
       return [
    'id' => $this->id,
    'image_url' => $this->image_path
        ? asset('storage/' . $this->image_path)
        : null,
    'slogan' => $this->slogan,
    'sort_order' => $this->sort_order,
    'status' => $this->status,
];
    }
}