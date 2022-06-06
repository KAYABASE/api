<?php

namespace App\Filters;

use Fabrikod\Repository\Contracts\Filter;
use Fabrikod\Repository\Contracts\Repository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PublishFilter implements Filter
{
    protected bool $shouldBeUnauthenticated = true;

    public function __construct(protected string $column)
    {
        //
    }

    public static function column(string $column)
    {
        return new static($column);
    }

    public function shouldBeUnauthenticated($value = true)
    {
        $this->shouldBeUnauthenticated = $value;

        return $this;
    }

    public function hasAdminAuthenticated(): bool
    {
        return Gate::allows('viewPanel');
    }

    public function applicable(): bool
    {
        if ($this->shouldBeUnauthenticated) {
            return !$this->hasAdminAuthenticated();
        }

        return true;
    }

    /**
     * Apply when user is not authenticated as admin if want to it.
     *
     * @inheritDoc
     */
    public function apply(Builder $query, Repository $repository)
    {
        if ($this->applicable()) {
            return $query->where($this->column, true);
        }

        if (($whereIndex = $this->findWhereClauseIndex($query)) !== false) {
            $this->removeWhereClause($query, $whereIndex);
        }

        return $query;
    }

    protected function findWhereClauseIndex($query)
    {
        $wheres = $query->getQuery()->wheres;

        foreach ($wheres as $index => $where) {
            if ($where['column'] === $this->column && $where['value'] === true) {
                return $index;
            }
        }

        return false;
    }

    /**
     * @param Builder|QueryBuilder $query
     */
    protected function removeWhereClause($query, $index)
    {
        $q = clone $query->getQuery();

        $wheres = $q->wheres;

        // Remove the where clause
        unset($wheres[$index]);

        $q->wheres = $wheres;

        $bindings = $q->bindings['where'];

        // Remove the bindings
        unset($bindings[$index]);

        $q->bindings['where'] = $bindings;

        $query->setQuery($q);

        return $query;
    }
}
