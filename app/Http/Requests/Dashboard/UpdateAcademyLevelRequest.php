<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAcademyLevelRequest extends FormRequest
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

    public function rules()
    {
        return [
            "locales" => "array",
            "locales.*" => [new \App\Rules\InLangRule()],
            "locales.*.name" => "string|max:255",
        ];
    }

    public function messages()
    {
        return [
            "locales.*.name.string" => __("messages.name is not a string"),
            "locales.*.name.max" => __("messages.max 255 characters"),
        ];
    }
}
