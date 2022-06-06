<?php

namespace Fabrikod\Repository\Contracts;

use Illuminate\Support\Collection;


interface RepositoryFilter
{

    /**
     * Push filter for the query
     *
     * @param $filter
     *
     * @return $this
     */
    public function pushFilter(Filter|callable|string|array $filter);

    /**
     * Get filters
     *
     * @return Collection
     */
    public function getFilters();

    /**
     * Find data by Filter
     *
     * @param Filter|string $filter
     *
     * @return mixed
     */
    public function getByFilter($filter);

    /**
     * Skip Filter
     *
     * @param bool $status
     *
     * @return $this
     */
    public function skipFilters($status = true);

    /**
     * Reset all Filters
     *
     * @return $this
     */
    public function resetFilters();
}
