<?php

namespace App\Http\Requests\Dashboard;

use App\Rules\IsInWebsiteLanguages;
use App\Rules\RequiredLanguages;
use App\Rules\UniqueLocale;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Country;

class CreateCountryRequest extends FormRequest
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

    protected function prepareForValidation()
    {
        $keys = array_keys($this->locales?:[]);
        $this->merge(['error' => $keys]);
    }

    public function rules()
    {
        return [
            'status' => 'boolean',
            'locales' => 'required|array|min:1',
            'locales.*' => [
                'required',
                new IsInWebsiteLanguages,
            ],
            'locales.*.name' => [
                'required',
                new UniqueLocale(type: Country::class),
                'max:255',
            ],

            'error' => [
                'required',
                new RequiredLanguages,
            ],
        ];
    }
}
