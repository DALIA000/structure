<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class EditAdminRrquest extends FormRequest
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
        $rules = [
            'name' => 'min:2|unique:admins,name',
            'email' => 'email|unique:admins,email',
            'username' => 'unique:admins,username',
            'password' => 'min:8',
            'password_confirmation' => 'same:password',
        ];
        return $rules;
    }
}
