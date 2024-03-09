<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class ReportMessageRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
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

