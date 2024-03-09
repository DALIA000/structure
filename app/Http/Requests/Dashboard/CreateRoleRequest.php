<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\IsInWebsiteLanguages;
use App\Models\Role;
use Illuminate\Validation\Rule;
use App\Rules\RequiredLanguages;
use App\Rules\RequiredPermissions;
use App\Rules\UniqueLocale;

class CreateRoleRequest extends FormRequest
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
        $keys = array_keys($this->locales ?: []);
        $this->merge(['error' => $keys]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'locales' => 'required|array|min:1',
            'locales.*' => [
                'required',
                new IsInWebsiteLanguages(),
            ],
            'locales.*.name' => [
                'required',
                new UniqueLocale(Role::class),
                'max:255',
            ],
                'permissions' => 'required|array|min:1',
                'permissions.*' => [
                'distinct',
                'exists:permissions,id',
                new RequiredPermissions($this->permissions)
            ],
            'error' => [
                'required',
                new RequiredLanguages(),
            ],
        ];
    }

    public function messages()
    {
        return [
          'permissions.array' => trans('validation.required'),
          'permissions.min' => trans('messages.you should choose at least one permission'),
        ];
    }
}
