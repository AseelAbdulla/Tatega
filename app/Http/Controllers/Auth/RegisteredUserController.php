<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeUserMail;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): Response
    {
        // 1. أضفنا التحقق (Validation) لحقول الهاتف والحالة لضمان وصولها بشكل سليم ومنع الأخطاء
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:255'], // تم إضافة التحقق لحقل الهاتف
            'status' => ['nullable', 'string'], // تم إضافة التحقق لحقل الحالة
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2. قمنا بتصحيح إنشاء المستخدم واستخدام القيمة الصحيحة للحالة مع وضع قيمة افتراضية 'active'
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => $request->status ?? 'active', // تم تعديله من active$ إلى الحالة الصحيحة مع قيمة افتراضية لمنع خطأ قاعدة البيانات
            'password' => Hash::make($request->string('password')),
        ]);

        // 3. إرسال البريد الإلكتروني للترحيب بالمستخدم مرة واحدة فقط
        Mail::to($user->email)->send(new WelcomeUserMail());

        // 4. تفعيل حدث التسجيل
        event(new Registered($user));

        // 5. تسجيل دخول المستخدم بعد التسجيل
        Auth::login($user);

        // 6. إزالة التكرار العشوائي والأكواد الميتة التي كانت مكررة في أسفل الدالة وحذفنا الكود الزائد

        return response()->noContent();
    }
}