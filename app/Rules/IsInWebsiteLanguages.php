<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Language;

class IsInWebsiteLanguages implements Rule
{
    public function __construct()
    {
    }

    public function passes($attribute, $value)
    {
        $attr = substr($attribute, strpos($attribute, 'locales'));
        $languages = Language::all()->pluck('slug')->toArray();
        $lang = explode(".", $attr)[1];
        return in_array($lang, $languages);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.custom.IsInWebsiteLanguages');
    }
}
