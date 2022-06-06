<?php

namespace App\Filters\QueryFilter\Custom;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class HasFilter implements Filter
{
    public function __construct(protected string $relation, protected bool $nullCheck = false, protected bool $localizedRelation = false)
    {
    }

    public function __invoke(Builder $query, $value, string $property)
    {
        if (blank($value)) {
            return $query;
        }

        if ($this->localizedRelation) {
            $this->relation = sprintf("%s->%s", $this->relation, app()->getLocale());
        }

        $condition = filter_var($value, FILTER_VALIDATE_BOOLEAN);

        if ($this->nullCheck) {
            $method = $condition ? 'whereNotNull' : 'whereNull';

            return $query->{$method}($this->relation);
        }

        $method = $condition ? 'whereHas' : 'whereDoesntHave';

        return $query->{$method}($this->relation);
    }
}
