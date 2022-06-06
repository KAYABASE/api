<?php

namespace App\Filters\QueryFilter\Custom;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class CuttableAnimalTypesFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        if ($value) {
            $query->whereHas('kinds', function ($query) {
                $query->where('cutting', true);
            });
        }
    }
}