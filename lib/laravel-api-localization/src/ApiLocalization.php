<?php

namespace Fabrikod\ApiLocalization;

use Fabrikod\ApiLocalization\Exceptions\AvailableLocalesNotDefined;
use Fabrikod\ApiLocalization\Exceptions\NotAvailableLocaleException;
use Fabrikod\ApiLocalization\Middleware\ApiLocalizationMiddleware;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;

class ApiLocalization
{
    const CONFIG_KEY = 'api-localization';

    /**
     * @var Application
     */
    protected $app;

    protected $defaultLocale;

    protected $availableLocales;

    protected $localeMaps;

    protected $locale;

    public function __construct(public Repository $config)
    {
        $this->defaultLocale = $this->config->get('app.locale');
    }

    public function defaultLocale()
    {
        return $this->defaultLocale;
    }

    public function setLocale(string $locale, bool $force = false)
    {
        if (!$force) {
            $this->ensureLocaleSupported($locale);
        }

        $this->locale = $this->parseLocale($locale);

        app()->setLocale($this->locale);

        $this->configureLocaleTime();

        return $this->currentLocale();
    }

    public function parseLocale($locale)
    {
        $availableLocales = $this->availableLocales();

        $regionalLocales = array_column($availableLocales, 'regional');

        if (isset($availableLocales[$locale])) {
            return $this->localeFromMaps($locale);
        }

        if (in_array($locale, $regionalLocales)) {
            $localeIndex = array_search($locale, $regionalLocales);

            $locales = array_keys($availableLocales);

            $locale = $locales[$localeIndex];
        }


        return $this->localeFromMaps($locale);
    }

    public function localeFromMaps($locale)
    {
        return $this->localeMaps()[$locale] ?? $locale;
    }

    public function currentLocale()
    {
        if ($this->locale) {
            return $this->locale;
        }

        return app()->getLocale();
    }

    public function configureLocaleTime()
    {
        $locale = $this->locale ?: $this->defaultLocale;

        $regional = $this->regionalFromLocale($locale);

        if ($regional) {
            $suffix = $this->config('utf8suffix');

            setlocale(LC_TIME, $regional . $suffix);
            setlocale(LC_MONETARY, $regional . $suffix);
        }
    }

    public function regionalFromLocale(string $locale)
    {
        $locales = $this->availableLocales();

        return $locales[$locale]['regional'] ?? null;
    }

    public function localeMaps()
    {
        if (empty($this->localeMaps)) {
            $this->localeMaps = $this->config('localeMaps');
        }

        return $this->localeMaps;
    }

    public function ignoredUrls()
    {
        return $this->config('ignoredUrls', []);
    }

    public function middleware()
    {
        return $this->config('middleware', ApiLocalizationMiddleware::class);
    }

    public function headerName()
    {
        return $this->config('header_name', 'Accept-Language');
    }

    public function config($key, $default = null): mixed
    {
        return $this->config->get(self::CONFIG_KEY . '.' . $key, $default);
    }

    public function ensureDefaultLocaleSupported()
    {
        $this->ensureLocaleSupported($this->defaultLocale);
    }

    public function ensureLocaleSupported($locale)
    {
        $locales = $this->availableLocales();

        $regionalLocales = array_column($locales, 'regional');

        if (!isset($locales[$locale]) && !in_array($locale, $regionalLocales)) {
            throw NotAvailableLocaleException::setLocale($locale);
        }
    }

    public function localeFromAvailableLocales($locale, $key = null, $default = null)
    {
        $locales = $this->availableLocales();

        $availableLocale = $locales[$locale] ?? ($default ? $locales[$default] ?? null : null);

        if ($key) {
            return $availableLocale[$key];
        }

        return $availableLocale;
    }

    public function availableLocales(): array
    {
        if ($this->availableLocales) {
            return $this->availableLocales;
        }

        $locales = $this->config(__FUNCTION__);

        if (empty($locales) || !is_array($locales)) {
            throw new AvailableLocalesNotDefined;
        }

        return $this->availableLocales = $locales;
    }
}
