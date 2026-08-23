<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerOrderController extends Controller
{
    /**
     * عرض سجل طلبات العميل المسجل دخوله
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $orders = $user
            ->orders()
            ->with([
                'details.product',
                'address',
            ])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }
}
