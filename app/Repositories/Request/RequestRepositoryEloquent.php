<?php

namespace App\Repositories\Request;

use App\Models\Request;
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

}
