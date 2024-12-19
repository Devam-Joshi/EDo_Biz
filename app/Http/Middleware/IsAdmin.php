<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->user()) {
            return redirect()->route('admin.login');
<<<<<<< HEAD
        } elseif (in_array($request->user()->type, ['admin', 'local_admin','user'])) {
=======
        } elseif (in_array($request->user()->type, ['admin', 'local_admin'])) {
>>>>>>> parent of 5811feb (Added Middleware)
            return $next($request);
        } else {
            return redirect()->route(getDashboardRouteName());
        }
    }
}
