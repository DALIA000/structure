<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\{
    User,
    Club,
    Federation,
    Academy,
    Player,
    Trainer,
    Influencer,
    Journalist,
    Business,
};

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
    ];

    public function boot()
    {
        $this->registerPolicies();

        // Gate::define('link-player', [PlayerPolicy::class, 'link']);
        Gate::define('academy', function (User $user) {
            return $user->user_type?->user_type === Academy::class;
        });

        Gate::define('federation', function (User $user) {
            return $user->user_type?->user_type === Federation::class;
        });
        
        Gate::define('player', function (User $user) {
            return $user->user_type?->user_type === Player::class;
        });
        
        Gate::define('club', function (User $user) {
            return $user->user_type?->user_type === Club::class;
        });

        Gate::define('trainer', function (User $user) {
            return $user->user_type?->user_type === Trainer::class;
        });

        Gate::define('promote', function (User $user) {
            $user_type = $user->user_type?->user_type;
            return (($user_type === Club::class) || 
                   ($user_type === Federation::class) || 
                   ($user_type === Academy::class) ||
                   ($user_type === Player::class) ||
                   ($user_type === Trainer::class) ||
                   ($user_type === Influencer::class) ||
                   ($user_type === Journalist::class) ||
                   ($user_type === Business::class)) && 
                   ($user?->user?->status_id === 1);
        });
    }
}
