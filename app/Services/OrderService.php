<?php

namespace App\Services;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\User;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Address;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreOrderRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;


class OrderService
{
    /**
     * إنشاء طلب من السلة
     */
    public function createOrder(
        User $user,
        array $data,
        ?UploadedFile $receipt = null
    ): Order {


        return DB::transaction(function () use ($user, $data, $receipt) {

                $payment_method = PaymentMethod::from($data['payment_method']);

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


                $address = Address::where('id', $data['address_id'])
                    ->where('user_id', $user->id)
                    ->first();

                if (!$address) {
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
            | رفع صورة الإيصال
            |-------------------------------------------
            */

            $receiptPath = null;

            if (
                $payment_method === PaymentMethod::WALLET && $receipt
            ) {

                $receiptPath = $receipt->store(
                    'payment-receipts',
                    'public'
                );
            }

            /*
            |-------------------------------------------
            | إنشاء الطلب
            |-------------------------------------------
             */

            $order = Order::create([

                'user_id' => $user->id,

                'address_id' => $data['address_id'] ?? null,

                'address_id' => $data['address_id'] ?? null,

                'shipping_country' => $address->country,
                'shipping_city' => $address->city,
                'shipping_region' => $address->region,
                'shipping_street' => $address->street,
                'shipping_building' => $address->building,

                'payment_method' => $payment_method,
                'payment_status' => $payment_method === PaymentMethod::WALLET
                    ? PaymentStatus::PENDING_REVIEW
                    : PaymentStatus::PENDING,
                    'payment_recepit' => $receiptPath,


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
                    $item->product->name,

                    'unit_name_snapshot' =>
                    $item->unit->unit_name,

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
                'address',
                'details.product',
                'details.unit'
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
