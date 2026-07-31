<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCartItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'unit_id'    => ['required', 'exists:product_units,id'],
            'quantity'   => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'يجب اختيار المنتج.',
            'product_id.exists'   => 'المنتج غير موجود.',

            'unit_id.required'    => 'يجب اختيار الوحدة.',
            'unit_id.exists'      => 'الوحدة غير موجودة.',

            'quantity.required'   => 'الكمية مطلوبة.',
            'quantity.integer'    => 'الكمية يجب أن تكون رقمًا صحيحًا.',
            'quantity.min'        => 'يجب أن تكون الكمية أكبر من صفر.',
        ];
    }
}
