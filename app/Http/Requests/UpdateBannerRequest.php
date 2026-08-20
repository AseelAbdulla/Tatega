<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,webp|max:5120',

            'slogan' => 'nullable|array',
            'slogan.ar' => 'nullable|string',
            'slogan.en' => 'nullable|string',

            'sort_order' => 'nullable|integer',
            'status' => 'nullable|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => 'صورة البنر مطلوبة.',
            'image.image' => 'الملف يجب أن يكون صورة.',
            'image.mimes' => 'صيغة الصورة يجب أن تكون jpg أو jpeg أو png أو webp.',
            'image.max' => 'حجم الصورة يجب ألا يتجاوز 5 ميجابايت.',

            'slogan.array' => 'النص يجب أن يكون بصيغة صحيحة.',
            'slogan.ar.string' => 'النص العربي يجب أن يكون نصاً.',
            'slogan.en.string' => 'النص الإنجليزي يجب أن يكون نصاً.',

            'sort_order.integer' => 'ترتيب العرض يجب أن يكون رقماً صحيحاً.',

            'status.in' => 'حالة البنر يجب أن تكون active أو inactive.',
        ];
    }
}