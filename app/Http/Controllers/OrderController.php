<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;


class OrderController extends Controller
{

    public function __construct(
        protected OrderService $orderService
    ) {}

    /**
     * إنشاء طلب جديد من السلة
     */
    public function store(StoreOrderRequest $request)
    {

        $order = $this->orderService->createOrder(

            user: Auth::user(),

            data: $request->validated(),

            receipt: $request->file('payment_receipt')

        );


        return response()->json([

            'status' => true,

            'message' => 'تم إنشاء الطلب بنجاح.',

            'data' => new OrderResource($order),

        ], 201);

    }

    /**
     * عرض طلبات المستخدم
     */
    public function index()
    {

        $orders = $this->orderService->getUserOrders(
            Auth::user()
        );


        return OrderResource::collection($orders);

    }

    /**
     * عرض طلب واحد
     */
    public function show(Order $order)
    {

        $order = $this->orderService->getOrder(
            Auth::user(),
            $order
        );


        return new OrderResource($order);

    }

}

