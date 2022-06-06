<?php

namespace App\Repositories\Column;

use Fabrikod\Repository\Contracts\Repository;

interface ColumnRepository extends Repository
{
    public function createMany($table_id, $columns);
    public function updateMany($table_id, $columns);
}
