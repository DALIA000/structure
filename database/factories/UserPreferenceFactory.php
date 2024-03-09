<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserPreferenceFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => 1,
            'preference_id' => 1,
            'value' => 1,
        ];
    }
    public function unverified()
    {
    }
}
