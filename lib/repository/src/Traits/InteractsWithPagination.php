<?php

namespace Fabrikod\Repository\Traits;

trait InteractsWithPagination
{
    public function pagination($query, $method = 'paginate', $columns = ['*'])
    {
        $request = request();

        $perPage = $request->query('perPage', config('repository.pagination.limit', 15));

        $results = $query->{$method}($perPage, $columns);

        $results->appends($request->query());

        return $results;
    }
}
