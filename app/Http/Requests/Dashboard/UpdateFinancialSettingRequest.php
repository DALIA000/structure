<?php

namespace App\Http\Requests\Dashboard;

use App\Rules\MediaRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFinancialSettingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'slug' => $this->slug
        ]);
    }

    public function rules()
    {
        return [
            'slug' => 'required|exists:financial_settings,slug',
            'value' => 'numeric|min:1|max:99999999',
        ];
    }
}
