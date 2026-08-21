<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get validation rules.
     */
    public function rules(): array
    {
        $id = $this->route('user');

        return [
            'name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($id),
            ],

            'phone' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('users', 'phone')->ignore($id),
            ],

            /*
             * كلمة المرور اختيارية في التعديل
             */
            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],

            'status' => [
                'nullable',
                'in:active,inactive',
            ],

            /*
             * اسم الدور وليس role_id
             */
            'role' => [
                'nullable',
                'string',
                Rule::exists('roles', 'name')
                    ->where('guard_name', 'sanctum'),
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
            'email.unique' =>
                'البريد الإلكتروني مستخدم بالفعل.',

            'phone.unique' =>
                'رقم الجوال مستخدم بالفعل.',

            'password.min' =>
                'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',

            'password.confirmed' =>
                'كلمتا المرور غير متطابقتين.',

            'role.exists' =>
                'الدور المحدد غير موجود.',

            'status.in' =>
                'حالة الحساب غير صحيحة.',

            'customer_type.in' =>
                'نوع العميل غير صحيح.',
        ];
    }
}
