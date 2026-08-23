<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    /**
     * =========================================================
     * CUSTOMER - CREATE ORDER
     * =========================================================
     *
     * POST /api/orders
     *
     * الطلبات المحلية يمكن أن ترسل:
     *
     * {
     *   "order_type": "local",
     *   "title": "طاولة خشبية",
     *   "city": "صنعاء",
     *   "description": "أريد طاولة خشبية..."
     * }
     *
     * order_type و total_price سيتم إعطاؤهما قيمًا افتراضية
     * إذا لم ترسلهما الواجهة.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        /*
         * =====================================================
         * VALIDATION
         * =====================================================
         */

        $validated = $request->validate([

            'address_id' => [
                'nullable',
                'integer',
                'exists:addresses,id',
            ],

            /*
             * لم نعد نجعل order_type إجباريًا.
             *
             * إذا لم ترسله الواجهة سيتم اعتباره local.
             */
            'order_type' => [
                'nullable',
                'string',
                'max:50',
            ],

            /*
             * بيانات الطلب المحلي
             */
            'title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'city' => [
                'nullable',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'customer_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'customer_phone' => [
                'nullable',
                'string',
                'max:50',
            ],

            'customer_email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'shipping_country' => [
                'nullable',
                'string',
                'max:255',
            ],

            'shipping_city' => [
                'nullable',
                'string',
                'max:255',
            ],

            'shipping_region' => [
                'nullable',
                'string',
                'max:255',
            ],

            'shipping_street' => [
                'nullable',
                'string',
                'max:255',
            ],

            'shipping_building' => [
                'nullable',
                'string',
                'max:255',
            ],

            'notes' => [
                'nullable',
                'string',
            ],

            'subtotal' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'discount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'tax' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            /*
             * لم نعد نجعل total_price إجباريًا.
             *
             * الطلب المحلي في البداية ليس له سعر.
             */
            'total_price' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'shipping_fee' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'payment_method' => [
                'nullable',
            ],

            'payment_status' => [
                'nullable',
            ],

            'payment_receipt' => [
                'nullable',
                'string',
                'max:255',
            ],

            /*
             * تفاصيل الطلب
             */
            'details' => [
                'nullable',
                'array',
            ],

            'details.*.product_id' => [
                'nullable',
                'integer',
                'exists:products,id',
            ],

            'details.*.unit_id' => [
                'nullable',
                'integer',
                'exists:product_units,id',
            ],

            'details.*.product_name_snapshot' => [
                'nullable',
            ],

            'details.*.unit_name_snapshot' => [
                'nullable',
            ],

            'details.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'details.*.unit_price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'details.*.total_price' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);

        try {

            /*
             * =====================================================
             * تحديد نوع الطلب
             * =====================================================
             *
             * إذا لم ترسل الواجهة order_type
             * نعتبره طلبًا محليًا.
             */

            $orderType =
                $validated['order_type']
                ?? 'local';


            /*
             * =====================================================
             * بيانات الطلب المحلي
             * =====================================================
             *
             * city       -> shipping_city
             * description -> notes
             */

            $shippingCity =
                $validated['shipping_city']
                ?? $validated['city']
                ?? null;

            $notes =
                $validated['notes']
                ?? $validated['description']
                ?? null;


            /*
             * =====================================================
             * السعر
             * =====================================================
             *
             * الطلب المحلي الجديد لا يملك سعرًا بعد.
             * لذلك نضع 0.
             */

            $totalPrice =
                $validated['total_price']
                ?? 0;


            /*
             * =====================================================
             * CREATE ORDER
             * =====================================================
             */

            $order = DB::transaction(function () use (
                $validated,
                $user,
                $orderType,
                $shippingCity,
                $notes,
                $totalPrice
            ) {

                $order = Order::create([

                    'user_id' =>
                        $user->id,

                    'address_id' =>
                        $validated['address_id']
                        ?? null,

                    'order_type' =>
                        $orderType,

                    /*
                     * الحالة الافتراضية:
                     * pending
                     */

                    'status' =>
                        OrderStatus::PENDING,

                    /*
                     * بيانات العميل
                     */

                    'customer_name' =>
                        $validated['customer_name']
                        ?? $user->name,

                    'customer_phone' =>
                        $validated['customer_phone']
                        ?? $user->phone,

                    'customer_email' =>
                        $validated['customer_email']
                        ?? $user->email,

                    /*
                     * بيانات الشحن
                     */

                    'shipping_country' =>
                        $validated['shipping_country']
                        ?? null,

                    'shipping_city' =>
                        $shippingCity,

                    'shipping_region' =>
                        $validated['shipping_region']
                        ?? null,

                    'shipping_street' =>
                        $validated['shipping_street']
                        ?? null,

                    'shipping_building' =>
                        $validated['shipping_building']
                        ?? null,

                    /*
                     * الوصف / الملاحظات
                     */

                    'notes' =>
                        $notes,

                    /*
                     * الأسعار
                     */

                    'subtotal' =>
                        $validated['subtotal']
                        ?? 0,

                    'discount' =>
                        $validated['discount']
                        ?? 0,

                    'tax' =>
                        $validated['tax']
                        ?? 0,

                    'total_price' =>
                        $totalPrice,

                    'shipping_fee' =>
                        $validated['shipping_fee']
                        ?? 0,

                    /*
                     * الدفع
                     */

                    'payment_method' =>
                        $validated['payment_method']
                        ?? null,

                    'payment_status' =>
                        $validated['payment_status']
                        ?? null,

                    'payment_receipt' =>
                        $validated['payment_receipt']
                        ?? null,
                ]);


                /*
                 * =================================================
                 * CREATE ORDER DETAILS
                 * =================================================
                 */

                if (
                    isset($validated['details']) &&
                    is_array($validated['details'])
                ) {

                    foreach (
                        $validated['details']
                        as $detail
                    ) {

                        OrderDetail::create([

                            'order_id' =>
                                $order->id,

                            'product_id' =>
                                $detail['product_id']
                                ?? null,

                            'unit_id' =>
                                $detail['unit_id']
                                ?? null,

                            'product_name_snapshot' =>
                                $detail['product_name_snapshot']
                                ?? null,

                            'unit_name_snapshot' =>
                                $detail['unit_name_snapshot']
                                ?? null,

                            'quantity' =>
                                $detail['quantity'],

                            'unit_price' =>
                                $detail['unit_price'],

                            'total_price' =>
                                $detail['total_price'],
                        ]);
                    }
                }

                return $order;
            });


            /*
             * =====================================================
             * LOAD RELATIONS
             * =====================================================
             */

            $order->load([
                'details.product',
                'details.unit',
                'address',
            ]);


            /*
             * =====================================================
             * RESPONSE
             * =====================================================
             *
             * نرجع success و status معًا
             * حتى يتوافق الـBackend مع الـFrontend.
             */

            return response()->json([

                'success' => true,

                'status' => true,

                'message' =>
                    'تم إنشاء الطلب بنجاح.',

                'data' =>
                    $order,

            ], 201);


        } catch (\Throwable $e) {

            return response()->json([

                'success' => false,

                'status' => false,

                'message' =>
                    'حدث خطأ أثناء إنشاء الطلب.',

                'error' =>
                    config('app.debug')
                    ? $e->getMessage()
                    : null,

            ], 500);
        }
    }


    /**
     * =========================================================
     * CUSTOMER - ORDER HISTORY
     * =========================================================
     *
     * GET /api/orders
     */
    public function index(Request $request)
    {
        $orders = $request->user()
            ->orders()
            ->with([
                'details.product',
                'details.unit',
                'address',
            ])
            ->latest()
            ->get();


        return response()->json([

            'success' => true,

            'status' => true,

            'data' => $orders,

        ]);
    }


    /**
     * =========================================================
     * CUSTOMER - SHOW ORDER
     * =========================================================
     *
     * GET /api/orders/{order}
     */
    public function show(
        Request $request,
        Order $order
    ) {

        if (
            $order->user_id !==
            $request->user()->id
        ) {

            return response()->json([

                'success' => false,

                'status' => false,

                'message' =>
                    'غير مصرح لك بعرض هذا الطلب.',

            ], 403);
        }


        $order->load([
            'details.product',
            'details.unit',
            'address',
        ]);


        return response()->json([

            'success' => true,

            'status' => true,

            'data' => $order,

        ]);
    }


    /**
     * =========================================================
     * CUSTOMER - CANCEL ORDER
     * =========================================================
     *
     * PATCH /api/orders/{order}/cancel
     */
    public function cancel(
        Request $request,
        Order $order
    ) {

        if (
            $order->user_id !==
            $request->user()->id
        ) {

            return response()->json([

                'success' => false,

                'status' => false,

                'message' =>
                    'غير مصرح لك بإلغاء هذا الطلب.',

            ], 403);
        }


        if (
            in_array(
                $order->status,
                [
                    OrderStatus::SHIPPED,
                    OrderStatus::DELIVERED,
                ],
                true
            )
        ) {

            return response()->json([

                'success' => false,

                'status' => false,

                'message' =>
                    'لا يمكن إلغاء الطلب بعد شحنه أو توصيله.',

            ], 422);
        }


        $order->update([

            'status' =>
                OrderStatus::CANCELLED,

        ]);


        return response()->json([

            'success' => true,

            'status' => true,

            'message' =>
                'تم إلغاء الطلب بنجاح.',

            'data' =>
                $order->fresh([
                    'details.product',
                    'details.unit',
                    'address',
                ]),

        ]);
    }


    /**
     * =========================================================
     * ADMIN - ALL ORDERS
     * =========================================================
     *
     * GET /api/admin/orders
     */
    public function adminIndex(Request $request)
    {
        $orders = Order::with([
            'user',
            'details.product',
            'details.unit',
            'address',
        ])
            ->latest()
            ->paginate(
                $request->integer(
                    'per_page',
                    15
                )
            );


        return response()->json([

            'success' => true,

            'status' => true,

            'data' => $orders,

        ]);
    }


    /**
     * =========================================================
     * ADMIN - SHOW ORDER
     * =========================================================
     *
     * GET /api/admin/orders/{order}
     */
    public function adminShow(
        Order $order
    ) {

        $order->load([
            'user',
            'details.product',
            'details.unit',
            'address',
        ]);


        return response()->json([

            'success' => true,

            'status' => true,

            'data' => $order,

        ]);
    }


    /**
     * =========================================================
     * ADMIN - UPDATE ORDER STATUS
     * =========================================================
     *
     * PATCH /api/admin/orders/{order}/status
     */
    public function updateStatus(
        Request $request,
        Order $order
    ) {

        $validated = $request->validate([

            'status' => [

                'required',

                Rule::in(
                    array_map(
                        fn ($status) =>
                            $status->value,
                        OrderStatus::cases()
                    )
                ),

            ],

            'rejection_reason' => [
                'nullable',
                'string',
            ],

        ]);


        $order->update([

            'status' =>
                $validated['status'],

            'rejection_reason' =>
                $validated['rejection_reason']
                ?? null,

        ]);


        $order->load([
            'user',
            'details.product',
            'details.unit',
            'address',
        ]);


        return response()->json([

            'success' => true,

            'status' => true,

            'message' =>
                'تم تحديث حالة الطلب بنجاح.',

            'data' =>
                $order,

        ]);
    }


    /**
     * =========================================================
     * ADMIN - DASHBOARD STATISTICS
     * =========================================================
     *
     * GET /api/admin/dashboard/stats
     */
    public function dashboardStats()
    {
        $total =
            Order::count();


        $pending =
            Order::where(
                'status',
                OrderStatus::PENDING
            )->count();


        $accepted =
            Order::where(
                'status',
                OrderStatus::ACCEPTED
            )->count();


        $preparing =
            Order::where(
                'status',
                OrderStatus::PREPARING
            )->count();


        $shipped =
            Order::where(
                'status',
                OrderStatus::SHIPPED
            )->count();


        $delivered =
            Order::where(
                'status',
                OrderStatus::DELIVERED
            )->count();


        $cancelled =
            Order::where(
                'status',
                OrderStatus::CANCELLED
            )->count();


        $rejected =
            Order::where(
                'status',
                OrderStatus::REJECTED
            )->count();


        return response()->json([

            'success' => true,

            'status' => true,

            'data' => [

                'total' =>
                    $total,

                'pending' =>
                    $pending,

                'accepted' =>
                    $accepted,

                'preparing' =>
                    $preparing,

                'shipped' =>
                    $shipped,

                'delivered' =>
                    $delivered,

                'cancelled' =>
                    $cancelled,

                'rejected' =>
                    $rejected,

            ],

        ]);
    }


    /**
     * =========================================================
     * ADMIN - UPDATE ORDER
     * =========================================================
     *
     * PUT/PATCH /api/admin/orders/{order}
     */
    public function update(
        Request $request,
        Order $order
    ) {

        $validated = $request->validate([

            'order_type' => [
                'sometimes',
                'string',
                'max:50',
            ],

            'customer_name' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'customer_phone' => [
                'sometimes',
                'nullable',
                'string',
                'max:50',
            ],

            'customer_email' => [
                'sometimes',
                'nullable',
                'email',
                'max:255',
            ],

            'shipping_country' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'shipping_city' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'shipping_region' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'shipping_street' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'shipping_building' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'notes' => [
                'sometimes',
                'nullable',
                'string',
            ],

            'subtotal' => [
                'sometimes',
                'numeric',
                'min:0',
            ],

            'discount' => [
                'sometimes',
                'numeric',
                'min:0',
            ],

            'tax' => [
                'sometimes',
                'numeric',
                'min:0',
            ],

            'total_price' => [
                'sometimes',
                'numeric',
                'min:0',
            ],

            'shipping_fee' => [
                'sometimes',
                'numeric',
                'min:0',
            ],

            'status' => [
                'sometimes',
                Rule::in(
                    array_map(
                        fn ($status) =>
                            $status->value,
                        OrderStatus::cases()
                    )
                ),
            ],

        ]);


        $order->update(
            $validated
        );


        $order->load([
            'user',
            'details.product',
            'details.unit',
            'address',
        ]);


        return response()->json([

            'success' => true,

            'status' => true,

            'message' =>
                'تم تحديث الطلب بنجاح.',

            'data' =>
                $order,

        ]);
    }


    /**
     * =========================================================
     * ADMIN - DELETE ORDER
     * =========================================================
     *
     * DELETE /api/admin/orders/{order}
     */
    public function destroy(
        Order $order
    ) {

        DB::transaction(
            function () use ($order) {

                /*
                 * حذف تفاصيل الطلب أولاً
                 */

                $order->details()->delete();


                /*
                 * حذف الطلب
                 */

                $order->delete();
            }
        );


        return response()->json([

            'success' => true,

            'status' => true,

            'message' =>
                'تم حذف الطلب بنجاح.',

        ]);
    }
}

