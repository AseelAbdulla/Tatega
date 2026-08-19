<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Http\Resources\OrderResource;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException; // 👈 1. تم إضافة الاستدعاء هنا

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
                abort(
                    403,
                    'هذا العنوان لا يخص المستخدم.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Calculate subtotal & Validate Initial Cart Items
            |--------------------------------------------------------------------------
            */

            $subtotal = 0;

            foreach ($cart->details as $item) {

                if (!$item->product) {
                    throw ValidationException::withMessages([
                        'cart' => ['أحد المنتجات غير موجود.']
                    ]);
                }

                if (!$item->unit) {
                    throw ValidationException::withMessages([
                        'cart' => ['وحدة أحد المنتجات غير موجودة.']
                    ]);
                }

                if (
                    $item->unit->product_id
                    !== $item->product_id
                ) {
                    throw ValidationException::withMessages([
                        'cart' => ['وحدة المنتج غير صحيحة.']
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
            $shipping_fee = $data['shipping_fee'] ?? 0;

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
                        'payment_receipt' => ['يجب إرفاق إيصال الدفع عند اختيار المحفظة.']
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
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'customer_email' => $data['customer_email'],

                /*
                |--------------------------------------------------------------------------
                | Shipping Snapshot
                |--------------------------------------------------------------------------
                */
                'shipping_country' => $address->country,
                'shipping_city' => $address->city,
                'shipping_region' => $address->region,
                'shipping_street' => $address->street,
                'shipping_building' => $address->building,

                /*
                |--------------------------------------------------------------------------
                | Payment
                |--------------------------------------------------------------------------
                */
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'payment_receipt' => $receiptPath,
                'wallet_number' => $data['wallet_number'] ?? null,

                /*
                |--------------------------------------------------------------------------
                | Notes
                |--------------------------------------------------------------------------
                */
                'notes' => $data['notes'] ?? null,

                /*
                |--------------------------------------------------------------------------
                | Pricing
                |--------------------------------------------------------------------------
                */
                'subtotal' => $subtotal,
                'shipping_fee' => $shipping_fee,
                'discount' => $discount,
                'tax' => $tax,
                'total_price' => $total,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Order Details & Reduce Stock (Locked & Safe)
            |--------------------------------------------------------------------------
            */

            foreach ($cart->details as $item) {

                // 🔒 قفل الصف في قاعدة البيانات وقراءة أحدث قيمة للمخزون فوراً
                $unit = \App\Models\ProductUnit::where('id', $item->unit_id)
                    ->lockForUpdate()
                    ->first();

                // 👈 2. التعديل الجوهري هنا (رمي ValidationException بدلاً من abort)
                // ✅ السطر الجديد والمعدل
                if (!$unit || $item->quantity > $unit->stock) {
                    // جلب اسم المنتج وتحويله من JSON إلى مصفوفة إن كان مجسّماً
                    $productName = $item->product->name;
                    if (is_string($productName)) {
                        $decoded = json_decode($productName, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $productName = $decoded['ar'] ?? $decoded['en'] ?? reset($decoded);
                        }
                    } elseif (is_array($productName)) {
                        $productName = $productName['ar'] ?? $productName['en'] ?? reset($productName);
                    }

                    throw ValidationException::withMessages([
                        'cart' => ["الكمية المطلوبة غير متوفرة في المخزون للمنتج: {$productName}"]
                    ]);
                }

                // إنشاء تفاصيل الطلب
                $order->details()->create([
                    'product_id' => $item->product_id,
                    'unit_id' => $item->unit_id,
                    'product_name_snapshot' => $item->product->name,
                    'unit_name_snapshot' => $unit->unit_name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->price,
                    'total_price' => $item->price * $item->quantity,
                ]);

                // 📉 خصم المخزون الآمن من الوحدة
                $unit->decrement('stock', $item->quantity);
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
     * جلب إحصائيات الطلبات وأحدث القائمة للـ API
     */
    public function getDashboardStats(): array
    {
        // 1. حساب الطلبات المعلقة
        $pendingCount = Order::where('status', OrderStatus::PENDING->value)
            ->orWhere('status', 'pending')
            ->count();

        // 2. حساب إجمالي المبيعات للطلبات المكتملة والمقبولة/المشحونة
        $totalSales = Order::whereIn('status', [
            OrderStatus::DELIVERED->value,
            OrderStatus::ACCEPTED->value,
            OrderStatus::SHIPPED->value,
            'delivered',
            'accepted',
            'completed'
        ])->sum('total_price');

        // 3. أحدث 5 طلبات
        $recentOrders = OrderResource::collection(
            Order::latest()->take(5)->get()
        );

        return [
            'stats' => [
                'pending_orders' => (int) $pendingCount,
                'total_sales'    => (float) $totalSales,
            ],
            'recentOrders' => $recentOrders,
        ];
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
     * التحقق من ملكية المستخدم للطلب
     */
    public function verifyOwnership(User $user, Order $order): void
    {
        if ($order->user_id !== $user->id) {
            throw new AuthorizationException('غير مصرح لك بالوصول لهذا الطلب.');
        }
    }

    /**
     * إلغاء الطلب وإعادة كميات المخزون
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
            throw ValidationException::withMessages([
                'order' => ['لا يمكن إلغاء هذا الطلب في حالته الحالية.']
            ]);
        }

        return DB::transaction(function () use ($order) {
            $order->load(['details']);

            foreach ($order->details as $detail) {
                if ($detail->unit_id) {
                    // 🔒 قفل وحدة المنتج ثم إعادة الزيادة
                    $unit = \App\Models\ProductUnit::where('id', $detail->unit_id)
                        ->lockForUpdate()
                        ->first();

                    if ($unit) {
                        $unit->increment('stock', $detail->quantity);
                    }
                }
            }

            $order->update([
                'status' => OrderStatus::CANCELLED,
            ]);

            return $order->fresh([
                'details',
                'address',
            ]);
        });
    }
}
