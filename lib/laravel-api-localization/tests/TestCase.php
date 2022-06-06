<?php

namespace Fabrikod\ApiLocalization\Tests;

use Fabrikod\ApiLocalization\ApiLocalization;
use Orchestra\Testbench\TestCase as Orchestra;
use Fabrikod\ApiLocalization\ApiLocalizationServiceProvider;
use Fabrikod\ApiLocalization\Middleware\ApiLocalizationMiddleware;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Request;

class TestCase extends Orchestra
{
    protected $defaultLocale = 'en';

    protected $enabledMiddleware = true;

    /**
     * @var ApiLocalization
     */
    protected $apiLocalization;

    protected function setUp(): void
    {
        parent::setUp();

        $this->apiLocalization = $this->app->make(ApiLocalization::class);
    }

    protected function getPackageProviders($app)
    {
        return [
            ApiLocalizationServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        app('config')->set('app.locale', $this->defaultLocale);

        app('translator')->getLoader()->addNamespace('localization-test', realpath(dirname(__FILE__)) . '/lang');

        app('translator')->load('localization-test', 'routes', 'tr');
        app('translator')->load('localization-test', 'routes', 'en');
    }

    protected function enableMiddleware()
    {
        $this->enabledMiddleware = true;

        return $this;
    }

    protected function disableMiddleware()
    {
        $this->enabledMiddleware = false;

        return $this;
    }

    protected function setRoutes($locale = null)
    {
        if ($locale) {
            $this->apiLocalization->setLocale($locale);
        }

        $middleware = $this->enabledMiddleware ? [ApiLocalizationMiddleware::class] : [];

        app('router')->middleware($middleware)
            ->prefix('api')
            ->group(function () {
                app('router')->get('/', ['as' => 'index', function () {
                    return app('translator')->get('localization-test::test.hello');
                },]);

                app('router')->get('/skipped', ['as' => 'skipped', function () {
                    return app('translator')->get('localization-test::test.hello');
                },]);
            });
    }

    /**
     * Refresh routes and refresh application.
     *
     * @param bool|string $locale
     */
    protected function refreshApplication($locale = null)
    {
        parent::refreshApplication();

        $this->setRoutes($locale);
    }
}
