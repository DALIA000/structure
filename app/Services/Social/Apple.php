<?php

namespace App\Services\Social;
use Laravel\Socialite\Facades\Socialite;

class Apple extends Provider
{
    const SCOPES = [
        'name', 
        'email'
    ];

    const PROVIDER = 'sign-in-with-apple';

    public static function userFromToken($access_token)
    {
        try {
            $user = Socialite::driver(SELF::PROVIDER)->scopes(SELF::SCOPES)->userFromToken($access_token);

            $name = explode(' ', $user->name);
            return [
                'id' => $user->id,
                'first_name' => isset($name[0]) && $name[0] ? $name[0] : null, 
                'last_name' => isset($name[1]) && $name[1] ? $name[1] : null, 
                'email' => $user->email,
                'phone' => $user->phone,
                'birthday' => $user->birthday,
            ];

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
