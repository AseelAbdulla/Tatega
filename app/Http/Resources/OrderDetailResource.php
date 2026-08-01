<?php

namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,


            /*
            | بيانات المنتج وقت الشراء
            | Snapshot وليس البيانات الحالية من products
            */
            'product' => [

                'id' => $this->product_id,

                'name' =>
                    $this->product_name_snapshot,

            ],



            /*
            | بيانات الوحدة وقت الشراء
            */
            'unit' => [

                'id' => $this->unit_id,

                'name' =>
                    $this->unit_name_snapshot,

            ],



            'quantity' =>
                $this->quantity,



            /*
            | السعر المحفوظ وقت إنشاء الطلب
            */
            'unit_price' =>
                (float) $this->unit_price,



            /*
            | إجمالي سعر هذا العنصر
            */
            'total_price' =>
                (float) $this->total_price,


            'created_at' =>
                $this->created_at,

        ];
    }
}

