<?php

namespace App\Repositories\Request;

use App\Models\Request;
use App\Models\Table;
use Fabrikod\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class RequestRepositoryEloquent extends BaseRepository implements RequestRepository
{
    /**
     * @inheritDoc
     */
    public function query(): Builder
    {
        return Request::query();
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        //
    }

    public function makeShowEp(Table $table, $request)
    {
        $requestBody = $request->validated();
        $_request = Request::where('filter', json_encode($requestBody('filter')['ids']))->where('table_id', $table->id)->first();
        if ($_request) {
            return $_request;
        }

        $query = uniqid();
        $_request = Request::create([
            'table_id' => $table->id,
            'query' => $query,
            'filter' => json_encode($requestBody('filter')['ids']),
        ]);
        return $_request;
    }
}
