<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Rule as RuleValidation;
use App\Models\Permission;

class RequiredPermissions implements Rule
{
    public $permissions;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($permissions = [])
    {
        $this->permissions = $permissions;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $permissions = Permission::select(['id', 'required_permissions_slugs'])->where('id', $value);
        $required_permissions_slugs = $permissions->get()->pluck('required_permissions_slugs')->flatten()->unique()->toArray();
        $required_permissions_arr = Permission::select(['id'])->whereIn('name', $required_permissions_slugs)->get()->toArray();
        $required_permissions_ids = collect($required_permissions_arr)->flatten()->unique()->toArray();

        $intersect = array_intersect($this->permissions, $required_permissions_ids);
        $this->permission = Permission::find($value)?->locale?->name;
        $this->required_permissions = Permission::select(['permissions.id', 'locales.name', 'locales.group'])
                                                  ->whereIn('permissions.id', $required_permissions_ids)
                                                  ->join('locales', 'localizable_id', 'permissions.id')
                                                  ->where(['localizable_type' => Permission::class, 'locale' => app()->getLocale()])
                                                  ->get()
                                                  ->map(fn ($i) => $i['name'] . ' ' . $i['group'])
                                                  ->toArray();
        return count($intersect) === count($required_permissions_ids);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {

        return trans('messages.permission requires:') . implode(', ', $this->required_permissions);
    }
}
