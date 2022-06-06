<?php

namespace Fabrikod\ApiLocalization;

use Fabrikod\ApiLocalization\Middleware\ApiLocalizationMiddleware;
use Illuminate\Support\ServiceProvider;

class ApiLocalizationServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/api-localization.php', 'api-localization');

        $this->app->singleton(ApiLocalization::class);

        $this->app->alias(ApiLocalization::class, 'api-localization');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/api-localization.php' => config_path('api-localization.php'),
            ], 'config');
        }

        $this->app['router']->pushMiddlewareToGroup('api', $this->app[ApiLocalization::class]->middleware());
    }
}
