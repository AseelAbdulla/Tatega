<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCartItemRequest extends FormRequest
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
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.required' => 'الكمية مطلوبة.',
            'quantity.integer'  => 'الكمية يجب أن تكون رقمًا صحيحًا.',
            'quantity.min'      => 'يجب أن تكون الكمية أكبر من صفر.',
        ];
    }
}
