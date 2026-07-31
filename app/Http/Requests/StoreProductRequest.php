<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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


            'sku' => [
                'required',
                'string',
                'unique:products,sku'
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