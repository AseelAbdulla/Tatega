<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'status' => [
                'sometimes',
                'required',
                'in:pending,approved,rejected',
            ],

            'admin_note' => [
                'sometimes',
                'nullable',
                'string',
                'max:2000',
            ],

            'visitor_name' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'visitor_email' => [
                'sometimes',
                'email',
                'max:255',
            ],

            'rating' => [
                'sometimes',
                'integer',
                'min:1',
                'max:5',
            ],

            'comment' => [
                'sometimes',
                'string',
                'min:3',
                'max:2000',
            ],
        ];
    }
}