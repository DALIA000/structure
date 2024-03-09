<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PinCodeRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(private $type)
    {
        $this->type = $type;
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
        $model = \App\Models\User::all()->where("pin_code.$this->type", $value)->first();
        if ($model) {
            return true;
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __("message.pin code not found");
    }
}
