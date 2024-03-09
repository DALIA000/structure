<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserLoginRequest extends FormRequest
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

    public function prepareForValidation()
    {
        if ($this->exists('login') && preg_match('/^([0-9\s\-\+\(\)]*)$/', $this->login)) {
            $this->merge(['phone' => '+' . remove_special_chars($this->login)]);
        }

        if ($this->exists('login') && preg_match('/(.+)@(.+)\.(.+)/i', $this->login)) {
            $this->merge(['email' => $this->login]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
          'login' => 'required|emailOrPhone',
          'password' => 'required'
        ];
    }
}
