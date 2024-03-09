<?php

namespace App\Services\Social;
use Laravel\Socialite\Facades\Socialite;

class Facebook extends Provider
{
    const FIELDS = [
        'id', 
        'first_name', 
        'last_name', 
        'birthday', 
        'email', 
    ];

    const PROVIDER = 'facebook';

    public static function userFromToken($access_token)
    {
        try {
            $user = Socialite::driver(SELF::PROVIDER)->fields(SELF::FIELDS)->userFromToken($access_token);
            return [
                'id' => $user->id,
                'first_name' => $user->user['first_name'] ?? null,
                'last_name' => $user->user['last_name'] ?? null,
                'email' => $user->user['email'] ?? null,
                'phone' => $user->user['phone'] ?? null,
                'birthday' => $user->user['birthday'] ?? null,
            ];

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
