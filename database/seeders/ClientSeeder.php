<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

/**
 * Class ClientSeeder
 */
class ClientSeeder extends Seeder
{
    public function run()
    {
        $client = new \Laravel\Passport\ClientRepository();

        $client->createPasswordGrantClient(null, 'Default password grant client', "");
        $client->createPersonalAccessClient(null, 'Personal Access Token', "");
    }
}
