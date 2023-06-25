<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ResetGoalReachMiddleware
{
    /**
     * Handle an incoming request.
     * @note Create a CRON job instead of middleware once app is live.
     * @param Request $request
     * @param Closure(Request): (Response) $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if ($user->shouldResetGoalReach()) {
            $user->resetGoalReach();
        }
        return $next($request);
    }
}
