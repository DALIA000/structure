<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PlanExistRule implements Rule
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
        $plans = \App\Models\Plan::where([
            "id" => $value,
            "service_id" => request()->id,
        ])->first();
        if ($plans) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'this plan is not belong to this service';
    }
}
