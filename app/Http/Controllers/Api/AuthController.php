<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register
     *
     * إنشاء حساب مستخدم جديد
     */
    public function register(Request $request): JsonResponse
    {
        /*
        |--------------------------------------------------------------------------
        | Validate Request
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'phone' => [
                'required',
                'string',
                'max:30',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Create User
        |--------------------------------------------------------------------------
        */

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],

            /*
             * User Model عندك يستخدم cast للتشفير
             */
            'password' => $validated['password'],

            'status' => 'active',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Assign Default Role
        |--------------------------------------------------------------------------
        |
        | المستخدم الذي يسجل من شاشة التسجيل العادية
        | سيكون Local Client.
        |
        | الدور الموجود فعلياً في قاعدة البيانات:
        | local-client
        |
        */

        $defaultRole = 'local-client';

        if (
            \Spatie\Permission\Models\Role::where('name', $defaultRole)
                ->where('guard_name', 'sanctum')
                ->exists()
        ) {
            $user->assignRole($defaultRole);
        }


        /*
        |--------------------------------------------------------------------------
        | Create Token
        |--------------------------------------------------------------------------
        */

        $token = $user
            ->createToken('auth-token')
            ->plainTextToken;


        /*
        |--------------------------------------------------------------------------
        | Get Role
        |--------------------------------------------------------------------------
        */

        $role = $user->getRoleNames()->first();


        /*
        |--------------------------------------------------------------------------
        | Return Response
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,

            'message' => 'تم إنشاء الحساب بنجاح',

            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,

                    'role' => $role,

                    'permissions' => $user
                        ->getAllPermissions()
                        ->pluck('name')
                        ->values(),
                ],

                'token' => $token,

                'token_type' => 'Bearer',
            ],
        ], 201);
    }


    /**
     * Login
     *
     * تسجيل الدخول
     */
    public function login(Request $request): JsonResponse
    {
        /*
        |--------------------------------------------------------------------------
        | Validate Request
        |--------------------------------------------------------------------------
        */

        $credentials = $request->validate([
            'email' => [
                'required',
                'email',
            ],

            'password' => [
                'required',
                'string',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Attempt Login
        |--------------------------------------------------------------------------
        */

        if (
            !Auth::attempt([
                'email' => $credentials['email'],
                'password' => $credentials['password'],
            ])
        ) {
            throw ValidationException::withMessages([
                'email' => [
                    'البريد الإلكتروني أو كلمة المرور غير صحيحة',
                ],
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Get Authenticated User
        |--------------------------------------------------------------------------
        */

        /** @var \App\Models\User $user */
        $user = Auth::user();


        /*
        |--------------------------------------------------------------------------
        | Check Account Status
        |--------------------------------------------------------------------------
        */

        if (
            isset($user->status) &&
            $user->status !== 'active'
        ) {
            Auth::logout();

            return response()->json([
                'success' => false,

                'message' => 'هذا الحساب غير مفعل',
            ], 403);
        }


        /*
        |--------------------------------------------------------------------------
        | Delete Old Tokens
        |--------------------------------------------------------------------------
        |
        | حتى لا تتراكم Tokens القديمة للمستخدم.
        |
        */

        $user->tokens()->delete();


        /*
        |--------------------------------------------------------------------------
        | Create New Token
        |--------------------------------------------------------------------------
        */

        $token = $user
            ->createToken('auth-token')
            ->plainTextToken;


        /*
        |--------------------------------------------------------------------------
        | Get Real Role From Spatie
        |--------------------------------------------------------------------------
        */

        $role = $user->getRoleNames()->first();


        /*
        |--------------------------------------------------------------------------
        | Get Permissions
        |--------------------------------------------------------------------------
        */

        $permissions = $user
            ->getAllPermissions()
            ->pluck('name')
            ->values();


        /*
        |--------------------------------------------------------------------------
        | Return Login Response
        |--------------------------------------------------------------------------
        |
        | مهم جداً:
        |
        | React Login.jsx عندك ينتظر:
        |
        | data.data.user
        | data.data.token
        | data.data.token_type
        |
        */

        return response()->json([
            'success' => true,

            'message' => 'تم تسجيل الدخول بنجاح',

            'data' => [
                'user' => [
                    'id' => $user->id,

                    'name' => $user->name,

                    'email' => $user->email,

                    'phone' => $user->phone,

                    'role' => $role,

                    'permissions' => $permissions,
                ],

                'token' => $token,

                'token_type' => 'Bearer',
            ],
        ], 200);
    }


    /**
     * Current User
     *
     * إرجاع بيانات المستخدم الحالي
     */
    public function me(Request $request): JsonResponse
    {
        /*
        |--------------------------------------------------------------------------
        | Get Authenticated User
        |--------------------------------------------------------------------------
        */

        /** @var \App\Models\User $user */
        $user = $request->user();


        /*
        |--------------------------------------------------------------------------
        | Get Role
        |--------------------------------------------------------------------------
        */

        $role = $user
            ->getRoleNames()
            ->first();


        /*
        |--------------------------------------------------------------------------
        | Get Permissions
        |--------------------------------------------------------------------------
        */

        $permissions = $user
            ->getAllPermissions()
            ->pluck('name')
            ->values();


        /*
        |--------------------------------------------------------------------------
        | Return Response
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,

            'message' => 'تم جلب بيانات المستخدم',

            'data' => [
                'user' => [
                    'id' => $user->id,

                    'name' => $user->name,

                    'email' => $user->email,

                    'phone' => $user->phone,

                    'status' => $user->status,
                ],

                'role' => $role,

                'permissions' => $permissions,
            ],
        ], 200);
    }


    /**
     * Logout
     *
     * تسجيل الخروج
     */
    public function logout(Request $request): JsonResponse
    {
        /*
        |--------------------------------------------------------------------------
        | Get Current Token
        |--------------------------------------------------------------------------
        */

        $token = $request
            ->user()
            ->currentAccessToken();


        /*
        |--------------------------------------------------------------------------
        | Delete Current Token
        |--------------------------------------------------------------------------
        */

        if ($token) {
            $token->delete();
        }


        /*
        |--------------------------------------------------------------------------
        | Return Response
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,

            'message' => 'تم تسجيل الخروج بنجاح',
        ], 200);
    }
}

