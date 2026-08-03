<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInternalNotificationRequest extends FormRequest
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

            'title' => 'required|array',

            'message' => 'required|array',

            'type' => 'required|string|max:100',

            'is_read' => 'nullable|boolean',

            'sent_at' => 'nullable|date'

        ];
    }





    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [

            'user_id.exists' => 'The selected user does not exist.',

            'title.required' => 'Title is required.',

            'title.array' => 'Title must be an array.',

            'message.required' => 'Message is required.',

            'message.array' => 'Message must be an array.',

            'type.required' => 'Notification type is required.',

            'type.string' => 'Notification type must be a string.',

            'is_read.boolean' => 'Read status must be true or false.',

            'sent_at.date' => 'Sent date must be a valid date.'

        ];
    }
}
