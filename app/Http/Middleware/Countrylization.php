<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Countrylization
{

  public function handle(Request $request, Closure $next)
  {
    $country = $request->hasHeader('country');

    if (!in_array($country = $request->header('country'), ['sa'])) {
      $country = 'sa';
    }
    
    \App::singleton('country', function() use ($country){
      return $country;
    });

    return $next($request);
  }
}