<?php

namespace Fabrikod\Repository;

use Fabrikod\Repository\Contracts\Filter;
use Fabrikod\Repository\Contracts\Repository;
use Illuminate\Database\Eloquent\Builder;

class CallableFilter implements Filter
{
    public function __construct(public $callback)
    {
        //
    }

    /**
     * Apply filter in query repository
     *
     * @param Builder $query
     * @param Repository $repository
     *
     * @return mixed
     */
    public function apply(Builder $query, Repository $repository)
    {
        return call_user_func($this->callback, $query, $repository);
    }
}
