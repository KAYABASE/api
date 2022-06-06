<?php

namespace App\Filters\QueryFilter;

use Fabrikod\Repository\Contracts\Filter;
use Fabrikod\Repository\Contracts\Repository;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\QueryBuilder;

class QueryFilter implements Filter
{
    public function __construct(protected QueryFilterOptions $options)
    {
        //
    }

    /**
     * @inheritDoc
     */
    public function apply(Builder $query, Repository $repository)
    {
        return QueryBuilder::for($query)->allowedFilters($this->options->filters());
    }
}
