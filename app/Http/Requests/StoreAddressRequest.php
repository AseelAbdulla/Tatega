<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [

            'user_id' => 'required|exists:users,id',

            'country' => 'required|string|max:100',

            'city' => 'required|string|max:100',

            'region' => 'required|string|max:100',

            'street' => 'required|string|max:255',

            'building' => 'required|string|max:100',

            'notes' => 'nullable|string'

        ];
    }



    /**
     * Custom messages
     */
    public function messages(): array
    {
        return [

            'user_id.required' => 'User is required',

            'user_id.exists' => 'User does not exist',

            'country.required' => 'Country is required',

            'city.required' => 'City is required',

            'region.required' => 'Region is required',

            'street.required' => 'Street is required',

            'building.required' => 'Building is required'

        ];
    }
}
