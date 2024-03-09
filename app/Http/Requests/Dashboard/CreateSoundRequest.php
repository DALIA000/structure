<?php

namespace App\Http\Requests\Dashboard;

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
            'sound' => 'required|file|mimes:mp3,wav',
            'title' => 'required|max:255',
            'image' => 'required|image',
            'singer' => 'required|max:255',
        ];

        return $rules;
    }
}
