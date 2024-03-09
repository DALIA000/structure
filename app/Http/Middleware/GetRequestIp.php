<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class GetRequestIp
{

  public function handle(Request $request, Closure $next)
  {
    $request->merge(['ip' => $request->ip()]);
    return $next($request);
  }
}
