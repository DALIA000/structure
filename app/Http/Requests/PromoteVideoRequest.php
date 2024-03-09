<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PromoteVideoRequest extends FormRequest
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
            // 'starts_at' => 'required|date',
            // 'ends_at' => 'required|date|after:starts_at',
            'duration' => 'numeric|min:1|max:30',
            'user_types' => 'nullable|array|distinct',
            'user_types.*' => 'exists:user_types,id',
            'cities' => 'nullable|array|distinct',
            'cities.*' => 'exists:cities,slug',
        ];

        return $rules;
    }
}
