<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\IsInWebsiteLanguages;
use App\Models\Role;
use App\Rules\UniqueLocale;
use App\Rules\RequiredPermissions;

class EditRoleRequest extends FormRequest
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
        return [
            'locales' => 'array|min:1',
            'locales.*' => [
                'required',
                new IsInWebsiteLanguages(),
            ],
            'locales.*.name' => [
                'nullable',
                new UniqueLocale(Role::class, $this->id, 'localizable_id'),
                'max:255',
            ],
            'permissions' => 'array|min:1',
            'permissions.*' => [
              'distinct',
              'exists:permissions,id',
              new RequiredPermissions($this->permissions)
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
