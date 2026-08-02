<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image_path' => 'sometimes|string|max:255',
            'slogan' => 'nullable|array',
            'sort_order' => 'nullable|integer',
            'status' => 'nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'image_path.string' => 'مسار الصورة يجب أن يكون نصاً.',
            'image_path.max' => 'مسار الصورة طويل جداً.',

            'slogan.array' => 'الشعار يجب أن يكون مصفوفة.',

            'sort_order.integer' => 'ترتيب العرض يجب أن يكون رقماً صحيحاً.',

            'status.string' => 'الحالة يجب أن تكون نصاً.',
            'status.max' => 'الحالة طويلة جداً.',
        ];
    }
}