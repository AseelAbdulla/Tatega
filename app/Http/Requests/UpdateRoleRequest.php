<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }



    public function rules(): array
    {
        return [

            'name' => 'nullable|string|max:100|unique:roles,name,'.$this->route('role'),

            'display_name' => 'nullable|array'

        ];
    }



    public function messages(): array
    {
        return [

            'name.unique' => 'This role already exists.',

            'display_name.array' => 'Display name must be an array.'

        ];
    }

}
