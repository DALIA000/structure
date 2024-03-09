<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidUserPasswordRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        if (!request()->login) {
            return false;
        } else {
            $login = request()->login;

            $user = \App\Models\User::where('email', $login)->orWhere('username', $login)->first();

            if ($user) {

                return \Illuminate\Support\Facades\Hash::check($value, $user->password);

            } else {
                return false;
            }
        }

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __("message.invalid password");
    }
}
