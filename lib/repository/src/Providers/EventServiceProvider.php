<?php

namespace Fabrikod\Repository\Providers;

use Fabrikod\Repository\Events\RepositoryEntityCreated;
use Fabrikod\Repository\Events\RepositoryEntityUpdated;
use Fabrikod\Repository\Listeners\CleanCacheRepository;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as BaseEventServiceProvider;

class EventServiceProvider extends BaseEventServiceProvider
{

    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        RepositoryEntityCreated::class => [
            CleanCacheRepository::class
        ],

        RepositoryEntityUpdated::class => [
            CleanCacheRepository::class
        ],

        RepositoryEntityDeleted::class => [
            CleanCacheRepository::class
        ]
    ];
}
