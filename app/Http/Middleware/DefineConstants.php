<?php

namespace App\Http\Middleware;

use App\GeneralSettings;
use Closure;

class DefineConstants
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
        GeneralSettings::define_const();
        return $next($request);
    }
}
