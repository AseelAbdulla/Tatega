<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerPaymentMethodController extends Controller
{
    /**
     * عرض طرق الدفع الخاصة بالعميل
     *
     * GET /api/customer/payment-methods
     */
    public function index(Request $request): JsonResponse
    {
        $paymentMethods = $request->user()
            ->paymentMethods()
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'تم جلب طرق الدفع بنجاح',
            'data' => $paymentMethods,
        ], 200);
    }


    /**
     * إضافة طريقة دفع
     *
     * POST /api/customer/payment-methods
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => [
                'required',
                'string',
                'max:50',
            ],

            'cardholder_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'last_four' => [
                'nullable',
                'string',
                'size:4',
                'regex:/^[0-9]+$/',
            ],

            'expiry_month' => [
                'nullable',
                'integer',
                'between:1,12',
            ],

            'expiry_year' => [
                'nullable',
                'integer',
                'min:2024',
                'max:2100',
            ],

            'is_default' => [
                'nullable',
                'boolean',
            ],
        ]);

        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | إذا كانت الطريقة الجديدة Default
        | نجعل بقية الطرق غير افتراضية
        |--------------------------------------------------------------------------
        */

        $isDefault =
            $validated['is_default'] ?? false;

        if ($isDefault) {
            $user->paymentMethods()->update([
                'is_default' => false,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | إذا لم توجد أي طريقة دفع
        | نجعل أول طريقة هي الافتراضية
        |--------------------------------------------------------------------------
        */

        if (
            !$user->paymentMethods()->exists()
        ) {
            $isDefault = true;
        }

        $paymentMethod =
            $user->paymentMethods()->create([
                'type' =>
                    $validated['type'],

                'cardholder_name' =>
                    $validated['cardholder_name']
                    ?? null,

                'last_four' =>
                    $validated['last_four']
                    ?? null,

                'expiry_month' =>
                    $validated['expiry_month']
                    ?? null,

                'expiry_year' =>
                    $validated['expiry_year']
                    ?? null,

                'is_default' =>
                    $isDefault,

                'status' =>
                    'active',
            ]);

        return response()->json([
            'success' => true,
            'message' =>
                'تمت إضافة طريقة الدفع بنجاح',
            'data' => $paymentMethod,
        ], 201);
    }


    /**
     * عرض طريقة دفع واحدة
     *
     * GET /api/customer/payment-methods/{paymentMethod}
     */
    public function show(
        Request $request,
        PaymentMethod $paymentMethod
    ): JsonResponse {
        /*
        |--------------------------------------------------------------------------
        | التأكد أن الطريقة تخص المستخدم الحالي
        |--------------------------------------------------------------------------
        */

        if (
            $paymentMethod->user_id !==
            $request->user()->id
        ) {
            return response()->json([
                'success' => false,
                'message' =>
                    'غير مصرح لك بالوصول إلى طريقة الدفع هذه.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $paymentMethod,
        ], 200);
    }


    /**
     * تعديل طريقة دفع
     *
     * PUT /api/customer/payment-methods/{paymentMethod}
     */
    public function update(
        Request $request,
        PaymentMethod $paymentMethod
    ): JsonResponse {
        /*
        |--------------------------------------------------------------------------
        | Ownership
        |--------------------------------------------------------------------------
        */

        if (
            $paymentMethod->user_id !==
            $request->user()->id
        ) {
            return response()->json([
                'success' => false,
                'message' =>
                    'غير مصرح لك بتعديل طريقة الدفع هذه.',
            ], 403);
        }

        $validated = $request->validate([
            'type' => [
                'sometimes',
                'string',
                'max:50',
            ],

            'cardholder_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'last_four' => [
                'nullable',
                'string',
                'size:4',
                'regex:/^[0-9]+$/',
            ],

            'expiry_month' => [
                'nullable',
                'integer',
                'between:1,12',
            ],

            'expiry_year' => [
                'nullable',
                'integer',
                'min:2024',
                'max:2100',
            ],

            'is_default' => [
                'nullable',
                'boolean',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Default
        |--------------------------------------------------------------------------
        */

        if (
            isset($validated['is_default']) &&
            $validated['is_default'] === true
        ) {
            $request->user()
                ->paymentMethods()
                ->update([
                    'is_default' => false,
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        $paymentMethod->update(
            $validated
        );

        return response()->json([
            'success' => true,
            'message' =>
                'تم تعديل طريقة الدفع بنجاح',
            'data' => $paymentMethod->fresh(),
        ], 200);
    }


    /**
     * حذف طريقة دفع
     *
     * DELETE /api/customer/payment-methods/{paymentMethod}
     */
    public function destroy(
        Request $request,
        PaymentMethod $paymentMethod
    ): JsonResponse {
        /*
        |--------------------------------------------------------------------------
        | Ownership
        |--------------------------------------------------------------------------
        */

        if (
            $paymentMethod->user_id !==
            $request->user()->id
        ) {
            return response()->json([
                'success' => false,
                'message' =>
                    'غير مصرح لك بحذف طريقة الدفع هذه.',
            ], 403);
        }

        $wasDefault =
            $paymentMethod->is_default;

        $paymentMethod->delete();

        /*
        |--------------------------------------------------------------------------
        | إذا حذفنا Default
        | نحدد طريقة أخرى كافتراضية
        |--------------------------------------------------------------------------
        */

        if ($wasDefault) {

            $nextMethod =
                $request->user()
                    ->paymentMethods()
                    ->latest()
                    ->first();

            if ($nextMethod) {
                $nextMethod->update([
                    'is_default' => true,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' =>
                'تم حذف طريقة الدفع بنجاح',
        ], 200);
    }


    /**
     * تحديد طريقة دفع كافتراضية
     *
     * PATCH /api/customer/payment-methods/{paymentMethod}/default
     */
    public function setDefault(
        Request $request,
        PaymentMethod $paymentMethod
    ): JsonResponse {
        /*
        |--------------------------------------------------------------------------
        | Ownership
        |--------------------------------------------------------------------------
        */

        if (
            $paymentMethod->user_id !==
            $request->user()->id
        ) {
            return response()->json([
                'success' => false,
                'message' =>
                    'غير مصرح لك بتعديل طريقة الدفع هذه.',
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | إزالة Default من الجميع
        |--------------------------------------------------------------------------
        */

        $request->user()
            ->paymentMethods()
            ->update([
                'is_default' => false,
            ]);

        /*
        |--------------------------------------------------------------------------
        | جعل الطريقة الحالية Default
        |--------------------------------------------------------------------------
        */

        $paymentMethod->update([
            'is_default' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' =>
                'تم تحديد طريقة الدفع الافتراضية بنجاح',
            'data' =>
                $paymentMethod->fresh(),
        ], 200);
    }
}
