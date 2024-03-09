<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBlogRequest extends FormRequest
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
            "locales.*" => ["", new \App\Rules\InLangRule()],
            "locales.*.name" => "string",
            "locales.*.description" => "string",
            "status" => "in:active,inactive",
            "media" => [ "exists:media,id", new \App\Rules\MediaRule()],
            "tags" => "array",
            "tags.*" => "required_with:tags|exists:tags,id",
        ];
    }

    public function messages()
    {
        return [
            "locales.*.name.string" => __("messages.name is not a string"),
            "locales.*.title.string" => __("messages.title is not a string"),
            "locales.*.description.string" => __("messages.description is not a string"),
            "status.in" => __("messages.status is not in active,inactive"),
        ];
    }

}
