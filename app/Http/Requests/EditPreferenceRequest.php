<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditPreferenceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function prepareForValidation()
    {
        $this->merge([
            'slug' => $this->slug,
        ]);
    }

    public function rules()
    {
        return [
            'slug' => 'required|exists:preferences,slug',
            'value' => 'required',
        ];
    }
}
