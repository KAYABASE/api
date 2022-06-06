<?php

namespace Database\Seeders;

use App\Models\Auth\Role;
use App\Services\PermissionFactory;
use App\Services\PermissionGroupFactory;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        activity()->disableLogging();

        Role::prepareDefaults();

        /**
         * Create permissions
         *
         * @var PermissionFactory $factory
         * ---------------------------------------------------------------------------------------------------------
         *
         * For example:
         *
         * `$factory->make('user');` process creates user permissions. (view user, create user, update user, delete user)
         */
        $factory = resolve(PermissionFactory::class)->guard('api');

        $factory
            ->make('user');

        $factory->only('activity log', ['view']);

        $factory->makeWithGroup('settings', function (PermissionGroupFactory $factory) {
            $factory->only('contact', ['view', 'update']);

            return $factory;
        });

        $factory->build();

        activity()->enableLogging();
    }
}
