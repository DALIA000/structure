<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LinkPlayerRequest extends FormRequest
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
            'strength_points' => 'nullable|string',
        ];

        return $rules;
    }
}
