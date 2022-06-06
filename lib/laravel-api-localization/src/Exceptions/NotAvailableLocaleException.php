<?php

namespace Fabrikod\ApiLocalization\Exceptions;

use Exception;
use Fabrikod\ApiLocalization\ApiLocalization;

class NotAvailableLocaleException extends Exception
{
    public static function setLocale(string $locale)
    {
        $config = ApiLocalization::CONFIG_KEY . ".availableLocales";

        return new static("The locale '{$locale}' is not available in the [$config] configs.");
    }
}
