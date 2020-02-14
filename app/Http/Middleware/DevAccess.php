<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class DevAccess
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $clientIp     = $request->getClientIp();
        $isLmiPage    = $request->getRequestUri() !== '/let_me_in_please';
        $hasDevCookie = ! $request->cookie('dev_access_digitalidea');
        $byPassIPs    = array_filter(str_getcsv(env('BYPASS_MAINTENANCE_IPS', '')));
        $canByPass    = in_array($clientIp, $byPassIPs);
        $isLocalEnv   = app()->environment() == 'local';
        $isGraphQLUri = $request->getRequestUri() === '/graphql';

        if ($isLmiPage && $hasDevCookie && ! $canByPass && ! $isLocalEnv && ! $isGraphQLUri) {
            return abort(503, 'Service Unavailable');
        }

        return $next($request);
    }
}
