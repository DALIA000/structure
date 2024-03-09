<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmTokenRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email' => 'required|email|exists:admins,email',
            'pin_code' => 'required|string|min:6|max:6',

        ];
    }

    public function messages()
    {
        return [
            'email.required' => __('validation.required'),
            'email.email' => __('validation.email'),
            'email.exists' => __('validation.exists'),

            'pin_code.required' => __('messages.pin code is required'),
            'pin_code.string' => __('messages.pin code is invalid'),
            'pin_code.min' => __('messages.pin code is invalid'),
            'pin_code.max' => __('messages.pin code is invalid'),
        ];
    }
}
