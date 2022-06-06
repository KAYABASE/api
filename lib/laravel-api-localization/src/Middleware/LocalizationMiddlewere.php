<?php

namespace Fabrikod\ApiLocalization\Middleware;

use Closure;
use Fabrikod\ApiLocalization\ApiLocalization;
use Illuminate\Http\Request;

abstract class LocalizationMiddlewere
{
    /**
     * The URIs that should not be localized.
     *
     * @var array
     */
    protected $except;

    public function __construct(protected ApiLocalization $localization)
    {
        $this->except = $this->except ?: $this->localization->ignoredUrls();
    }

    abstract public function run(Request $request, Closure $next);

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->shouldIgnore($request)) {
            return $next($request);
        }

        return $this->run($request, $next);
    }

    /**
     * Determine if the request has a URI that should not be localized.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function shouldIgnore($request)
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->is($except)) {
                return true;
            }
        }

        return false;
    }
}
