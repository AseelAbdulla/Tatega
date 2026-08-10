<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInternalNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->roles->contains('name', 'admin');
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'array'],
            'message' => ['nullable', 'array'],
            'type' => ['nullable', 'string', 'max:100'],
            'is_read' => ['nullable', 'boolean'],
            'sent_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.array' => 'Title must be an array.',
            'message.array' => 'Message must be an array.',
            'type.string' => 'Notification type must be a string.',
            'is_read.boolean' => 'Read status must be true or false.',
            'sent_at.date' => 'Sent date must be a valid date.',
        ];
    }
}