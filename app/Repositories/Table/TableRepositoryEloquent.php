<?php

namespace App\Repositories\Table;

use App\Models\Table;
use App\Repositories\Column\ColumnRepository;
use App\Traits\SendsApiResponse;
use Fabrikod\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class TableRepositoryEloquent extends BaseRepository implements TableRepository
{
    use SendsApiResponse;

    /**
     * @inheritDoc
     */
    public function query(): Builder
    {
        return Table::query();
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        //
    }

    public function createManyWithColumns($database_id, $tables, ColumnRepository $columnRepository)
    {
        if (count($tables) == 0 || !$tables) {
            return $this->error('No tables provided');
        }

        foreach ($tables as $table) {
            $tableResource = $this->create(array_merge($table[0], ['database_id' => $database_id]));

            if (@$table[0]['columns'] && count(@$table[0]['columns']) > 0) {
                $columnRepository->createMany($tableResource->id, $table[0]['columns']);
            }
        }
    }

    public function updateManyWithColumns($database_id, $tables, ColumnRepository $columnRepository)
    {
        if (count($tables) == 0 || !$tables) {
            return $this->error('No tables provided');
        }

        $tables_db = $this->all()->where('database_id', $database_id);

        // DELETE
        foreach ($tables_db as $table_db) {
            $isExist = collect($tables)->where('id', $table_db['id'])->count() == 0 ? false : true;

            if (!$isExist) {
                $table = $this->findOrFail($table_db['id']);
                $table->delete();
            }
        }

        // POST
        foreach ($tables as $table) {
            $isExist = isset($table[0]['id']);

            if (!$isExist) {
                $resource = $this->create(array_merge($table[0], ['database_id' => $database_id]));

                if (@$table[0]['columns'] && count(@$table[0]['columns']) > 0) {
                    $columnRepository->createMany($resource->id, $table[0]['columns']);
                }
            }
        }

        // PUT
        foreach ($tables as $table) {
            $isExist = isset($table[0]['id']);

            if ($isExist) {
                $this->update(array_merge($table[0], ['database_id' => $database_id]), $table[0]['id']);

                if (@$table[0]['columns'] && count(@$table[0]['columns']) > 0) {
                    $columnRepository->updateMany($table[0]['id'], $table[0]['columns']);
                }
            }
        }
    }
}
