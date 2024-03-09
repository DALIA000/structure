<?php

namespace App\Services\Social;
use Laravel\Socialite\Facades\Socialite;

class Google extends Provider
{
    const PROVIDER = 'google';

    public static function userFromToken($access_token)
    {
        try {
            $user = Socialite::driver(SELF::PROVIDER)->userFromToken($access_token);

            return [
                'id' => $user->id,
                'first_name' => $user->user['given_name'] ?? null,
                'last_name' => $user->user['family_name'] ?? null,
                'email' => $user->user['email'] ?? null,
                'phone' => $user->user['phone'] ?? null,
                'birthday' => $user->user['birthday'] ?? null,
            ];

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
