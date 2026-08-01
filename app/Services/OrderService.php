<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Address;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreOrderRequest;

class OrderService
{
    /**
     * إنشاء طلب من السلة
     */
    public function createOrder(
        User $user,
        array $data
    ): Order {

        return DB::transaction(function () use ($user, $data) {


            /*
             |-------------------------------------------
             | جلب سلة المستخدم
             |-------------------------------------------
             */

            $cart = Cart::where('user_id', $user->id)
                ->with([
                    'details.product',
                    'details.unit'
                ])
                ->first();



            if (!$cart || $cart->details->isEmpty()) {

                abort(
                    422,
                    'السلة فارغة.'
                );
            }



            /*
             |-------------------------------------------
             | التحقق من العنوان
             |-------------------------------------------
             */

            if (!empty($data['address_id'])) {


                $addressExists = Address::where('id', $data['address_id'])
                    ->where('user_id', $user->id)
                    ->exists();


                if (!$addressExists) {

                    abort(
                        403,
                        'هذا العنوان لا يخص المستخدم.'
                    );
                }
            }



            /*
             |-------------------------------------------
             | حساب المبالغ
             |-------------------------------------------
             */

            $subtotal = 0;


            foreach ($cart->details as $item) {

                $subtotal +=
                    $item->price * $item->quantity;
            }


            $discount = 0;

            $tax = 0;


            $total =
                $subtotal
                - $discount
                + $tax;



            /*
             |-------------------------------------------
             | إنشاء الطلب
             |-------------------------------------------
             */

            $order = Order::create([

                'user_id' => $user->id,

                'address_id' =>
                $data['address_id'] ?? null,


                'status' => 'pending',

                'order_type' => 'normal',


                'customer_name' =>
                $data['customer_name'],


                'customer_phone' =>
                $data['customer_phone'],


                'customer_email' =>
                $data['customer_email'],


                'notes' =>
                $data['notes'] ?? null,


                'subtotal' =>
                $subtotal,


                'discount' =>
                $discount,


                'tax' =>
                $tax,


                'total_price' =>
                $total,

            ]);




            /*
             |-------------------------------------------
             | إنشاء تفاصيل الطلب
             | حفظ Snapshot
             |-------------------------------------------
             */

            foreach ($cart->details as $item) {


                $order->details()->create([


                    'product_id' =>
                    $item->product_id,


                    'unit_id' =>
                    $item->unit_id,



                    /*
                     * نحفظ الاسم وقت الشراء
                     * حتى لو تغير لاحقًا
                     */

                    'product_name_snapshot' =>
                    json_encode(
                        $item->product->name
                    ),



                    'unit_name_snapshot' =>
                    json_encode(
                        $item->unit->unit_name
                    ),



                    'quantity' =>
                    $item->quantity,



                    /*
                     * السعر وقت الشراء
                     */

                    'unit_price' =>
                    $item->price,



                    'total_price' =>
                    $item->price *
                        $item->quantity,


                ]);
            }



            /*
             |-------------------------------------------
             | تفريغ السلة
             |-------------------------------------------
             */

            $cart->details()->delete();



            return $order->load([
                'details',
                'address'
            ]);
        });
    }



    /**
     * طلبات المستخدم
     */
    public function getUserOrders(User $user)
    {
        return Order::where('user_id', $user->id)
            ->with('details')
            ->latest()
            ->get();
    }



    /**
     * عرض طلب معين
     */
    public function getOrder(
        User $user,
        Order $order
    ) {

        if ($order->user_id !== $user->id) {

            abort(403);
        }


        return $order->load([
            'details',
            'address'
        ]);
    }
}
