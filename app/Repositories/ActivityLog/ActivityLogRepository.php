<?php

namespace App\Repositories\ActivityLog;

use Fabrikod\Repository\Contracts\Repository;

interface ActivityLogRepository
{
    public function paginate($columns = ['*']);
}
