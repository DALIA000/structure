<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Localization
{
  public function handle(Request $request, Closure $next)
  {
    $locale = $request->hasHeader('lang');

    if (!in_array($locale = $request->header('lang'), ['en', 'ar'])) {
      $locale = 'en';
    }

    app()->setLocale($locale);
    return $next($request);
  }
}