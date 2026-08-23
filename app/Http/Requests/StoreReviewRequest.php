<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'visitor_name' => [
                'required',
                'string',
                'max:255',
            ],

            'visitor_email' => [
                'required',
                'email',
                'max:255',
            ],

            'rating' => [
                'required',
                'integer',
                'min:1',
                'max:5',
            ],

            'comment' => [
                'required',
                'string',
                'min:3',
                'max:2000',
            ],

            'product_id' => [
                'nullable',
                'exists:products,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'visitor_name.required' =>
                'الاسم مطلوب.',

            'visitor_name.string' =>
                'الاسم يجب أن يكون نصًا.',

            'visitor_name.max' =>
                'الاسم طويل جدًا.',

            'visitor_email.required' =>
                'البريد الإلكتروني مطلوب.',

            'visitor_email.email' =>
                'البريد الإلكتروني غير صحيح.',

            'visitor_email.max' =>
                'البريد الإلكتروني طويل جدًا.',

            'rating.required' =>
                'الرجاء اختيار التقييم.',

            'rating.integer' =>
                'التقييم غير صحيح.',

            'rating.min' =>
                'يجب اختيار نجمة واحدة على الأقل.',

            'rating.max' =>
                'لا يمكن أن يزيد التقييم عن 5 نجوم.',

            'comment.required' =>
                'التعليق مطلوب.',

            'comment.string' =>
                'التعليق يجب أن يكون نصًا.',

            'comment.min' =>
                'التعليق قصير جدًا.',

            'comment.max' =>
                'التعليق طويل جدًا.',

            'product_id.exists' =>
                'المنتج المحدد غير موجود.',
        ];
    }
}