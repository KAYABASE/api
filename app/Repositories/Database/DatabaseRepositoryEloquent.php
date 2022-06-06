<?php

namespace App\Repositories\Database;

use App\Models\Database;
use App\Traits\SendsApiResponse;
use Fabrikod\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class DatabaseRepositoryEloquent extends BaseRepository implements DatabaseRepository
{
    

    /**
     * @inheritDoc
     */
    public function query(): Builder
    {
        return Database::query();
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        //
    }

}
