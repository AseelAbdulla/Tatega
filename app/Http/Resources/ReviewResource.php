<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(
        Request $request
    ): array {

        return [

            'id' =>
                $this->id,

            'user_id' =>
                $this->user_id,

            'visitor_name' =>
                $this->visitor_name,

            'visitor_email' =>
                $this->visitor_email,

            'rating' =>
                $this->rating,

            'comment' =>
                $this->comment,

            'status' =>
                $this->status,

            'admin_note' =>
                $this->admin_note,

            'product_id' =>
                $this->product_id,

            'created_at' =>
                $this->created_at,

            'updated_at' =>
                $this->updated_at,

            'user' =>
                $this->whenLoaded(
                    'user'
                ),

            'product' =>
                $this->whenLoaded(
                    'product'
                ),
        ];
    }
}