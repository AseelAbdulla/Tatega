<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePartnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'name' => 'nullable|array',
            'logo' => 'sometimes|string|max:255',
            'website_url' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'status' => 'nullable|string|max:50',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
        ];
    }
}