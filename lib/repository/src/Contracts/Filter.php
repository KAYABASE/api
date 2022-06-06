<?php

namespace Fabrikod\Repository\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface Filter
{
    /**
     * Apply filter in query repository
     *
     * @param Builder $query
     * @param Repository $repository
     *
     * @return mixed
     */
    public function apply(Builder $query, Repository $repository);
}
