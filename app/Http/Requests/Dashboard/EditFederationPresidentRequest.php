<?php

namespace App\Http\Requests\Dashboard;

use App\Rules\MediaRule;
use Illuminate\Foundation\Http\FormRequest;

class EditFederationPresidentRequest extends FormRequest
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
