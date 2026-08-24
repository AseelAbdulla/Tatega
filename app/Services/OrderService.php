<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Http\Resources\OrderResource;
use App\Models\Address;
use App\Models\Cart;
use App\Models\InternalNotification;
use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /**
     * =========================================================
     * CREATE ORDER
     * =========================================================
     *
     * إنشاء طلب من سلة العميل
     *
     * السيناريو:
     *
     * العميل
     *   ↓
     * إنشاء الطلب
     *   ↓
     * حفظ الطلب في orders
     *   ↓
     * إرسال إشعار للأدمن
     *
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

            /*
            |--------------------------------------------------------------------------
            | Payment Method
            |--------------------------------------------------------------------------
            */

            $paymentMethod = PaymentMethod::from(
                $data['payment_method']
            );


            /*
            |--------------------------------------------------------------------------
            | Cart
            |--------------------------------------------------------------------------
            */

            $cart = Cart::where(
                'user_id',
                $user->id
            )
                ->with([
                    'details.product',
                    'details.unit',
                ])
                ->first();

            if (!$cart || $cart->details->isEmpty()) {

                throw ValidationException::withMessages([
                    'cart' => ['السلة فارغة.']
                ]);

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

                throw ValidationException::withMessages([
                    'address_id' => [
                        'هذا العنوان لا يخص المستخدم.'
                    ]
                ]);

            }


            /*
            |--------------------------------------------------------------------------
            | Calculate Subtotal
            |--------------------------------------------------------------------------
            */

            $subtotal = 0;

            foreach ($cart->details as $item) {

                if (!$item->product) {

                    throw ValidationException::withMessages([
                        'cart' => [
                            'أحد المنتجات غير موجود.'
                        ]
                    ]);

                }

                if (!$item->unit) {

                    throw ValidationException::withMessages([
                        'cart' => [
                            'وحدة أحد المنتجات غير موجودة.'
                        ]
                    ]);

                }

                if (
                    $item->unit->product_id
                    !== $item->product_id
                ) {

                    throw ValidationException::withMessages([
                        'cart' => [
                            'وحدة المنتج غير صحيحة.'
                        ]
                    ]);

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

            $shipping_fee =
                $data['shipping_fee'] ?? 0;

            $total =
                $subtotal
                - $discount
                + $tax
                + $shipping_fee;


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

                    throw ValidationException::withMessages([
                        'payment_receipt' => [
                            'يجب إرفاق إيصال الدفع عند اختيار المحفظة.'
                        ]
                    ]);

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

                'wallet_number' =>
                    $data['wallet_number'] ?? null,


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

                'subtotal' =>
                    $subtotal,

                'shipping_fee' =>
                    $shipping_fee,

                'discount' =>
                    $discount,

                'tax' =>
                    $tax,

                'total_price' =>
                    $total,
            ]);


            /*
            |--------------------------------------------------------------------------
            | Order Details + Reduce Stock
            |--------------------------------------------------------------------------
            */

            foreach ($cart->details as $item) {

                /*
                |--------------------------------------------------------------------------
                | Lock Product Unit
                |--------------------------------------------------------------------------
                */

                $unit = \App\Models\ProductUnit::where(
                    'id',
                    $item->unit_id
                )
                    ->lockForUpdate()
                    ->first();


                /*
                |--------------------------------------------------------------------------
                | Check Stock
                |--------------------------------------------------------------------------
                */

                if (
                    !$unit
                    || $item->quantity > $unit->stock
                ) {

                    $productName =
                        $item->product->name;

                    if (is_string($productName)) {

                        $decoded =
                            json_decode(
                                $productName,
                                true
                            );

                        if (
                            json_last_error()
                            === JSON_ERROR_NONE
                            && is_array($decoded)
                        ) {

                            $productName =
                                $decoded['ar']
                                ?? $decoded['en']
                                ?? reset($decoded);

                        }

                    } elseif (is_array($productName)) {

                        $productName =
                            $productName['ar']
                            ?? $productName['en']
                            ?? reset($productName);

                    }


                    throw ValidationException::withMessages([
                        'cart' => [
                            "الكمية المطلوبة غير متوفرة في المخزون للمنتج: {$productName}"
                        ]
                    ]);

                }


                /*
                |--------------------------------------------------------------------------
                | Create Order Detail
                |--------------------------------------------------------------------------
                */

                $order->details()->create([

                    'product_id' =>
                        $item->product_id,

                    'unit_id' =>
                        $item->unit_id,

                    'product_name_snapshot' =>
                        $item->product->name,

                    'unit_name_snapshot' =>
                        $unit->unit_name,

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

                $unit->decrement(
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
            | Send Notification To Admins
            |--------------------------------------------------------------------------
            |
            | العميل أنشأ طلبًا جديدًا
            | ↓
            | يتم إرسال إشعار إلى الأدمن
            |
            */

            $admins = User::whereHas(
                'roles',
                function ($query) {
                    $query->where('name', 'admin');
                }
            )->get();


            foreach ($admins as $admin) {

                InternalNotification::create([

                    'user_id' =>
                        $admin->id,

                    'title' => [
                        'ar' => 'طلب جديد',
                        'en' => 'New Order',
                    ],

                    'message' => [
                        'ar' =>
                            "تم إنشاء طلب جديد رقم #{$order->id} من العميل {$user->name}.",

                        'en' =>
                            "A new order #{$order->id} was created by {$user->name}.",
                    ],

                    'type' =>
                        'order',

                    'is_read' =>
                        false,

                    'sent_at' =>
                        now(),
                ]);
            }


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


    /*
    |--------------------------------------------------------------------------
    | DASHBOARD STATISTICS
    |--------------------------------------------------------------------------
    */

    public function getDashboardStats(): array
    {

        /*
        |--------------------------------------------------------------------------
        | Pending Orders
        |--------------------------------------------------------------------------
        */

        $pendingCount =
            Order::where(
                'status',
                OrderStatus::PENDING->value
            )
                ->orWhere(
                    'status',
                    'pending'
                )
                ->count();


        /*
        |--------------------------------------------------------------------------
        | Total Sales
        |--------------------------------------------------------------------------
        */

        $totalSales =
            Order::whereIn(
                'status',
                [
                    OrderStatus::DELIVERED->value,
                    OrderStatus::ACCEPTED->value,
                    OrderStatus::SHIPPED->value,

                    'delivered',
                    'accepted',
                    'completed',
                ]
            )
                ->sum('total_price');


        /*
        |--------------------------------------------------------------------------
        | Recent Orders
        |--------------------------------------------------------------------------
        */

        $recentOrders =
            OrderResource::collection(
                Order::latest()
                    ->take(5)
                    ->get()
            );


        return [

            'stats' => [

                'pending_orders' =>
                    (int) $pendingCount,

                'total_sales' =>
                    (float) $totalSales,

            ],

            'recentOrders' =>
                $recentOrders,
        ];

    }


    /*
    |--------------------------------------------------------------------------
    | CUSTOMER ORDERS
    |--------------------------------------------------------------------------
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
                'details.product',
                'details.unit',
            ])
            ->latest()
            ->get();

    }


    /*
    |--------------------------------------------------------------------------
    | GET SINGLE ORDER
    |--------------------------------------------------------------------------
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
            'details.product',
            'details.unit',
        ]);

    }


    /*
    |--------------------------------------------------------------------------
    | VERIFY ORDER OWNERSHIP
    |--------------------------------------------------------------------------
    */

    public function verifyOwnership(
        User $user,
        Order $order
    ): void {

        if (
            $order->user_id
            !== $user->id
        ) {

            throw new AuthorizationException(
                'غير مصرح لك بالوصول لهذا الطلب.'
            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE ORDER STATUS
    |--------------------------------------------------------------------------
    |
    | الأدمن يغير حالة الطلب
    | ↓
    | يتم تحديث الطلب
    | ↓
    | يتم إرسال إشعار للعميل
    |
    */

    public function updateOrderStatus(
        Order $order,
        string $status
    ): Order {

        /*
        |--------------------------------------------------------------------------
        | Update Status
        |--------------------------------------------------------------------------
        */

        $order->update([
            'status' => $status,
        ]);


        /*
        |--------------------------------------------------------------------------
        | Send Notification To Customer
        |--------------------------------------------------------------------------
        */

        $statusText = $this->getOrderStatusText(
            $status
        );


        InternalNotification::create([

            'user_id' =>
                $order->user_id,

            'title' => [
                'ar' => 'تحديث حالة الطلب',
                'en' => 'Order Status Updated',
            ],

            'message' => [
                'ar' =>
                    "تم تحديث حالة طلبك رقم #{$order->id} إلى: {$statusText['ar']}.",

                'en' =>
                    "The status of your order #{$order->id} has been updated to: {$statusText['en']}.",
            ],

            'type' =>
                'order',

            'is_read' =>
                false,

            'sent_at' =>
                now(),
        ]);


        return $order->fresh([
            'user',
            'details',
            'address',
            'details.product',
            'details.unit',
        ]);

    }


    /*
    |--------------------------------------------------------------------------
    | ORDER STATUS TEXT
    |--------------------------------------------------------------------------
    */

    private function getOrderStatusText(
        string $status
    ): array {

        return match ($status) {

            'pending' => [
                'ar' => 'قيد الانتظار',
                'en' => 'Pending',
            ],

            'accepted' => [
                'ar' => 'تمت الموافقة',
                'en' => 'Accepted',
            ],

            'shipped' => [
                'ar' => 'تم الشحن',
                'en' => 'Shipped',
            ],

            'delivered' => [
                'ar' => 'تم التوصيل',
                'en' => 'Delivered',
            ],

            'cancelled' => [
                'ar' => 'ملغي',
                'en' => 'Cancelled',
            ],

            default => [
                'ar' => $status,
                'en' => $status,
            ],

        };

    }


    /*
    |--------------------------------------------------------------------------
    | CANCEL ORDER
    |--------------------------------------------------------------------------
    |
    | العميل يلغي الطلب
    | ↓
    | التحقق من الملكية
    | ↓
    | إعادة المخزون
    | ↓
    | تغيير الحالة إلى cancelled
    |
    */

    public function cancelOrder(
        User $user,
        Order $order
    ): Order {

        $this->verifyOwnership(
            $user,
            $order
        );


        /*
        |--------------------------------------------------------------------------
        | Check Status
        |--------------------------------------------------------------------------
        */

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

            throw ValidationException::withMessages([
                'order' => [
                    'لا يمكن إلغاء هذا الطلب في حالته الحالية.'
                ]
            ]);

        }


        return DB::transaction(
            function () use ($order) {

                $order->load([
                    'details'
                ]);


                /*
                |--------------------------------------------------------------------------
                | Restore Stock
                |--------------------------------------------------------------------------
                */

                foreach ($order->details as $detail) {

                    if ($detail->unit_id) {

                        $unit =
                            \App\Models\ProductUnit::where(
                                'id',
                                $detail->unit_id
                            )
                                ->lockForUpdate()
                                ->first();

                        if ($unit) {

                            $unit->increment(
                                'stock',
                                $detail->quantity
                            );

                        }

                    }

                }


                /*
                |--------------------------------------------------------------------------
                | Cancel Order
                |--------------------------------------------------------------------------
                */

                $order->update([
                    'status' =>
                        OrderStatus::CANCELLED,
                ]);


                /*
                |--------------------------------------------------------------------------
                | Notification To Customer
                |--------------------------------------------------------------------------
                */

                InternalNotification::create([

                    'user_id' =>
                        $order->user_id,

                    'title' => [
                        'ar' => 'إلغاء الطلب',
                        'en' => 'Order Cancelled',
                    ],

                    'message' => [
                        'ar' =>
                            "تم إلغاء طلبك رقم #{$order->id}.",

                        'en' =>
                            "Your order #{$order->id} has been cancelled.",
                    ],

                    'type' =>
                        'order',

                    'is_read' =>
                        false,

                    'sent_at' =>
                        now(),
                ]);


                return $order->fresh([
                    'details',
                    'address',
                    'details.product',
                    'details.unit',
                ]);

            }
        );

    }

}
