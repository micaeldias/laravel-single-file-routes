<?php

namespace MicaelDias\SingleFileRoutes\Commands;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class RouteGroupMakeCommand extends GeneratorCommand
{
    use CreatesMatchingTest;

    /**
     * {@inheritdoc}
     */
    protected $name = 'make:route-group';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Create a new route group';

    /**
     * {@inheritdoc}
     */
    protected $type = 'Route Group';

    /**
     * {@inheritdoc}
     */
    public function handle(): ?bool
    {
        if (parent::handle() === false) {
            return false;
        }

        $this->decorateStub();

        return null;
    }

    /**
     * {@inheritdoc}
     */
    protected function getStub(): string
    {
        return __DIR__.'/stubs/RouteGroup.php.stub';
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function decorateStub(): void
    {
        $name = $this->qualifyClass($this->getNameInput());
        $path = $this->getPath($name);
        $stub = $this->files->get($path);

        $prefix = strtolower($this->argument('prefix') ?: '');

        if ($prefix && ! Str::startsWith($prefix, '/')) {
            $prefix = "/{$prefix}";
        }

        $stub = str_replace('{{ prefix }}', $prefix, $stub);

        $this->files->put($path, $stub);
    }

    /**
     * Get the default namespace for the class.
     */
    public function getDefaultNamespace($rootNamespace): string
    {
        return config('single-file-routes.routes-namespace');
    }

    /**
     * {@inheritdoc}
     */
    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the route group'],
            ['prefix', InputArgument::OPTIONAL, 'The prefix for the route group'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getOptions(): array
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the group already exists'],
        ];
    }
}
