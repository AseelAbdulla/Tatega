<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePartnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'name' => 'required|array',
            'logo' => 'nullable|image|max:255',
            'website_url' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'status' => 'nullable|string|max:50',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
        ];
    }
}