<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductUnitRequest extends FormRequest
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


            'unit_name_ar' => [
                'required',
                'string',
                'max:255'
            ],


            'unit_name_en' => [
                'required',
                'string',
                'max:255'
            ],


            'price' => [
                'required',
                'numeric',
                'min:0'
            ],


            'stock' => [
                'nullable',
                'integer',
                'min:0'
            ],

        ];
    }

}