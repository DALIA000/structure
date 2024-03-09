<?php

namespace App\Http\Requests;

use App\Models\ClubAchievment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\MediaRule;

class EditClubAchievmentRequest extends FormRequest
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
            'title' => 'notNullable|min:2|string',
            'year' => 'notNullable|date_format:Y',
            'media' => [
                'bail',
                'nullable',
                'exists:media,id', 
                new MediaRule(ClubAchievment::class),
            ],
        ];

        return $rules;
    }
}
