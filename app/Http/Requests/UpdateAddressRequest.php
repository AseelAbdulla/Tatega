<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
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

            'user_id' => 'nullable|exists:users,id',

            'title' => 'nullable|string|max:100',

            'city' => 'nullable|string|max:100',

            'address' => 'nullable|string|max:255',

            'phone' => 'nullable|string|max:20',

            'is_default' => 'nullable|boolean'

        ];
    }


    /**
     * Custom messages
     */
    public function messages(): array
    {
        return [

            'user_id.exists' => 'User does not exist',

            'title.max' => 'Title is too long',

            'city.max' => 'City name is too long'

        ];
    }
}
