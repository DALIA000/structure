<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\{
    Country,
};
use Illuminate\Validation\Rule;
use App\Rules\MediaRule;

class CreateClubRequest extends FormRequest
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
        
        $rules = [
            'business_name' => 'required|min:2|business_name',
            'latitude' => 'required|between:-90,90',
            'longitude' => 'required|between:-180,180',
            'description' => 'nullable|string|max:500',
            'username' => 'required|slug|unique:users,username',
            'email' => 'required|emailRegex|unique:users,email',
            'phone' => 'required|phone|unique:users,phone',
            'password' => 'required|min:8|passwordRegex',
            'password_confirmation' => 'required|same:password',
            'birthday' => 'required|date',
            'country' => 'required|exists:countries,slug',
            'city' => [
                'required',
                Rule::exists('cities', 'slug')->where(function ($query) {
                    $country = Country::where('slug', $this->country)?->first();
                    $query->where('country_id', $country?->id);
                }),
            ],
            'media' => [
                'bail',
                'required',
                'exists:media,id', 
                new MediaRule(),
            ],
        ];

        return $rules;
    }
}
