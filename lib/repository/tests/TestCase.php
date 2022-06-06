<?php

namespace Fabrikod\Repository\Tests;

use Fabrikod\Repository\Eloquent\BaseRepository;
use Fabrikod\Repository\Providers\RepositoryServiceProvider;
use Fabrikod\Repository\Tests\Fixtures\RepositoryTestModel;
use Illuminate\Database\Eloquent\Builder;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            RepositoryServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');

        $this->artisan('migrate', ['--database' => 'testbench'])->run();


        $this->beforeApplicationDestroyed(function () {
            $this->artisan('migrate:rollback', ['--database' => 'testbench'])->run();
        });
    }

    protected function defineEnvironment($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    /**
     * @return DummyRepository
     */
    protected function repository()
    {
        return resolve(DummyRepository::class);
    }
}


class DummyRepository extends BaseRepository
{
    public function query(): Builder
    {
        return RepositoryTestModel::query();
    }
}
