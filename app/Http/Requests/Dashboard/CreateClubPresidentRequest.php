<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\MediaRule;

class CreateClubPresidentRequest extends FormRequest
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
            'full_name' => 'required|min:2|alpha_spaceable',
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
