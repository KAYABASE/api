<?php

namespace Fabrikod\ApiLocalization\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiLocalizationMiddleware extends LocalizationMiddlewere
{
    public function run(Request $request, Closure $next)
    {
        $locale = $this->determineLocaleFromHeader($request);

        $enableMiddlewareException = $this->localization->config('enable_middleware_exception', false);

        $this->localization->setLocale($locale, !$enableMiddlewareException);

        return $next($request);
    }

    protected function determineLocaleFromHeader($request)
    {
        $headerName = $this->localization->headerName();

        $headerValue = $request->header($headerName, $this->localization->defaultLocale());

        if ($headerName !== 'Accept-Language') {
            return $headerValue;
        }

        $matches = $this->getMatchesFromAcceptedLanguages($headerValue);

        $availableLocales = $this->localization->availableLocales();

        foreach ($matches as $key => $q) {

            // Get locale from map
            $locale = $this->localization->localeFromMaps($key);

            // If locale is available from locale map, return it.
            if (isset($availableLocales[$locale])) {
                return $locale;
            }

            // Search for acceptable locale by 'regional' => 'af_ZA' or 'lang' => 'af-ZA' match.
            foreach ($availableLocales as $available_locale => $locale) {
                if ((isset($locale['regional']) && $locale['regional'] == $key) ||
                    (isset($locale['lang']) && $locale['lang'] == $key)
                ) {
                    return $available_locale;
                }
            }
        }

        // If any (i.e. "*") is acceptable, return the first supported format
        if (isset($matches['*'])) {
            reset($acceptLanguages);

            return key($acceptLanguages);
        }

        return $headerValue;
    }

    /**
     * Return all the accepted languages from the browser.
     *
     * @return array Matches from the header field Accept-Languages
     */
    protected function getMatchesFromAcceptedLanguages($acceptLanguages)
    {
        $matches = [];

        if ($acceptLanguages) {
            $acceptLanguages = explode(',', $acceptLanguages);

            $generic_matches = [];

            foreach ($acceptLanguages as $option) {
                $option = array_map('trim', explode(';', $option));

                $l = $option[0];
                $q = null;

                if (isset($option[1])) {
                    $q = (float) str_replace('q=', '', $option[1]);
                }
                // Assign default low weight for generic values
                else if ($l == '*/*') {
                    $q = 0.01;
                } elseif (substr($l, -1) == '*') {
                    $q = 0.02;
                }

                // Unweighted values, get high weight by their position in the list
                $q = $q ?: 1000 - count($matches);
                $matches[$l] = $q;

                // If for some reason the Accept-Language header only sends language with country
                // we should make the language without country an accepted option, with a value
                // less than it's parent.
                $l_ops = explode('-', $l);

                array_pop($l_ops);

                while (!empty($l_ops)) {
                    //The new generic option needs to be slightly less important than it's base
                    $q -= 0.001;
                    $op = implode('-', $l_ops);
                    if (empty($generic_matches[$op]) || $generic_matches[$op] > $q) {
                        $generic_matches[$op] = $q;
                    }
                    array_pop($l_ops);
                }
            }

            $matches = array_merge($generic_matches, $matches);

            arsort($matches, SORT_NUMERIC);
        }

        return $matches;
    }
}
