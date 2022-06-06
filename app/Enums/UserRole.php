<?php

namespace App\Enums;

use Illuminate\Support\Str;
use Spatie\Enum\Laravel\Enum;

/**
 * @method static self ADMIN()
 * @method static self CUSTOMER()
 * @method static self SUPER_ADMIN()
 */
final class UserRole extends Enum
{
    protected static function values()
    {
        return function (string $name): string|int {
            return Str::of($name)->lower()->replace('_', ' ')->snake(' ')->__toString();
        };
    }
}
