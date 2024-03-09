<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\{
    IsInWebsiteLanguages,
    UniqueLocale,
    RequiredLanguages,
};
use App\Models\Spam;

class CreateSpamRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function prepareForValidation()
    {
        $keys = array_keys($this->locales??[]);
        $this->merge(['error' => $keys]);
    }

    public function rules()
    {
        return [
            'spam_section' => 'required|exists:spam_sections,id',
            'locales' => 'required|array|min:1',
            'locales.*' => [
                'required',
                new IsInWebsiteLanguages(),
            ],
            'locales.*.name' => [
                'required',
                'max:255',
            ],

            'error' => [
                'required',
                new RequiredLanguages(),
            ],
        ];
    }
}
