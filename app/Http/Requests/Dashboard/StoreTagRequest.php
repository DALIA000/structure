<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class StoreTagRequest extends FormRequest
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
            'name' => "required|string|unique:tags,name",
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('messages.name is required'),
            'name.string' => __('messages.name must be string'),
            'name.unique' => __('messages.name must be unique'),
        ];
    }
}
