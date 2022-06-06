<?php

namespace Fabrikod\Repository\Providers;

use App\Providers\TenancyServiceProvider;
use Fabrikod\Repository\Console\RepositoryFilterMakeCommand;
use Fabrikod\Repository\Console\RepositoryInterfaceMakeCommand;
use Fabrikod\Repository\Console\RepositoryMakeCommand;
use Illuminate\Support\ServiceProvider;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath;
use Stancl\Tenancy\Middleware\InitializeTenancyByRequestData;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/**
 * Class RepositoryServiceProvider
 * @package Fabrikod\Repository\Providers
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/repository.php' => config_path('repository.php')
        ]);

        $this->mergeConfigFrom(__DIR__ . '/../../config/repository.php', 'repository');

        $tenancyMiddleware = [
            PreventAccessFromCentralDomains::class,
            InitializeTenancyByDomain::class,
            InitializeTenancyBySubdomain::class,
            InitializeTenancyByDomainOrSubdomain::class,
            InitializeTenancyByPath::class,
            InitializeTenancyByRequestData::class,
        ];

        foreach (array_reverse($tenancyMiddleware) as $middleware) {
            $this->app[\Illuminate\Contracts\Http\Kernel::class]->prependToMiddlewarePriority($middleware);
        }
    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                RepositoryMakeCommand::class,
                RepositoryInterfaceMakeCommand::class,
                RepositoryFilterMakeCommand::class,
            ]);
        }
        $this->app->register(EventServiceProvider::class);
        $this->app->register(TenancyServiceProvider::class);
    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
