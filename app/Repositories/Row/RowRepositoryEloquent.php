<?php

namespace App\Repositories\Row;

use App\Models\Row;
use Fabrikod\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class RowRepositoryEloquent extends BaseRepository implements RowRepository
{
    /**
     * @inheritDoc
     */
    public function query(): Builder
    {
        return Row::query();
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        //
    }

}
