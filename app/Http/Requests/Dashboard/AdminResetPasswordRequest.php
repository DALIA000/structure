<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class AdminResetPasswordRequest extends FormRequest
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
        ];
    }

    public function messages()
    {
        return [
            'email.required' => __('messages.email is required'),
            'email.email' => __('messages.email is invalid'),
            'email.exists' => __('messages.email is invalid'),
        ];
    }
}
