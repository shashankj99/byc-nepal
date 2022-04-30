<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdminCreated
{
    /**
     * Function that handles block-way for user to enter into app without changing PW once
     * @param Request $request
     * @param Closure $next
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->is_admin_created == "0")
            return $next($request);

        return redirect()->route("customer.change.password");
    }
}
