<?php

namespace App\Helpers;

/**
 * Class ArrayUtils
 *
 * @package App\Helpers
 */
class ArrayUtils
{
    /**
     * @param $item
     *
     * @return mixed
     */
    public static function compact($item)
    {
        if (!is_array($item)) {
            return $item;
        }

        return collect($item)
            ->reject(function ($item) {
                return is_null($item);
            })
            ->flatMap(function ($item, $key) {

                return is_numeric($key)
                    ? [self::compact($item)]
                    : [$key => self::compact($item)];
            })
            ->toArray();
    }
}
