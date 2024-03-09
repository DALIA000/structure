<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class EditAdminRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        $id = $this->id;
        return [
            'name' => 'max:255',
            'username' => 'max:255|unique:admins,username,'.$this->id,
            'email' => 'emailRegex|unique:admins,email,'.$this->id,
            'password' => 'string|passwordRegex',
            'role' => 'exists:roles,id',
        ];
    }
}
