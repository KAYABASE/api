<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

/**
 * Class VerifyCsrfToken
 *
 * @package App\Http\Middleware
 */
class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
    ];

    /**
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     * @throws TokenMismatchException
     * @noinspection PhpMissingParamTypeInspection
     */
    public function handle($request, Closure $next)
    {
        if (!$request->wantsJson()) {
            return parent::handle($request, $next);
        }

        return $next($request);
    }
}
