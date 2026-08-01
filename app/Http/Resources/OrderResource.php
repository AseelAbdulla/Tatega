<?php

namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,


            'status' => $this->status,

            'order_type' => $this->order_type,


            /*
             | بيانات العميل وقت إنشاء الطلب
             */
            'customer' => [

                'name' =>
                    $this->customer_name,

                'phone' =>
                    $this->customer_phone,

                'email' =>
                    $this->customer_email,

            ],



            /*
             | العنوان
             */
            'address' => $this->whenLoaded(
                'address',
                function () {

                    return [

                        'id' =>
                            $this->address->id,

                        'title' =>
                            $this->address->title,

                        'address' =>
                            $this->address->address,

                    ];

                }
            ),




            /*
             | تفاصيل المنتجات
             */
            'items' =>
                OrderDetailResource::collection(
                    $this->whenLoaded('details')
                ),




            /*
             | المبالغ
             */
            'pricing' => [

                'subtotal' =>
                    (float) $this->subtotal,


                'discount' =>
                    (float) $this->discount,


                'tax' =>
                    (float) $this->tax,


                'total' =>
                    (float) $this->total_price,

            ],




            'notes' =>
                $this->notes,


            'rejection_reason' =>
                $this->rejection_reason,



            'created_at' =>
                $this->created_at,

            'updated_at' =>
                $this->updated_at,

        ];
    }
}
