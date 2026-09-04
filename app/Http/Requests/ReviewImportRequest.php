<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ReviewImportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'action'               => 'required|in:approve,reject',
            'rejection_reason'     => 'required_if:action,reject|nullable|string',
            'shipping_method'      => 'required_if:action,approve|nullable|string',
            'offered_shipping_fee' => 'required_if:action,approve|nullable|numeric|min:0',
            'offer_expires_at'     => 'required_if:action,approve|nullable|date|after:now',
            'items'                => 'required_if:action,approve|array',
            'items.*.id'           => 'required_with:items|exists:import_request_items,id',
            'items.*.offered_unit_price' => 'required_with:items|numeric|min:0',
        ];
    }
}
