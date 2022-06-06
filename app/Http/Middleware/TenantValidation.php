<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TenantValidation
{
    public function handle(Request $request, Closure $next)
    {
        if (empty($request->header("X-Tenant")) || is_null($request->header("X-Tenant"))) {
            abort(403, __('İşletme Kodu Zorunludur.'));
        }

        return $next($request);
    }
}
