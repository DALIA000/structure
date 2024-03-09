<?php

namespace App\Services\Social;
use Laravel\Socialite\Facades\Socialite;

class Twitter extends Provider
{
    const PROVIDER = 'twitter';

    public static function userFromToken($access_token, $token_secret)
    {
        try {
            $user = Socialite::driver(SELF::PROVIDER)->userFromTokenAndSecret($access_token, $token_secret);

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
