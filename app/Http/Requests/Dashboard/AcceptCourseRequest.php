<?php

namespace App\Http\Requests\Dashboard;

use App\Rules\MediaRule;
use Illuminate\Foundation\Http\FormRequest;

class AcceptCourseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
    }

    public function rules()
    {
        return [
            // 'cost' => 'required|numeric|min:1|max:99999999',
        ];
    }
}
