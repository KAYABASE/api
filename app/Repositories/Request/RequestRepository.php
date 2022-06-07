<?php

namespace App\Repositories\Request;

use App\Models\Table;
use Fabrikod\Repository\Contracts\Repository;

interface RequestRepository extends Repository
{
    public function makeShowEp(Table $table, $request);
    public function makeStoreEp(Table $table, $request);
    public function makeUpdateEp(Table $table, $request);
    public function makeDestroyEp(Table $table, $request);
}
