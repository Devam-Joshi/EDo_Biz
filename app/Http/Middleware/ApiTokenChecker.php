<?php

namespace App\Http\Middleware;

use App\DeviceToken;
use Closure;
use Illuminate\Support\Facades\Auth;

class ApiTokenChecker
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
        $valid_type = ['user', 'vendor'];
        $token = get_header_auth_token();
        if ($token) {
            $is_login = DeviceToken::where('token', $token)->with('user')->has('user')->first();
            if ($is_login) {
                $user_data = $is_login->user;
                if ($user_data->status == "active") {
                    if (in_array($user_data->type, $valid_type)) {
                        Auth::loginUsingId($user_data->id);
                        return $next($request);
                    } else {
                        send_response(401, __('api.err_not_allowed_task'));
                    }
                } else {
                    send_response(401, __('api.err_account_ban'));
                }
            }
        }
        send_response(401, __('api.err_please_login'));
    }

}
