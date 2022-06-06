<?php

namespace App\Console\Commands;

use App\Elasticsearch\IndexConfigurators\ProductIndexConfigurator;
use App\Models\Product\Product;
use Illuminate\Console\Command;

class ElasticsearchPrepareCommand extends Command
{
    const INDEXES = [
        ProductIndexConfigurator::class
    ];

    const MODELS = [
        Product::class
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elasticsearch:prepare';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prepare all indexes,maps and data for elasticsearch';

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
     * @return mixed
     */
    public function handle()
    {
        $this->updateIndexes();
        $this->updateMaps();
        $this->scoutImport();
    }

    protected function updateMaps()
    {
        foreach (self::MODELS as $model) {
            $this->call("elastic:update-mapping", ['model' => $model]);
        }
    }

    protected function scoutImport()
    {
        foreach (self::MODELS as $model) {
            $this->call("scout:import", ['model' => $model]);
        }
    }

    protected function updateIndexes()
    {
        foreach (self::INDEXES as $index) {
            try {
                $this->call("elastic:create-index", ['index-configurator' => $index]);
            } catch (\Throwable $th) {
                $this->call("elastic:update-index", ['index-configurator' => $index]);
            }
        }
    }
}
