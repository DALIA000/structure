<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SocialCheckRequest extends FormRequest
{
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

    public function rules()
    {
        $social_auth = app('social_auth') ?? [];

        return [
          'social' => 'required|in:' . implode(',', $social_auth),
          'access_token' => 'required|string',
          'token_secret' => 'required_if:social,twitter',
        ];
    }
}
