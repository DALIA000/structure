<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\MediaRule;

class CreateClubAchievmentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function prepareForValidation()
    {
    }

    public function rules()
    {
        
        $rules = [
            'title' => 'required|min:2|string',
            'year' => 'required|date_format:Y',
            'media' => [
                'bail',
                'nullable',
                'exists:media,id', 
                new MediaRule(),
            ],
        ];

        return $rules;
    }
}
