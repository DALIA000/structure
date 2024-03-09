<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Language;

class RequiredLanguages implements Rule
{
    public function __construct()
    {
    }

    public function passes($attribute, $value)
    {
        $languages = Language::all()->pluck('slug')->toArray();
        return !count(array_diff($languages, $value));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('messages.languages are required');
    }
}
