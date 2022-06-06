<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tenant = Tenant::updateOrCreate(['name' => 'TEST USER']);

        $tenant->run(function () {
            $this->call(ClientSeeder::class);
            $this->call(PermissionSeeder::class);
        });
    }
}
