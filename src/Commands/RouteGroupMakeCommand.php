<?php

namespace MicaelDias\SingleFileRoutes\Commands;

use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use MicaelDias\SingleFileRoutes\Routing\RouteServiceProvider;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\text;

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
    protected $type = 'RouteGroup';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        parent::handle();

        $this->decorateStub();
        $this->registerGroupToServiceProvider();
    }

    /**
     * {@inheritdoc}
     */
    protected function getStub(): string
    {
        return __DIR__.'/stubs/RouteGroup.php.stub';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $this->argument('namespace');
    }

    /**
     * {@inheritdoc}
     */
    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the route group'],
            ['prefix', InputArgument::REQUIRED, 'The prefix for the route group'],
            ['namespace', InputArgument::REQUIRED, 'The namespace of the route group'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function promptForMissingArguments(InputInterface $input, OutputInterface $output)
    {
        $this->determineName($input);
        $this->determinePrefix($input);
        $this->determineNamespace($input);

        parent::promptForMissingArguments($input, $output);
    }

    /**
     * {@inheritdoc}
     */
    protected function getOptions(): array
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the route group already exists'],
        ];
    }

    protected function determinePrefix(InputInterface &$input): void
    {
        $prefix = $input->getArgument('prefix');

        if (is_null($prefix)) {
            $prefix = text(
                'What should this route group prefix be?',
                '(optional)',
                '',
                false,
                null,
                'E.g. /api'
            );
        }

        if ($prefix) {
            Str::lower(Str::startsWith($prefix, '/') ? $prefix : "/{$prefix}");
        }

        $input->setArgument('prefix', $prefix);
    }

    protected function determineName(InputInterface &$input): void
    {
        $name = $input->getArgument('name');

        if (is_null($name)) {
            $name = text(
                'What should this route group be named?',
                '',
                '',
                true,
                null,
                'E.g. Api'
            );
        }

        $name = ucfirst($name).'RouteGroup';

        $input->setArgument('name', $name);
    }

    protected function determineNamespace(InputInterface &$input): void
    {
        if (is_null($input->getArgument('namespace'))) {
            $name = Str::replace('RouteGroup', '', $input->getArgument('name'));

            $default = config('single-file-routes.routes-namespace').'\\'.$name;

            $input->setArgument('namespace', text(
                'Where should this route group be placed?',
                '',
                $default,
                true
            ));
        }
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function decorateStub(): void
    {
        $name = $this->qualifyClass($this->getNameInput());
        $path = $this->getPath($name);
        $stub = $this->files->get($path);

        $stub = str_replace('{{ prefix }}', $this->input->getArgument('prefix'), $stub);

        $this->files->put($path, $stub);
    }

    protected function getServiceProviderPath(): string
    {
        $default = app_path('Providers/SingleFileRoutesServiceProvider.php');

        if (file_exists($default)) {
            return $default;
        }

        try {
            $classes = ClassFinder::getClassesInNamespace(
                $this->laravel->getNamespace(),
                ClassFinder::RECURSIVE_MODE
            );

            $found = Arr::first($classes, function (string $class) {
                return is_subclass_of($class, RouteServiceProvider::class);
            });

            if (! $found) {
                throw new \Exception();
            }

            return $found;
        } catch (\Exception $e) {
            $this->components->error("SingleFileRoutesServiceProvider could not be determined.\n".
                'Did you run php artisan single-file-routes:install?');

            return false;
        }
    }

    protected function registerGroupToServiceProvider(): void
    {
        $path = $this->getServiceProviderPath();
        $search = 'protected $groups = [';
        $class = $this->argument('namespace').'\\'.$this->argument('name');
        $contents = file_get_contents($path);

        $suffix = Str::contains($contents, 'protected $groups = []') ? '    ' : '';

        file_put_contents($path, str_replace(
            $search,
            $search.PHP_EOL.'        \\'."{$class}::class,".PHP_EOL.$suffix,
            $contents
        ));
    }
}
