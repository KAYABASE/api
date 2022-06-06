<?php

namespace App\Enums;

use Illuminate\Support\Str;
use Spatie\Enum\Laravel\Enum;

/**
 * @method static self VARCHAR()
 * @method static self INTEGER()
 * @method static self DECIMAL()
 * @method static self BOOLEAN()
 */
final class ValueType extends Enum
{
    protected static function values()
    {
        return function (string $name): string|int {
            return Str::of($name)->lower()->snake(' ')->__toString();
        };
    }
}
