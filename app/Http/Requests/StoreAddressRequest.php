<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'country' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'region' => ['required', 'string', 'max:100'],
            'street' => ['required', 'string', 'max:255'],
            'building' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'country.required' => 'Country is required',
            'city.required' => 'City is required',
            'region.required' => 'Region is required',
            'street.required' => 'Street is required',
            'building.required' => 'Building is required',
        ];
    }
}