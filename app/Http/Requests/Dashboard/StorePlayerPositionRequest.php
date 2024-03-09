<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class StorePlayerPositionRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "locales" => "required|array",
            "locales.*" => ["required", new \App\Rules\InLangRule()],
            "locales.*.name" => "required|string",
        ];
    }

    public function messages()
    {
        return [
            "locales.*.name.required" => __("messages.name is required"),
            "locales.*.name.string" => __("messages.name is not a string"),
            "locales.*.name.max" => __("messages.max 255 characters"),
        ];
    }
}
