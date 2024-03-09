<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\{
    Federation,
};
use Illuminate\Validation\Rule;
use App\Rules\MediaRule;

class EditFederationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function prepareForValidation()
    {
        if ($this->exists('phone') && preg_match('/^([0-9\s\-\+\(\)]*)$/', $this->phone)) {
            $phone = remove_special_chars($this->phone);
            if (!strlen($phone)) {
                $this->merge(['phone' => null]);
            } else {
                $this->merge(['phone' => '+' . $phone]);
            }
        }
    }

    public function rules()
    {
        $account = Federation::find($this->id)?->account;

        if (!$account) {
            return [
                'user' => 'exists:users,id'
            ];
        }

        $rules = [
            'business_name' => 'notNullable|min:2|business_name',
            'latitude' => 'notNullable|between:-90,90',
            'longitude' => 'notNullable|between:-180,180',
            'username' => 'notNullable|slug|unique:users,username,'.$account->id,
            'email' => 'notNullable|emailRegex|unique:users,email,'.$account->id,
            'phone' => 'notNullable|phone|unique:users,phone,'.$account->id,
            'password' => 'notNullable|min:8|passwordRegex',
            'password_confirmation' => 'notNullable|same:password',
            'birthday' => 'notNullable|date',
            'media' => [
                'bail',
                'nullable',
                'exists:media,id',
                new MediaRule(Federation::class),
            ],
        ];

        return $rules;
    }
}
