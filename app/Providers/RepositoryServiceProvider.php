<?php

namespace App\Providers;

use App\Repositories\ActivityLog\{
    ActivityLogRepository,
    ActivityLogRepositoryEloquent
};
use App\Repositories\User\{
    UserRepository,
    UserRepositoryEloquent
};
use App\Repositories\Database\{DatabaseRepository, DatabaseRepositoryEloquent};
use App\Repositories\Table\{TableRepository, TableRepositoryEloquent};
use App\Repositories\Column\{ColumnRepository, ColumnRepositoryEloquent};
use App\Repositories\Value\{ValueRepository, ValueRepositoryEloquent};
use App\Repositories\Request\{RequestRepository, RequestRepositoryEloquent};
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Repository bindings.
     *
     * @var array
     */
    const BINDINGS = [
        ActivityLogRepository::class => ActivityLogRepositoryEloquent::class,
        UserRepository::class => UserRepositoryEloquent::class,
        DatabaseRepository::class => DatabaseRepositoryEloquent::class,
        TableRepository::class => TableRepositoryEloquent::class,
        ColumnRepository::class => ColumnRepositoryEloquent::class,
        ValueRepository::class => ValueRepositoryEloquent::class,
        RequestRepository::class => RequestRepositoryEloquent::class,
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        foreach (self::BINDINGS as $abstract => $concrete) {
            $this->app->bind($abstract, $concrete);
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
