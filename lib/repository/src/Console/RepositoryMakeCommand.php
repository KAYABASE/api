<?php

namespace Fabrikod\Repository\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class RepositoryMakeCommand extends GeneratorCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:repository';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (parent::handle() === false && !$this->option('force')) {
            return false;
        }

        $this->createInterface();
    }

    /**
     * Create a seeder file for the model.
     *
     * @return void
     */
    protected function createInterface()
    {
        $interface = $this->getNameInput();

        $this->call('make:repository-interface', [
            'name' => "{$interface}",
        ]);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/eloquent.stub');
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__ . $stub;
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $namespaceModel = $this->option('model')
            ? $this->qualifyModel($this->option('model'))
            : $this->qualifyModel($this->guessModelName($this->name('RepositoryEloquent')));

        $model = class_basename($namespaceModel);

        $interfaceName = $this->qualifyInterface($this->getNameInput());

        $replace = [
            '{{ model }}' => $model,
            '{{model}}' => $model,
            '{{ namespaceModel }}' => $namespaceModel,
            '{{namespaceModel}}' => $namespaceModel,
            '{{ interfaceName }}' => $interfaceName,
            '{{interfaceName}}' => $interfaceName,
        ];

        return str_replace(
            array_keys($replace),
            array_values($replace),
            parent::buildClass($name)
        );
    }

    protected function qualifyInterface($name)
    {
        return $name . 'Repository';
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        $baseName = class_basename($name);

        $name = Str::replaceLast($baseName, $this->name('RepositoryEloquent'), str_replace('\\', '/', $name));

        return $this->laravel['path'] . '/' . $name . '.php';
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $class = $this->name('RepositoryEloquent');

        return str_replace(['DummyClass', '{{ class }}', '{{class}}'], $class, $stub);
    }

    /**
     * Guess the model name from the Factory name or return a default model name.
     *
     * @param  string  $name
     * @return string
     */
    protected function guessModelName($name)
    {
        if (Str::endsWith($name, 'Repository')) {
            $name = substr($name, 0, -strlen('Repository'));
        } elseif (Str::endsWith($name, 'RepositoryEloquent')) {
            $name = substr($name, 0, -strlen('RepositoryEloquent'));
        }

        $modelName = $this->qualifyModel(Str::after($name, $this->rootNamespace()));

        if (class_exists($modelName)) {
            return $modelName;
        }

        if (is_dir(app_path('Models/'))) {
            return $this->rootNamespace() . 'Models\Model';
        }

        return $this->rootNamespace() . 'Model';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Repositories\\' . $this->name();
    }


    protected function name($append = null)
    {
        return Str::of($this->getNameInput())
            ->studly()
            ->replace(['Repository', 'RepositoryEloquent'], '')
            ->append($append)
            ->__toString();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'The name of the model'],
        ];
    }
}
