<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }



    public function rules(): array
    {
        return [

            'country' => 'nullable|string|max:100',

            'city' => 'nullable|string|max:100',

            'region' => 'nullable|string|max:100',

            'street' => 'nullable|string|max:255',

            'building' => 'nullable|string|max:100',

            'notes' => 'nullable|string'

        ];
    }



    public function messages(): array
    {
        return [

            'country.string' => 'Country must be text',

            'city.string' => 'City must be text',

            'region.string' => 'Region must be text',

            'street.string' => 'Street must be text',

            'building.string' => 'Building must be text'

        ];
    }
}
