
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roleId = $this->route('role');

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                Rule::unique('roles', 'name')->ignore($roleId),
            ],

            'display_name' => [
                'sometimes',
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

