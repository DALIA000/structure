<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\{
    IsInWebsiteLanguages,
    UniqueLocale,
    RequiredLanguages,
};
use App\Models\City;

class CreateCityRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
      $keys = array_keys($this->locales);
      $this->merge(['error' => $keys]);
    }

    public function rules()
    {
        return [
            'country' => 'required|exists:countries,id',
            'status' => 'boolean',
            'locales' => 'required|array|min:1',
            'locales.*' => [
                'required',
                new IsInWebsiteLanguages,
            ],
            'locales.*.name' => [
                'required',
                new UniqueLocale(type: City::class),
                'max:255',
            ],

            'error' => [
                'required',
                new RequiredLanguages,
            ],
        ];
    }
}
