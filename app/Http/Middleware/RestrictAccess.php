<?php

namespace App\Http\Middleware;

use Closure;
// step1: to add the following
use Auth;
use User;


class RestrictAccess
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
        // Step 2: and create the function isAdmin() uner User.php
        // Step 3: add admin field to User table
        // Step 4: add middleware inside the kernel.php
        // Step 5: apply middleware to route inside the web.php
        if (Auth::check() && Auth::user()->isAdmin()) {
         return $next($request);
        }
        return redirect("/login");
    }
}
