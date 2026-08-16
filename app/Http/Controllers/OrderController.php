<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | CLIENT ENDPOINTS
    |--------------------------------------------------------------------------
    */

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->createOrder(
            user: Auth::user(),
            data: $request->validated(),
            receipt: $request->file('payment_receipt')
        );

        return response()->json([
            'status'  => true,
            'message' => 'تم إنشاء الطلب بنجاح.',
            'data'    => new OrderResource($order),
        ], 201);
    }

    public function index()
    {
        $orders = $this->orderService->getUserOrders(Auth::user());
        return OrderResource::collection($orders);
    }

    public function show(Order $order)
    {
        $order = $this->orderService->getOrder(Auth::user(), $order);
        return new OrderResource($order);
    }

    public function cancel(Order $order): JsonResponse
    {
        $order = $this->orderService->cancelOrder(Auth::user(), $order);

        return response()->json([
            'status'  => true,
            'message' => 'تم إلغاء الطلب بنجاح.',
            'data'    => new OrderResource($order),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN ENDPOINTS
    |--------------------------------------------------------------------------
    */

    /**
     * GET /api/admin/orders
     */
    public function adminIndex(): JsonResponse
    {
        $orders = Order::with(['user', 'address','details.product', 'details.unit'])->latest()->get();

        return response()->json([
            'status' => true,
            'data'   => OrderResource::collection($orders),
        ]);
    }

    /**
     * GET /api/admin/orders/{order}
     */
    public function adminShow(Order $order): JsonResponse
    {
        $order->load(['user', 'details', 'address', 'details.product', 'details.unit']);

        return response()->json([
            'status' => true,
            'data'   => new OrderResource($order),
        ]);
    }

    /**
     * PUT/PATCH /api/admin/orders/{order}
     */
    public function update(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'sometimes|string',
            'total'  => 'sometimes|numeric',
            'notes'  => 'nullable|string',
        ]);

        $order->update($validated);

        return response()->json([
            'status'  => true,
            'message' => 'تم تحديث بيانات الطلب بنجاح.',
            'data'    => new OrderResource($order),
        ]);
    }

    /**
     * DELETE /api/admin/orders/{order}
     */
    public function destroy(Order $order): JsonResponse
    {
        $order->delete();

        return response()->json([
            'status'  => true,
            'message' => 'تم حذف الطلب بنجاح.',
        ]);
    }

    /**
     * PATCH /api/admin/orders/{order}/status
     */
    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $order->update(['status' => $request->status]);

        return response()->json([
            'status'  => true,
            'message' => 'تم تحديث حالة الطلب بنجاح.',
            'data'    => new OrderResource($order),
        ]);
    }

    /**
     * GET /api/admin/dashboard/stats
     */
    public function dashboardStats(): JsonResponse
    {
        $data = $this->orderService->getDashboardStats();

        return response()->json([
            'status' => true,
            'data'   => $data,
        ])->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }
}
