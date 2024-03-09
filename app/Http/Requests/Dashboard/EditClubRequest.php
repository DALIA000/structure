<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\{
    Country,
    Club,
};
use Illuminate\Validation\Rule;
use App\Rules\MediaRule;

class EditClubRequest extends FormRequest
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
        $account = Club::find($this->id)?->account;
        if (!$account) {
            return [
                'user' => 'exists:users,id'
            ];
        }
        $rules = [
            'business_name' => 'notNullable|min:2|business_name',
            'latitude' => 'notNullable|between:-90,90',
            'longitude' => 'notNullable|between:-180,180',
            'description' => 'nullable|string|max:500',
            'username' => 'notNullable|slug|unique:users,username,'.$account->id,
            'email' => 'notNullable|emailRegex|unique:users,email,'.$account->id,
            'phone' => 'notNullable|phone|unique:users,phone,'.$account->id,
            'password' => 'notNullable|min:8|passwordRegex',
            'password_confirmation' => 'notNullable|same:password',
            'birthday' => 'notNullable|date',
            'country' => 'notNullable|exists:countries,slug',
            'city' => [
                'notNullable',
                Rule::exists('cities', 'slug')->where(function ($query) use ($account) {
                    $country = $this->country ? Country::where('slug', $this->country)?->first() : $account->city?->country;
                    $query->where('country_id', $country?->id);
                }),
            ],
            'media' => [
                'bail',
                'nullable',
                'exists:media,id',
                new MediaRule(Club::class),
            ],
        ];

        return $rules;
    }
}
