<?php

namespace App\Repositories\Column;

use App\Models\Column;
use App\Traits\SendsApiResponse;
use Fabrikod\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class ColumnRepositoryEloquent extends BaseRepository implements ColumnRepository
{
    use SendsApiResponse;

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

    public function updateMany($table_id, $columns)
    {
        if (count($columns) == 0 || !$columns) {
            return $this->error('No columns provided');
        }

        $columns_db = $this->all()->where('table_id', $table_id);

        // DELETE
        foreach ($columns_db as $column_db) {
            $isExist = collect($columns)->where('id', $column_db['id'])->count() == 0 ? false : true;

            if (!$isExist) {
                $column = $this->findOrFail($column_db['id']);
                $column->delete();
            }
        }

        // POST
        foreach ($columns as $column) {
            $isExist = isset($table[0]['id']);

            if (!$isExist) {
                $resource = $this->create(array_merge($column[0], ['table_id' => $table_id]));
            }
        }

        // PUT
        foreach ($columns as $column) {
            $isExist = isset($table[0]['id']);

            if ($isExist) {
                $this->update(array_merge($column[0], ['table_id' => $table_id]), $column[0]['id']);
            }
        }
    }
}
