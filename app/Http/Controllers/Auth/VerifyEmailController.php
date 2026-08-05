<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class VerifyEmailController extends Controller
{
    public function __invoke(Request $request, $id, $hash)
    {
        // التحقق من توقيع الرابط
        if (! URL::hasValidSignature($request)) {
            return response()->json([
                'message' => 'Invalid or expired verification link'
            ], 403);
        }


        // جلب المستخدم من id الموجود في الرابط
        $user = User::findOrFail($id);


        // مقارنة hash البريد
        if (! hash_equals(
            sha1($user->getEmailForVerification()),
            $hash
        )) {
            return response()->json([
                'message' => 'Invalid verification hash'
            ], 403);
        }


        // تحديث البريد
        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }


        return response()->json([
            'message' => 'Email verified successfully',
            'email_verified_at' => $user->email_verified_at
        ]);
    }
}
