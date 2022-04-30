<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsNotCustomer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if ($user->hasRole("Customer"))
            abort(403, "You don't have access to this page");

        return $next($request);
    }
}
