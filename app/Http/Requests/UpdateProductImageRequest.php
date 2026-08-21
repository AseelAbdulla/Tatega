<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'product_id' => [
                'sometimes',
                'exists:products,id'
            ],

            'image' => [
                'sometimes',
                'image',
                'mimes:jpeg,png,jpg,gif,svg',
                'max:2048'
            ],

            'is_main' => [
                'sometimes',
                'boolean'
            ],

            'sort_order' => [
                'sometimes',
                'integer',
                'min:0'
            ],

        ];
    }
}