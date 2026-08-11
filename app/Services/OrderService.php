<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

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
        return DB::transaction(function () use (
            $user,
            $data,
            $receipt
        ) {

            $paymentMethod = PaymentMethod::from(
                $data['payment_method']
            );

            /*
            |--------------------------------------------------------------------------
            | Cart
            |--------------------------------------------------------------------------
            */

            $cart = Cart::where('user_id', $user->id)
                ->with([
                    'details.product',
                    'details.unit',
                ])
                ->first();

            if (!$cart || $cart->details->isEmpty()) {
                abort(422, 'السلة فارغة.');
            }

            /*
            |--------------------------------------------------------------------------
            | Address
            |--------------------------------------------------------------------------
            */

            $address = Address::where(
                'id',
                $data['address_id']
            )
                ->where(
                    'user_id',
                    $user->id
                )
                ->first();

            if (!$address) {
                abort(
                    403,
                    'هذا العنوان لا يخص المستخدم.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Calculate subtotal
            |--------------------------------------------------------------------------
            */

            $subtotal = 0;

            foreach ($cart->details as $item) {

                if (!$item->product) {
                    abort(
                        422,
                        'أحد المنتجات غير موجود.'
                    );
                }

                if (!$item->unit) {
                    abort(
                        422,
                        'وحدة أحد المنتجات غير موجودة.'
                    );
                }

                if (
                    $item->unit->product_id
                    !== $item->product_id
                ) {
                    abort(
                        422,
                        'وحدة المنتج غير صحيحة.'
                    );
                }

                if (
                    $item->quantity
                    > $item->unit->stock
                ) {
                    abort(
                        422,
                        'الكمية المطلوبة غير متوفرة في المخزون.'
                    );
                }

                $subtotal +=
                    $item->price
                    * $item->quantity;
            }

            /*
            |--------------------------------------------------------------------------
            | Pricing
            |--------------------------------------------------------------------------
            */

            $discount = 0;

            $tax = 0;

            $total =
                $subtotal
                - $discount
                + $tax;

            /*
            |--------------------------------------------------------------------------
            | Payment Receipt
            |--------------------------------------------------------------------------
            */

            $receiptPath = null;

            if (
                $paymentMethod
                === PaymentMethod::WALLET
            ) {

                if (!$receipt) {
                    abort(
                        422,
                        'يجب إرفاق إيصال الدفع عند اختيار المحفظة.'
                    );
                }

                $receiptPath = $receipt->store(
                    'payment-receipts',
                    'public'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Payment Status
            |--------------------------------------------------------------------------
            */

            $paymentStatus =
                $paymentMethod
                === PaymentMethod::WALLET

                    ? PaymentStatus::PENDING_REVIEW

                    : PaymentStatus::PENDING;

            /*
            |--------------------------------------------------------------------------
            | Create Order
            |--------------------------------------------------------------------------
            */

            $order = Order::create([

                'user_id' => $user->id,

                'address_id' => $address->id,

                'order_type' => 'normal',

                'status' => OrderStatus::PENDING,

                /*
                |--------------------------------------------------------------------------
                | Customer Snapshot
                |--------------------------------------------------------------------------
                */

                'customer_name' =>
                    $data['customer_name'],
 
                'customer_phone' =>
                    $data['customer_phone'],
 
                'customer_email' =>
                    $data['customer_email'],

                /*
                |--------------------------------------------------------------------------
                | Shipping Snapshot
                |--------------------------------------------------------------------------
                */

                'shipping_country' =>
                    $address->country,

                'shipping_city' =>
                    $address->city,

                'shipping_region' =>
                    $address->region,

                'shipping_street' =>
                    $address->street,

                'shipping_building' =>
                    $address->building,

                /*
                |--------------------------------------------------------------------------
                | Payment
                |--------------------------------------------------------------------------
                */

                'payment_method' =>
                    $paymentMethod,

                'payment_status' =>
                    $paymentStatus,

                'payment_receipt' =>
                    $receiptPath,

                /*
                |--------------------------------------------------------------------------
                | Notes
                |--------------------------------------------------------------------------
                */

                'notes' =>
                    $data['notes'] ?? null,

                /*
                |--------------------------------------------------------------------------
                | Pricing
                |--------------------------------------------------------------------------
                */

                'subtotal' => $subtotal,

                'discount' => $discount,

                'tax' => $tax,

                'total_price' => $total,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Order Details
            |--------------------------------------------------------------------------
            */

            foreach ($cart->details as $item) {

                $order->details()->create([

                    'product_id' =>
                        $item->product_id,

                    'unit_id' =>
                        $item->unit_id,

                    'product_name_snapshot' =>
                        $item->product->name,

                    'unit_name_snapshot' =>
                        $item->unit->unit_name,

                    'quantity' =>
                        $item->quantity,
 
                    'unit_price' =>
                        $item->price,

                    'total_price' =>
                        $item->price
                        * $item->quantity,
                ]);

                /*
                |--------------------------------------------------------------------------
                | Reduce Stock
                |--------------------------------------------------------------------------
                */

                $item->unit->decrement(
                    'stock',
                    $item->quantity
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Clear Cart
            |--------------------------------------------------------------------------
            */

            $cart->details()->delete();

            /*
            |--------------------------------------------------------------------------
            | Return Order
            |--------------------------------------------------------------------------
            */

            return $order->load([
                'details',
                'address',
                'details.product',
                'details.unit',
            ]);
        });
    }

    /**
     * طلبات المستخدم
     */
    public function getUserOrders(User $user)
    {
        return Order::where(
            'user_id',
            $user->id
        )
            ->with([
                'details',
                'address',
            ])
            ->latest()
            ->get();
    }

    /**
     * عرض طلب محدد
     */
    public function getOrder(
        User $user,
        Order $order
    ): Order {
        $this->verifyOwnership(
            $user,
            $order
        );

        return $order->load([
            'details',
            'address',
        ]);
    }

    /**
     * إلغاء الطلب
     */
    public function cancelOrder(
        User $user,
        Order $order
    ): Order {
        $this->verifyOwnership(
            $user,
            $order
        );

        if (
            !in_array(
                $order->status,
                [
                    OrderStatus::PENDING,
                    OrderStatus::ACCEPTED,
                ],
                true
            )
        ) {
            abort(
                422,
                'لا يمكن إلغاء هذا الطلب في حالته الحالية.'
            );
        }

        $order->update([
            'status' => OrderStatus::CANCELLED,
        ]);

        return $order->fresh([
            'details',
            'address',
        ]);
    }

    /**
     * التأكد من ملكية الطلب
     */
    private function verifyOwnership(
        User $user,
        Order $order
    ): void {
        if ($order->user_id !== $user->id) {
            abort(
                403,
                'غير مصرح لك بهذا الطلب.'
            );
        }
    }
}
