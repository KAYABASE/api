<?php

namespace Fabrikod\Repository\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class RepositoryFilterMakeCommand extends GeneratorCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:repository-filter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository filter class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository Filter';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/filter.stub');
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
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $class = Str::of($this->getNameInput())
            ->studly()
            ->replace(['Filter'], '')
            ->append('Filter')
            ->__toString();

        return str_replace(['DummyClass', '{{ class }}', '{{class}}'], $class, $stub);
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Filters';
    }
}
