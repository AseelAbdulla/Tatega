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
            'product_id' => 'sometimes|exists:products,id',
            'user_id' => 'nullable|exists:users,id',
            'visitor_name' => 'nullable|string|max:255',
            'rating' => 'sometimes|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'status' => 'nullable|string|max:50',
        ];
    }
}