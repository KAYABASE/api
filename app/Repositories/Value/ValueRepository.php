<?php

namespace App\Repositories\Value;

use Fabrikod\Repository\Contracts\Repository;

interface ValueRepository extends Repository
{
    public function getParsed($id = null);
    public function parseType($value, $type);
    public function customPaginate($items, $perPage = 15, $page = null, $options = []);
    public function updateMany($values);
}
