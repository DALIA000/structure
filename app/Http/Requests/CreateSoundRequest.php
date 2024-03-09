<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateSoundRequest extends FormRequest
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
            'path' => 'required|file|mimes:mp3,wav',
            'title' => 'required|string|max:255',
            'image' => 'required|image',
            'singer' => 'required|string|max:255',
        ];

        return $rules;
    }
}
