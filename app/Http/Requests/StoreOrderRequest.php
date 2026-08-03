<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    /**
     * تحديد صلاحية تنفيذ الطلب
     */
    public function authorize(): bool
    {
        return true;
    }


    /**
     * قواعد التحقق
     */
    public function rules(): array
    {
        return [

            'address_id' => [
                'nullable',
                'exists:addresses,id'
            ],


            'customer_name' => [
                'required',
                'string',
                'max:255'
            ],


            'customer_phone' => [
                'required',
                'string',
                'max:50'
            ],


            'customer_email' => [
                'required',
                'email',
                'max:255'
            ],


            'notes' => [
                'nullable',
                'string'
            ],

            /*
             |-------------------------------------------
             | بيانات الدفع
             |-------------------------------------------
             */

            'payment_method' => [
                'required',
                Rule::in([
                    'cash_on_delivery',
                    'wallet',
                ]),
            ],

            'payment_receipt' => [
                'nullable',
                'required_if:payment_method,wallet',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ];
    }


    /**
     * رسائل الخطأ
     */
    public function messages(): array
    {
        return [

            'address_id.exists' =>
                'العنوان المحدد غير موجود.',


            'customer_name.required' =>
                'اسم العميل مطلوب.',


            'customer_phone.required' =>
                'رقم الهاتف مطلوب.',


            'customer_email.required' =>
                'البريد الإلكتروني مطلوب.',


            'customer_email.email' =>
                'البريد الإلكتروني غير صحيح.',

        ];
    }
}
