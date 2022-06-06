<?php

namespace App\Http\Controllers;

use App\Http\Resources\ActivityLogResource;
use App\Repositories\ActivityLog\ActivityLogRepository;
use Spatie\Activitylog\Models\Activity;

class LogController extends Controller
{
    public function __construct()
    {
        $this->panelMiddleware();
    }

    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(ActivityLogRepository $repository)
    {
        $this->authorize('viewAny', Activity::class);

        return ActivityLogResource::collection($repository->paginate());
    }
}
