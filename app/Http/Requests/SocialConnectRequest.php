<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SocialConnectRequest extends FormRequest
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
        if ($this->social != null) {
            $this->merge(['social' => $this->social]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $loggedinUser = app('loggedinUser');
        $social_auth = app('social_auth') ?? [];

        return [
          'social' => 'required|in:' . implode(',', $social_auth),
          'email' => [
            'required', 
            'emailRegex',
            Rule::unique('social_auth', $this->social . '_email'), //->ignore($loggedinUser->id),
        ],
          'id' => [
            'required',
            'numeric',
            Rule::unique('social_auth', $this->social), //->ignore($loggedinUser->id),
          ]
        ];
    }

    public function messages()
    {
        return [
          'id.unique' => trans('auth.socialAlreadyConnected', ['social' => $this->social]),
        ];
    }
}
