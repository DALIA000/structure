<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class UploadMediaRequest extends FormRequest
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
        return [
            'media' => 'media',
            'media' => 'array',
            'media.*' => 'file',
            "link" => "url",
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'media.media' => 'The media must be a file',
            'media.array' => 'The media must be an array',
            'media.*.file' => 'The media must be files',
            'link.url' => 'The link must be a valid url',

        ];
    }
}
