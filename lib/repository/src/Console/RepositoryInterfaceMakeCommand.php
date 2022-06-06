<?php

namespace Fabrikod\Repository\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class RepositoryInterfaceMakeCommand extends GeneratorCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:repository-interface';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository interface';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository Interface';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/interface.stub');
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
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Repositories\\' . $this->name();
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

        $name = Str::replaceLast($baseName, $this->name('Repository'), str_replace('\\', '/', $name));

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
        $class = $this->name('Repository');

        return str_replace(['DummyClass', '{{ class }}', '{{class}}'], $class, $stub);
    }

    protected function name($append = null)
    {
        return Str::of($this->getNameInput())
            ->studly()
            ->replace('Repository', '')
            ->append($append)
            ->__toString();
    }
}
