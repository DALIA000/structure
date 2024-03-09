<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateUserCompetitionOptionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'competition_id' => $this->id
        ]);
    }

    public function rules()
    {
        return [
            'option_id' => [
                'required', 
                Rule::exists('competition_options', 'id')->where('competition_id', $this->competition_id)
            ],
        ];
    }
}
