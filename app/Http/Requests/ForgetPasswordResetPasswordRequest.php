<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\UserExists;

class ForgetPasswordResetPasswordRequest extends FormRequest
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
     * @return array
     */

     public function prepareForValidation()
     {
        if (preg_match('/^([0-9\s\-\+\(\)]*)$/', $this->login)) {
            $phone = remove_special_chars($this->login);
            if (!strlen($phone)) {
                $this->merge(['phone' => null]);
            } else {
                $this->merge(['phone' => '+' . $phone]);
            }
        } else {
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
          'login' => [
            'required',
            'emailOrPhone',
            'exists:users,' . ($this->phone ? 'phone' : 'email'),
          ],
          'code' => 'required',
          'password' => 'required|passwordRegex',
          'password_confirmation' => 'required_with:password|same:password',
        ];
    }
}
