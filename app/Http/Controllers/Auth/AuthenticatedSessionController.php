<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle login request (API Sanctum)
     */
    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'email' => [
                'required',
                'email'
            ],

            'password' => [
                'required'
            ],
        ]);


        // البحث عن المستخدم
        $user = User::where('email', $request->email)->first();


        // التحقق من البيانات
        if (!$user || !Hash::check($request->password, $user->password)) {

            return response()->json([
                'message' => 'Invalid email or password'
            ], 401);

        }


        // إنشاء Token Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;


        return response()->json([

            'message' => 'Logged in successfully',

            'access_token' => $token,

            'token_type' => 'Bearer',

            'user' => $user

        ], 200);
    }



    /**
     * Logout API
     */
    public function destroy(Request $request)
    {

        // حذف التوكن الحالي
        $request->user()->currentAccessToken()->delete();


        return response()->json([

            'message' => 'Logged out successfully'

        ], 200);

    }
}
