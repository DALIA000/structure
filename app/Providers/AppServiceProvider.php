<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use App\Services\LoggedinUser;
use App\Services\ResponseService;
use Str;


class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    if ($this->app->environment('local')) {
        $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
        $this->app->register(TelescopeServiceProvider::class);
    }

    $this->app->singleton('loggedinUser', fn () => LoggedinUser::user());
    $this->app->singleton('social_auth', fn () => ['google','facebook','twitter','apple']);
    $this->app->bind(ResponseService::class, fn () => new ResponseService() );
  }

  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {
    Validator::extend('emailRegex', function ($attribute, $value, $parameters, $validator) {
      return preg_match('/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/', $value);
    });

    /* Validator::extend('latRegex', function ($attribute, $value, $parameters, $validator) {
      return preg_match('/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/', $value);
    }); */

    Validator::extend('slug', function ($attribute, $value, $parameters, $validator) {
      return Str::slug($value, '') === $value;
    });

    Validator::extend('phone', function ($attribute, $value, $parameters, $validator) {
      return preg_match('/^([0-9\s\-\+\(\)]*)$/', $value) && (Str::length($value) >= 10);
    });

    Validator::extend('emailOrPhone', function ($attribute, $value, $parameters, $validator) {
      return preg_match('/^([0-9\s\-\+\(\)]*)$/', $value) || preg_match('/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/', $value);
    });

    Validator::extend('passwordRegex', function ($attribute, $value, $parameters, $validator) {
      return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?#&])[A-Za-z\d@$!%*?#&]{8,}$/', $value);
    });

    Validator::extend('coupon', function ($attribute, $value, $parameters, $validator) {
      return preg_match('/^([0-9a-zA-Z-]+)$/', $value);
    });

    Validator::extend('business_name', function ($attribute, $value, $parameters, $validator) {
      return preg_match('/^([0-9a-zA-Z\- ]*)$/', $value);
    });

    Validator::extend('alpha_spaceable', function ($attribute, $value, $parameters, $validator) {
      return preg_match('/^([a-zA-Z\- ]*)$/', $value);
    });

    Validator::extend('notNullable', function ($attribute, $value, $parameters, $validator) {
        return strlen($value) > 0;
      });
  }
}
