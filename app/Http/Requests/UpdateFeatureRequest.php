<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFeatureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'icon' => 'sometimes|string|max:100',
            'title' => 'nullable|array',
            'description' => 'nullable|array',
            'sort_order' => 'nullable|integer',
            'status' => 'nullable|string|max:50',
        ];
    }
}