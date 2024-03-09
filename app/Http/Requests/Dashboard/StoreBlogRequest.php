<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlogRequest extends FormRequest
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
            "locales" => "required|array",
            "locales.*" => ["required", new \App\Rules\InLangRule()],
            "locales.*.name" => "required|string",
            "locales.*.description" => "required|string",
            "status" => "nullable|in:active,inactive",
            "media" => ["required", "exists:media,id", new \App\Rules\MediaRule()],
            "tags" => "required|array",
            "tags.*" => "required_with:tags|exists:tags,id",
        ];
    }

    public function messages()
    {
        return [
            "locales.*.name.required" => __("messages.name is required"),
            "locales.*.name.string" => __("messages.name is not a string"),
            "locales.*.title.required" => __("messages.title is required"),
            "locales.*.title.string" => __("messages.title is not a string"),
            "locales.*.description.required" => __("messages.description is required"),
            "locales.*.description.string" => __("messages.description is not a string"),
            "status.required" => __("messages.status is required"),
            "status.in" => __("messages.status is not in active,inactive"),
            "media.required" => __("messages.media is required"),
        ];
    }

}
