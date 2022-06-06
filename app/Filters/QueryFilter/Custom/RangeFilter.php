<?php

namespace App\Filters\QueryFilter\Custom;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class RangeFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {

        if (is_array($value)) {
            $count = count($value);

            if ($count > 2) {
                $value = [$value[0], $value[1]];
            } elseif ($count < 2) {
                $value = [$value[0], $value[0]];
            }

            return $query->whereBetween($property, $value);
        }

        $val = Str::of($value);

        if ($val->startsWith('>=')) {
            return $query->where($property, '>=', $val->after('>='));
        } else if ($val->startsWith('<=')) {
            return $query->where($property, '<=', $val->after('<='));
        } else if ($val->startsWith('>')) {
            return $query->where($property, '>', $val->after('>'));
        } else if ($val->startsWith('<')) {
            return $query->where($property, '<', $val->after('<'));
        }

        return $query->where($property, $value);
    }
}
