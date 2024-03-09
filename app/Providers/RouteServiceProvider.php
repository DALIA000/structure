<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

/**
 * @suppress PHP0404
 */

class RouteServiceProvider extends ServiceProvider
{
  public const HOME = '/';

  public function boot()
  {
    $this->configureRateLimiting();

    $this->routes(function () {
      Route::prefix(env('HOST_PREFIX') . 'api')
        ->middleware('api')
        ->namespace($this->namespace)
        ->group(base_path('routes/api.php'));

      Route::prefix(env('HOST_PREFIX') . 'api/admin')
        ->middleware('api')
        ->namespace($this->namespace)
        ->group(base_path('routes/admin.php'));

      Route::prefix(env('HOST_PREFIX') . 'api/user')
        ->middleware('api')
        ->namespace($this->namespace)
        ->group(base_path('routes/user.php'));

      Route::middleware('web')
        ->namespace($this->namespace)
        ->group(base_path('routes/web.php'));
      });
  }

  protected function configureRateLimiting()
  {
    RateLimiter::for('api', function (Request $request) {
      return Limit::perMinute(1000)->by($request->user()?->id ?: $request->ip());
    });
  }
}
