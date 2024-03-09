<?php

namespace App\Http\Requests;

use App\Rules\MediaRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCompetitionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
    }

    public function rules()
    {
        return [
            'title' => 'string|min:3|max:255',
            'description' => [
                $this->style === 2 ? 'nullable' : '',
                'string', 
                'min:10', 
                'max:500'
            ],
            'prize' => 'string|min:10|max:500',
            'days' => 'integer|min:1|max:30',
            'hours' => 'integer|min:0|max:23',
            'winners_count' => 'integer|min:0|max:3',
            'media' => [
                'bail',
                $this->style === 2 ? 'nullable' : '',
                'exists:media,id', 
                new MediaRule(),
            ],
        ];
    }
}
