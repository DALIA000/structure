<?php

namespace App\Http\Middleware;

use App\Services\ResponseService;
use Closure;
use Illuminate\Http\Request;

class UserIsNotSuspended
{

    public function __construct(public ResponseService $responseService) {
        
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $loggedinUser = app('loggedinUser');

        $suspended_users = json_decode(\Cache::get('suspended_users')) ?: [];

        if (in_array($loggedinUser->id, $suspended_users)) {
            return $this->responseService->json('Fail!', [], 400, trans('models.user.suspended'));
        }

        return $next($request);
    }
}
