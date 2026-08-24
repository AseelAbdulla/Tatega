<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerProfileController extends Controller
{
    /**
     * عرض الملف الشخصي للعميل الحالي
     *
     * GET /api/customer/profile
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | تحميل عناوين المستخدم
        |--------------------------------------------------------------------------
        */

        $user->load('addresses');

        return response()->json([
            'success' => true,

            'message' =>
                'تم جلب بيانات الملف الشخصي بنجاح',

            'data' => [
                'id' => $user->id,

                'name' => $user->name,

                'email' => $user->email,

                'phone' => $user->phone,

                'status' => $user->status,

                'profile_image' =>
                    $user->profile_image ?? null,

                /*
                |--------------------------------------------------------------------------
                | جميع العناوين
                |--------------------------------------------------------------------------
                */

                'addresses' =>
                    $user->addresses,

                /*
                |--------------------------------------------------------------------------
                | أول عنوان للاستخدام السريع
                |--------------------------------------------------------------------------
                */

                'address' =>
                    $user->addresses->first(),
            ],
        ], 200);
    }


    /**
     * تحديث الملف الشخصي
     *
     * PUT /api/customer/profile
     */
    public function update(
        Request $request
    ): JsonResponse {

        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            /*
            |--------------------------------------------------------------------------
            | User
            |--------------------------------------------------------------------------
            */

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',

                Rule::unique(
                    'users',
                    'email'
                )->ignore($user->id),
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            /*
            |--------------------------------------------------------------------------
            | Address
            |--------------------------------------------------------------------------
            */

            'address' => [
                'required',
                'array',
            ],

            'address.country' => [
                'required',
                'string',
                'max:100',
            ],

            'address.city' => [
                'required',
                'string',
                'max:100',
            ],

            'address.region' => [
                'required',
                'string',
                'max:100',
            ],

            'address.street' => [
                'required',
                'string',
                'max:255',
            ],

            'address.building' => [
                'required',
                'string',
                'max:100',
            ],

            'address.notes' => [
                'nullable',
                'string',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | تحديث بيانات المستخدم
        |--------------------------------------------------------------------------
        */

        $user->name =
            $validated['name'];

        $user->email =
            $validated['email'];

        $user->phone =
            $validated['phone'] ?? null;

        $user->save();


        /*
        |--------------------------------------------------------------------------
        | تحديث عنوان المستخدم
        |--------------------------------------------------------------------------
        */

        $addressData =
            $validated['address'];

        $firstAddress =
            $user->addresses()->first();


        /*
        |--------------------------------------------------------------------------
        | إذا كان لديه عنوان
        |--------------------------------------------------------------------------
        */

        if ($firstAddress) {

            $firstAddress->update([
                'country' =>
                    $addressData['country'],

                'city' =>
                    $addressData['city'],

                'region' =>
                    $addressData['region'],

                'street' =>
                    $addressData['street'],

                'building' =>
                    $addressData['building'],

                'notes' =>
                    $addressData['notes'] ?? null,
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | إذا لم يكن لديه عنوان
        |--------------------------------------------------------------------------
        */

        else {

            $user->addresses()->create([
                'country' =>
                    $addressData['country'],

                'city' =>
                    $addressData['city'],

                'region' =>
                    $addressData['region'],

                'street' =>
                    $addressData['street'],

                'building' =>
                    $addressData['building'],

                'notes' =>
                    $addressData['notes'] ?? null,
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | إعادة تحميل البيانات
        |--------------------------------------------------------------------------
        */

        $user->load('addresses');


        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,

            'message' =>
                'تم تحديث الملف الشخصي بنجاح',

            'data' => [
                'id' => $user->id,

                'name' => $user->name,

                'email' => $user->email,

                'phone' => $user->phone,

                'status' => $user->status,

                'profile_image' =>
                    $user->profile_image ?? null,

                'addresses' =>
                    $user->addresses,

                'address' =>
                    $user->addresses->first(),
            ],
        ], 200);
    }
}

