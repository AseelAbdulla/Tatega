<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductImageRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [

            'product_id' => [
                'required',
                'exists:products,id'
            ],


            'image' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,gif,svg',
                'max:2048'
            ],


            'is_main' => [
                'nullable',
                'boolean'
            ],


            'sort_order' => [
                'nullable',
                'integer',
                'min:0'
            ],

        ];
    }

}