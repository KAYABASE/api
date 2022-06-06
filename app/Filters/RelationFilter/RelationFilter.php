<?php

namespace App\Filters\RelationFilter;

use App\Filters\RelationFilter\RelationFilterOptions;
use Fabrikod\Repository\Contracts\Filter;
use Fabrikod\Repository\Contracts\Repository;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\QueryBuilder;

class RelationFilter implements Filter
{
    protected $with;

    public function __construct(protected RelationFilterOptions $options)
    {
        # code...
    }

    public function with(array $relations)
    {
        $this->with = $relations;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function apply(Builder $query, Repository $repository)
    {
        $builder = QueryBuilder::for($query)->allowedIncludes($this->options->filters());

        if (!empty($this->with)) {
            $builder->with($this->with);
        }

        return $builder;
    }
}
