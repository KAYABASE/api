<?php

namespace App\Repositories\ActivityLog;

use Illuminate\Database\Eloquent\Builder;
use Fabrikod\Repository\Eloquent\BaseRepository;
use Spatie\Activitylog\Models\Activity;

class ActivityLogRepositoryEloquent extends BaseRepository implements ActivityLogRepository
{
    /**
     * @inheritDoc
     */
    public function query(): Builder
    {
        return Activity::query();
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        //
    }
}
