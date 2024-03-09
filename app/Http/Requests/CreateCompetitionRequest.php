<?php

namespace App\Http\Requests;

use App\Rules\MediaRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Enums\CompetitionStyle;
use App\Rules\NullableIf;
use Illuminate\Validation\Rules\Enum;

class CreateCompetitionRequest extends FormRequest
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
            'title' => 'required|string|min:3|max:255',
            'description' => [
                'bail',
                $this->style === 2 ? 'nullable' : '',
                'required_if:style,1', 
                'string', 
                'min:10', 
                'max:500'
            ],
            'prize' => 'required|string|min:10|max:500',
            'days' => 'required|integer|min:1|max:30',
            'winners_count' => 'required|integer|min:1|max:3',
            'type' => 'required|boolean',
            'style' => ['required', new Enum(CompetitionStyle::class)],
            'price' => [
                'bail',
                $this->type === 0 ? 'nullable' : '',
                'required_if:type,1',
                'numeric',
                'min:1',
                'max:99999999',
            ],
            'media' => [
                'bail',
                $this->style === 2 ? 'nullable' : '',
                'required_if:style,1', 
                'exists:media,id', 
                new MediaRule(),
            ],
            'options' => [
                'bail',
                $this->style === 1 ? 'nullable' : '',
                'required_if:style,2', 
                'array',
                'max:4'
            ],
            'options.*' => 'array',
            'options.*.value' => 'required|string',
            'options.*.is_right_option' => 'required|boolean',
        ];
    }
}
