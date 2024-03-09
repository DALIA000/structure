<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function prepareForValidation()
    {
    }

    public function rules()
    {
        return [
            'current_password' => 'required',
            'password' => 'required|passwordRegex',
            'password_confirmation' => 'required|same:password',
        ];
    }
}
