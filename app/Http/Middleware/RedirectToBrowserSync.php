<?php

namespace App\Http\Middleware;

use App;
use Closure;

class RedirectToBrowserSync
{
    /**
     * Handle an incoming request.
     *
     * @param                                         \Illuminate\Http\Request $request
     * @param                                         \Closure                 $next
     * @param                                         string|null              $guard
     * @return                                        mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (App::isLocal() && ($request->getPort() != 3000)) {
            //return \Response::redirectTo('http://localhost:3000/');
        }

        return $next($request);
    }
}
