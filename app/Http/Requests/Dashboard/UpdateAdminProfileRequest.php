<?php

namespace App\Http\Requests\Dashboard;

use App\Rules\AdminCurrentPassword;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminProfileRequest extends FormRequest
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
    public function rules()
    {
        $id = user()->id;
        return [
            'name' => 'string|max:255',
            'email' => 'string|max:255|email|unique:admins,email,' . $id,
            'username' => 'alpha_dash|max:255|unique:admins,username,' . $id,
            'password' => "min:8|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/|confirmed",
            'current_password' => ['required_with:password', new AdminCurrentPassword()],
            'role' => 'exists:roles,id',
            "country" => "exists:countries,id",
        ];
    }

    public function messages()
    {
        return [
            'name.string' => __('message.name must be string'),
            'name.max' => __('message.name must be less than 255 characters'),
            'email.string' => __('message.email must be string'),
            'email.email' => __('message.email is invalid'),
            'email.max' => __('message.email must be less than 255 characters'),
            'email.unique' => __('message.email is already taken'),
            'username.alpha_dash' => __('message.username must be alphanumeric'),
            'username.max' => __('message.username must be less than 255 characters'),
            'username.unique' => __('message.username is already taken'),
            'password.min' => __('message.password must be at least 8 characters'),
            'password.regex' => __('message.password must contain at least one lowercase letter, one uppercase letter, and one digit and one special characters'),
            'password.confirmed' => __('message.password confirmation does not match'),
            'current_password.required_with' => __('message.current password is required'),
            'role.exists' => __('message.role is invalid'),
            'country.exists' => __('message.country is invalid'),
        ];
    }
}
