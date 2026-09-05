<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInternalNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->roles->contains('name', 'admin');
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'title' => ['required'], // إزالة شرط 'array' ليعمل مع النصوص والمصفوفات
            'message' => ['required'], // إزالة شرط 'array'
            'type' => ['required', 'string', 'max:100'],
            'sent_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'User is required.',
            'user_id.exists' => 'The selected user does not exist.',
            'title.required' => 'Title is required.',
            'title.array' => 'Title must be an array.',
            'message.required' => 'Message is required.',
            'message.array' => 'Message must be an array.',
            'type.required' => 'Notification type is required.',
            'type.string' => 'Notification type must be a string.',
            'sent_at.date' => 'Sent date must be a valid date.',
        ];
    }
}
