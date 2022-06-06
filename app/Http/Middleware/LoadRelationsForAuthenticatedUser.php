<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LoadRelationsForAuthenticatedUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($user = $request->user()) {
            $user->load([
                'favorites'
            ]);
        }

        return $next($request);
    }
}
