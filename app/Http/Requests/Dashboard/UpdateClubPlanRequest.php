<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\{
    IsInWebsiteLanguages,
    RequiredLanguages,
};

class UpdateClubPlanRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $keys = array_keys($this->locales?:[]);
        if ($this->locales) {
            $this->merge(['error' => $keys]);
        }
    }

    public function rules()
    {
        return [
            'club_id' => 'required|exists:clubs,id',
            'plans' => 'array',
            'plans.*.price' => 'required|numeric',

            'plans.*.locales' => 'array|min:1',
            'plans.*.locales.*' => [
                'required',
                new IsInWebsiteLanguages,
            ],
            'plans.*.locales.*.description' => [
                'notNullable',
                'max:500',
            ],
        ];
    }
}
