<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\MediaRule;
use App\Models\ClubPresident;

class EditFederationPresidentRequest extends FormRequest
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
            'full_name' => 'notNullable|min:2|alpha_spaceable',
            'media' => [
                'bail',
                'nullable',
                'exists:media,id', 
                new MediaRule(ClubPresident::class),
            ],
        ];

        return $rules;
    }
}
