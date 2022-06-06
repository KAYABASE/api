<?php

namespace App\Repositories\Table;

use App\Repositories\Column\ColumnRepository;
use Fabrikod\Repository\Contracts\Repository;

interface TableRepository extends Repository
{
    public function createManyWithColumns($database_id, $tables, ColumnRepository $columnRepository);
    public function updateManyWithColumns($database_id, $tables, ColumnRepository $columnRepository);
}
