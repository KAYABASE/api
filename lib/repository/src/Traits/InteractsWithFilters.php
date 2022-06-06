<?php

namespace Fabrikod\Repository\Traits;

use Fabrikod\Repository\CallableFilter;
use Fabrikod\Repository\Contracts\Filter;
use Fabrikod\Repository\Exceptions\RepositoryException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

trait InteractsWithFilters
{
    /**
     * Collection of filters
     *
     * @var Collection
     */
    protected $filters;

    /**
     * @var bool
     */
    protected $skipFilters = false;

    /**
     * Push filters for filter the query
     *
     * @param Filter|callable|string|array $filter
     *
     * @return $this
     * @throws \Fabrikod\Repository\Exceptions\RepositoryException
     */
    public function pushFilter(Filter|callable|string|array $filter)
    {
        if (is_array($filter)) {
            return $this->pushFilters($filter);
        }

        $filter = $this->resolveFilter($filter);

        $this->filters->push($filter);

        return $this;
    }

    public function pushFilters(array $filters)
    {
        foreach ($filters as $filter) {
            $this->pushFilter($filter);
        }

        return $this;
    }

    public function resolveFilter($filter)
    {
        if (is_string($filter) && !is_subclass_of($filter, Filter::class)) {
            throw new RepositoryException('Filter must be an instance of ' . Filter::class);
        } else if (is_string($filter)) {
            $filter = resolve($filter);
        } else if (is_callable($filter)) {
            $filter = new CallableFilter($filter);
        }

        return $filter;
    }

    /**
     * Get filters
     *
     * @return Collection
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Find data by filters
     *
     * @param Filter|string $filter
     *
     * @return mixed
     */
    public function getByFilter($filter)
    {
        $filter = $this->resolveFilter($filter);

        $filter->apply($this->query, $this);

        return $this->query->get();
    }

    /**
     * Skip filters
     *
     * @param bool $status
     *
     * @return $this
     */
    public function skipFilters($status = true)
    {
        $this->skipFilters = $status;

        $this->resetQuery();

        return $this;
    }

    /**
     * Reset all filters
     *
     * @return $this
     */
    public function resetFilters()
    {
        $this->filters = new Collection;

        return $this;
    }

    /**
     * Apply filters
     *
     * @return $this
     */
    public function applyFilters()
    {
        if ($this->skipFilters === true) {
            return $this;
        }

        $this->filters
            ->whereInstanceOf(Filter::class)
            ->each->apply($this->query, $this);

        return $this;
    }

    /**
     * Apply all filters and handle the callback
     *
     * @param callable|mixed $callback
     */
    public function withFilters(callable $callback)
    {
        $this->applyFilters();

        return call_user_func($callback, $this);
    }
}
