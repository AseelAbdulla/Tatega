<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    /**
     * Display all customers.
     */
    public function index(): JsonResponse
    {
        $customers = User::with([
            'roles',
            'addresses',
        ])
            ->whereHas('roles', function ($query) {
                $query->whereIn('name', [
                    'local-client',
                    'international-pending',
                ]);
            })
            ->select([
                'id',
                'name',
                'email',
                'phone',
                'status',
                'created_at',
                'updated_at',
            ])
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'تم جلب العملاء بنجاح.',
            'data' => $customers,
        ], 200);
    }

    /**
     * Display one customer.
     */
    public function show(string $id): JsonResponse
    {
        $customer = User::with([
            'roles',
            'addresses',
            'orders',
        ])
            ->whereHas('roles', function ($query) {
                $query->whereIn('name', [
                    'local-client',
                    'international-pending',
                ]);
            })
            ->find($id);

        if (!$customer) {
            return response()->json([
                'status' => false,
                'message' => 'العميل غير موجود.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'تم جلب بيانات العميل بنجاح.',
            'data' => $customer,
        ], 200);
    }

    /**
     * Display customer's orders.
     */
    public function orders(string $id): JsonResponse
    {
        $customer = User::whereHas('roles', function ($query) {
            $query->whereIn('name', [
                'local-client',
                'international-pending',
            ]);
        })->find($id);

        if (!$customer) {
            return response()->json([
                'status' => false,
                'message' => 'العميل غير موجود.',
            ], 404);
        }

        $orders = $customer->orders()
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'تم جلب طلبات العميل بنجاح.',
            'data' => $orders,
        ], 200);
    }
}
