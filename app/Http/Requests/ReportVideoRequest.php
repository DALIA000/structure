<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReportVideoRequest extends FormRequest
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
            'spam_option_id' => [
                'required_without:note',
                Rule::exists('spams', 'id')/* ->where(function ($query) {
                    return $query->where('spam_section_id', 1);
                }) */,
            ],
            'note' => 'required_without:spam_option_id|max:200',
        ];

        return $rules;
    }
}
