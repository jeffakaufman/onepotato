<?php

namespace Laravel\Spark\Http\Middleware;

use Laravel\Spark\Spark;

class VerifyUserIsAdmin
{
    /**
     * Determine if the authenticated user is a admin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {
        if ($request->user() && Spark::admin($request->user()->email)) {
            return $next($request);
        }

        return $request->ajax() || $request->wantsJson()
                        ? response('Unauthorized.', 401)
                        : redirect('/settings#/subscription');
    }
}
