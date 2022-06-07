<?php

namespace App\Repositories\Request;

use App\Models\Table;
use Fabrikod\Repository\Contracts\Repository;

interface RequestRepository extends Repository
{
    public function makeShowEp(Table $table, $request);
}
