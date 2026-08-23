<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    /**
     * عرض طرق الدفع الخاصة بالعميل الحالي
     */
    public function index(Request $request)
    {
        $paymentMethods = $request->user()
            ->paymentMethods()
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $paymentMethods,
        ]);
    }

    /**
     * إضافة طريقة دفع
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:50',

            'cardholder_name' =>
                'nullable|string|max:255',

            'last_four' =>
                'nullable|string|max:4',

            'expiry_month' =>
                'nullable|integer|min:1|max:12',

            'expiry_year' =>
                'nullable|integer|min:' . date('Y'),

            'is_default' =>
                'sometimes|boolean',

            'status' =>
                'nullable|string|max:50',
        ]);

        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | إذا كانت طريقة الدفع الجديدة افتراضية
        |--------------------------------------------------------------------------
        */

        if (
            isset($validated['is_default']) &&
            $validated['is_default'] === true
        ) {
            $user->paymentMethods()->update([
                'is_default' => false,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | إنشاء طريقة الدفع
        |--------------------------------------------------------------------------
        */

        $paymentMethod =
            $user->paymentMethods()->create(
                $validated
            );

        return response()->json([
            'success' => true,
            'message' =>
                'Payment method added successfully.',
            'data' => $paymentMethod,
        ], 201);
    }

    /**
     * عرض طريقة دفع محددة
     */
    public function show(
        Request $request,
        $id
    ) {
        $paymentMethod =
            $request->user()
                ->paymentMethods()
                ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $paymentMethod,
        ]);
    }

    /**
     * تحديث طريقة دفع
     */
    public function update(
        Request $request,
        $id
    ) {
        $paymentMethod =
            $request->user()
                ->paymentMethods()
                ->findOrFail($id);

        $validated = $request->validate([
            'type' =>
                'sometimes|string|max:50',

            'cardholder_name' =>
                'nullable|string|max:255',

            'last_four' =>
                'nullable|string|max:4',

            'expiry_month' =>
                'nullable|integer|min:1|max:12',

            'expiry_year' =>
                'nullable|integer|min:' . date('Y'),

            'is_default' =>
                'sometimes|boolean',

            'status' =>
                'nullable|string|max:50',
        ]);

        /*
        |--------------------------------------------------------------------------
        | إذا أصبحت الطريقة افتراضية
        |--------------------------------------------------------------------------
        */

        if (
            isset($validated['is_default']) &&
            $validated['is_default'] === true
        ) {
            $request->user()
                ->paymentMethods()
                ->where(
                    'id',
                    '!=',
                    $paymentMethod->id
                )
                ->update([
                    'is_default' => false,
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | تحديث
        |--------------------------------------------------------------------------
        */

        $paymentMethod->update(
            $validated
        );

        return response()->json([
            'success' => true,
            'message' =>
                'Payment method updated successfully.',
            'data' =>
                $paymentMethod->fresh(),
        ]);
    }

    /**
     * حذف طريقة دفع
     */
    public function destroy(
        Request $request,
        $id
    ) {
        $paymentMethod =
            $request->user()
                ->paymentMethods()
                ->findOrFail($id);

        $paymentMethod->delete();

        return response()->json([
            'success' => true,
            'message' =>
                'Payment method deleted successfully.',
        ]);
    }

    /**
     * جعل طريقة دفع افتراضية
     */
    public function setDefault(
        Request $request,
        $id
    ) {
        $paymentMethod =
            $request->user()
                ->paymentMethods()
                ->findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | إلغاء الافتراضية عن جميع طرق الدفع
        |--------------------------------------------------------------------------
        */

        $request->user()
            ->paymentMethods()
            ->update([
                'is_default' => false,
            ]);

        /*
        |--------------------------------------------------------------------------
        | جعل الطريقة المطلوبة افتراضية
        |--------------------------------------------------------------------------
        */

        $paymentMethod->update([
            'is_default' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' =>
                'Default payment method updated successfully.',
            'data' =>
                $paymentMethod->fresh(),
        ]);
    }
}
