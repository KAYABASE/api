<?php

namespace Fabrikod\ApiLocalization\Exceptions;

use Exception;

class AvailableLocalesNotDefined extends Exception
{
    public function __construct()
    {
        parent::__construct('No available languages are defined.');
    }
}
