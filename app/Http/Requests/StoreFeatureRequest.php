<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeatureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'icon' => 'required|string|max:100',
            'title' => 'required|array',
            'description' => 'required|array',
            'sort_order' => 'nullable|integer',
            'status' => 'nullable|string|max:50',
        ];
    }
}