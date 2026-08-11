<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'address_id' => [
                'required',
                'exists:addresses,id',
            ],

            'customer_name' => [
                'required',
                'string',
                'max:255',
            ],

            'customer_phone' => [
                'required',
                'string',
                'max:50',
            ],

            'customer_email' => [
                'required',
                'email',
                'max:255',
            ],

            'notes' => [
                'nullable',
                'string',
            ],

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

    public function messages(): array
    {
        return [

            'address_id.required' =>
                'العنوان مطلوب.',

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

            'payment_method.required' =>
                'طريقة الدفع مطلوبة.',

            'payment_method.in' =>
                'طريقة الدفع غير صحيحة.',

            'payment_receipt.required_if' =>
                'إيصال الدفع مطلوب عند اختيار المحفظة.',

            'payment_receipt.image' =>
                'إيصال الدفع يجب أن يكون صورة.',

            'payment_receipt.mimes' =>
                'صيغة الإيصال يجب أن تكون jpg أو jpeg أو png أو webp.',

            'payment_receipt.max' =>
                'حجم الإيصال يجب ألا يتجاوز 2MB.',
        ];
    }
}
