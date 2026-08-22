<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {

        return [

            'category_id' => [
                'required',
                'exists:categories,id'
            ],


            'name_ar' => [
                'required',
                'string',
                'max:255'
            ],


            'name_en' => [
                'required',
                'string',
                'max:255'
            ],


            'description_ar' => [
                'nullable',
                'string'
            ],


            'description_en' => [
                'nullable',
                'string'
            ],


            'sku' => [
                'nullable',
                Rule::unique('products','sku')
                    ->ignore($this->product->id)
            ],


            'base_price' => [
                'required',
                'numeric',
                'min:0'
            ],


            'discount_price' => [
                'nullable',
                'numeric',
                'min:0'
            ],


            'stock' => [
                'required',
                'integer',
                'min:0'
            ],


            'low_stock_threshold' => [
                'nullable',
                'integer',
                'min:0'
            ],


            'status' => [
                'nullable',
                'in:active,inactive'
            ],


            'has_discount' => [
                'nullable',
                'boolean'
            ],

        ];
    }

}