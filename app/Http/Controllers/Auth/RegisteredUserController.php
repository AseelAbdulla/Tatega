<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:' . User::class
            ],
            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults()
            ],
            'phone' => [
                'required',
                'string',
                'max:50',
                'unique:users,phone'
            ],
            'role' => [
                'required',
                'in:admin,employee,local-client,emport-client'
            ],
        ]);

        // 1. إنشاء المستخدم
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => 'active',
            'password' => Hash::make($request->string('password')),
        ]);

        // 2. إسناد الدور باستخدام طريقة Spatie الرسمية
        $user->assignRole($request->role);

        event(new Registered($user));

        // 3. إنشاء التوكين
        $token = $user->createToken('auth_token')->plainTextToken;

        // 4. تجهيز بيانات المستخدم مع إرجاع اسم الدور بشكل صريح لـ React
        $userData = $user->toArray();
        $userData['role'] = $request->role; // ضمان إرجاع الدور المختار بدقة

        return response()->json([
            'message' => 'تم إنشاء الحساب بنجاح',
            'token' => $token,
            'user' => $userData
        ], 201);
    }
}

