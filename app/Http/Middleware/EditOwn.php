<?php

namespace App\Http\Middleware;

use App\Helpers\AuthHelper;
use Closure;

class EditOwn
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
        if (AuthHelper::myId() && AuthHelper::myId() != $request->user_id) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            }

            return abort(401, 'Unauthorized');
        }

        return $next($request);
    }
}
