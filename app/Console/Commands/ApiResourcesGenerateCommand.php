<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ApiResourcesGenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:resources {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the api CRUD. It will be create controller,tests,repository,resource,policy and request classes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = Str::studly($this->argument('name'));

        // Create controller
        $this->createController($name);

        // Create tests
        $this->createTests($name);

        // Create repository
        $this->createRepository($name);

        // Create resource
        $this->createResource($name);

        // Create policy
        $this->createPolicy($name);

        // Create request
        $this->createRequests($name);
    }

    protected function createRequests($name)
    {
        $this->call('make:request', [
            'name' => "{$name}/{$name}StoreRequest",
        ]);

        $this->call('make:request', [
            'name' => "{$name}/{$name}UpdateRequest",
        ]);
    }

    protected function createPolicy($name)
    {
        $this->call('make:policy', [
            'name' => $name . 'Policy',
            '--model' => $name
        ]);
    }

    protected function createResource($name)
    {
        $this->call('make:resource', [
            'name' => $name . 'Resource',
        ]);
    }

    protected function createRepository($name)
    {
        $this->call('make:repository', [
            'name' => $name,
        ]);
    }

    protected function createController($name)
    {
        $this->call('make:controller', [
            'name' => $name . 'Controller',
            '--api' => true,
        ]);
    }

    protected function createTests($name)
    {
        $tests = [
            $name . '/' . $name . 'IndexTest',
            $name . '/' . $name . 'ViewTest',
            $name . '/' . $name . 'StoreTest',
            $name . '/' . $name . 'UpdateTest',
            $name . '/' . $name . 'DeleteTest',
        ];

        foreach ($tests as $test) {
            $this->call('make:test', [
                'name' => $test,
            ]);
        }
    }
}
