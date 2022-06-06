<?php

namespace App\Repositories\Column;

use App\Models\Column;
use Fabrikod\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class ColumnRepositoryEloquent extends BaseRepository implements ColumnRepository
{
    /**
     * @inheritDoc
     */
    public function query(): Builder
    {
        return Column::query();
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        //
    }

    public function createMany($table_id, $columns)
    {
        if (count($columns) == 0 || !$columns) {
            return $this->error('No columns provided');
        }

        foreach ($columns as $column) {
            $this->create(array_merge($column, ['table_id' => $table_id]));
        }
    }

}
