<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\{
    IsInWebsiteLanguages,
    UniqueLocale,
};
use App\Models\City;
use Str;

class EditCityRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
          'country' => 'exists:countries,id',
          'status' => 'boolean',
          'locales' => 'array|min:1',
          'locales.*' => [
            'required',
            new IsInWebsiteLanguages(),
          ],
          'locales.*.name' => [
            new UniqueLocale(City::class, $this->id, 'localizable_id'),
            'max:255',
          ],
    ];
    }
}
