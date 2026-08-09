
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                'unique:roles,name',
            ],

            'display_name' => [
                'required',
                'array',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Role name is required.',
            'name.string' => 'Role name must be a string.',
            'name.max' => 'Role name may not be greater than 100 characters.',
            'name.unique' => 'This role already exists.',

            'display_name.required' => 'Display name is required.',
            'display_name.array' => 'Display name must be an array.',
        ];
    }
}

