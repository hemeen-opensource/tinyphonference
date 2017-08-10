<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;


class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $admin = Session::get('users');
        if (!empty($admin)) {
            return $next($request);
        }
        return redirect()->guest('/');
    }
}
