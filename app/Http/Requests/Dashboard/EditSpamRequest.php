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

class EditSpamRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function prepareForValidation()
    {
        $keys = array_keys($this->locales);
        $this->merge(['error' => $keys]);
    }

    public function rules()
    {
        return [
            'locales' => 'array|min:1',
            'locales.*' => [
                'required',
                new IsInWebsiteLanguages(),
            ],
            'locales.*.name' => [
                'max:255',
            ],
        ];
    }
}
