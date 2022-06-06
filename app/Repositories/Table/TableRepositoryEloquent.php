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
}
