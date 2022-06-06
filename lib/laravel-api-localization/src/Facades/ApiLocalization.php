<?php

namespace Fabrikod\ApiLocalization\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Fabrikod\ApiLocalization\ApiLocalization
 */
class ApiLocalization extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'api-localization';
    }
}
