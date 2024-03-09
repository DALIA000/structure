<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class EditSoundRequest extends FormRequest
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
            'sound' => 'file|mimes:mp3,wav',
            'title' => 'max:255',
            'image' => 'image',
            'singer' => 'max:255',
        ];

        return $rules;
    }
}
