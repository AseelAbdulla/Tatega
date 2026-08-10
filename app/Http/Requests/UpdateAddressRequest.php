<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'country' => ['sometimes', 'string', 'max:100'],
            'city' => ['sometimes', 'string', 'max:100'],
            'region' => ['sometimes', 'string', 'max:100'],
            'street' => ['sometimes', 'string', 'max:255'],
            'building' => ['sometimes', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'country.max' => 'Country name is too long',
            'city.max' => 'City name is too long',
            'region.max' => 'Region name is too long',
            'street.max' => 'Street is too long',
            'building.max' => 'Building is too long',
        ];
    }
}