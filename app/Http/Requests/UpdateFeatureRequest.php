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
            'title' => 'sometimes|array',
            'title.ar' => 'sometimes|string',
            'title.en' => 'sometimes|string',

            'description' => 'sometimes|array',
            'description.ar' => 'sometimes|string',
            'description.en' => 'sometimes|string',
            'sort_order' => 'nullable|integer',
            'status' => 'nullable|in:active,inactive',
        ];
    }
}