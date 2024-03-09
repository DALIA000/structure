<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerificationCodeRequest extends FormRequest
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
            'exists:users,' . ($this->phone ? 'phone' : 'email'),
          ],
        ];
    }
}
