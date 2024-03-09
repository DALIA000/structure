<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class AdminConfirmPasswordRequest extends FormRequest
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
            'token' => 'required|exists:admins,token',
            'password' => "required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/",
        ];
    }

    public function messages()
    {
        return [
            'token.required' => 'Token is required',
            'token.exists' => 'Token is invalid',
            'password.required' => __('messages.password is required'),
            'password.string' => __('messages.password must be string'),
            'password.min' => __('messages.password must be at least 8 characters'),
            'password.confirmed' => __('messages.password confirmation does not match'),
            'password.regex' => __('messages.password must contain at least one lowercase letter, one uppercase letter, and one digit'),

        ];
    }
}
