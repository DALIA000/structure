<?php

namespace App\Http\Requests\Dashboard;

use App\Rules\IsInWebsiteLanguages;
use App\Rules\UniqueLocale;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Country;

class EditCountryRequest extends FormRequest
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
            'status' => 'boolean',
            'locales' => 'array|min:1',
            'locales.*' => [
                'required',
                new IsInWebsiteLanguages(),
            ],
            'locales.*.name' => [
                new UniqueLocale(Country::class, $this->id, 'localizable_id'),
                'max:255',
            ],
        ];
    }
}
