<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }



    public function rules(): array
    {

        $id = $this->route('user');


        return [

            'name' => 'nullable|string|max:255',

            'email' => 'nullable|email|unique:users,email,' . $id,

            'phone' => 'nullable|unique:users,phone,' . $id,

            'password' => 'nullable|min:8',

            'status' => 'nullable|string|max:50',

            'role_id' => 'nullable|exists:roles,id'

        ];
    }



    public function messages(): array
    {
        return [

            'email.unique' => 'Email already exists',

            'phone.unique' => 'Phone number already exists',

            'role_id.exists' => 'Selected role does not exist'

        ];
    }
}
