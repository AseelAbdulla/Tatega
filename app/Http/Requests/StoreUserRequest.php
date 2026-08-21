<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:20',
                'unique:users,phone',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            /*
             * Frontend يرسل اسم الدور
             * وليس role_id
             */
            'role' => [
                'required',
                'string',
                Rule::exists('roles', 'name')
                    ->where('guard_name', 'sanctum'),
            ],

            'status' => [
                'nullable',
                'in:active,inactive',
            ],

            'customer_type' => [
                'nullable',
                'in:local,international',
            ],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' =>
                'يرجى إدخال اسم المستخدم.',

            'email.required' =>
                'يرجى إدخال البريد الإلكتروني.',

            'email.email' =>
                'صيغة البريد الإلكتروني غير صحيحة.',

            'email.unique' =>
                'البريد الإلكتروني مستخدم بالفعل.',

            'phone.unique' =>
                'رقم الجوال مستخدم بالفعل.',

            'password.required' =>
                'يرجى إدخال كلمة المرور.',

            'password.min' =>
                'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',

            'password.confirmed' =>
                'كلمتا المرور غير متطابقتين.',

            'role.required' =>
                'يرجى اختيار دور المستخدم.',

            'role.exists' =>
                'الدور المحدد غير موجود.',

            'status.in' =>
                'حالة الحساب غير صحيحة.',

            'customer_type.in' =>
                'نوع العميل غير صحيح.',
        ];
    }
}
