<?php

namespace MicaelDias\SingleFileRoutes\Commands;

use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use MicaelDias\SingleFileRoutes\Routing\RouteServiceProvider;
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
    public function handle(): ?bool
    {
        $this->decorateOptions();

        if (parent::handle() === false) {
            return false;
        }

        $this->decorateStub();

        $this->registerGroupToServiceProvider();

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
     * {@inheritdoc}
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $this->option('namespace');
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

    protected function determineName(InputInterface &$input): void
    {
        if (is_null($input->getOption('name'))) {
            $input->setOption('name', text(
                'What should this route group be named?',
                '',
                '',
                true,
                null,
                'E.g. Api'
            ));
        }
    }

    protected function determinePrefix(InputInterface &$input): void
    {
        if (is_null($input->getOption('prefix'))) {
            $input->setOption('prefix', text(
                'What should this route group prefix be?',
                '(optional)',
                '',
                false,
                null,
                'E.g. /api'
            ));
        }
    }

    protected function determineNamespace(InputInterface &$input): void
    {
        if (is_null($input->getOption('namespace'))) {
            $default = config('single-file-routes.routes-namespace').'\\'.ucfirst($input->getOption('name'));

            $input->setOption('namespace', text(
                'Where should this route group be placed?',
                '',
                $default,
                true
            ));
        }
    }

    protected function decorateOptions(): void
    {
        $name = $this->input->getOption('name');

        if (! Str::contains($name, 'RouteGroup')) {
            $name .= 'RouteGroup';
        }

        $this->input->setOption('name', ucfirst($name));

        $prefix = $this->input->getOption('prefix');

        if ($prefix && ! Str::startsWith($prefix, '/')) {
            $prefix = "/{$prefix}";
        }

        $this->input->setOption('prefix', strtolower($prefix));
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function decorateStub(): void
    {
        $name = $this->qualifyClass($this->getNameInput());
        $path = $this->getPath($name);
        $stub = $this->files->get($path);

        $baseGroupFQN = config('single-file-routes.route-group-class');

        $stub = str_replace('{{ baseGroupFQN }}', $baseGroupFQN, $stub);
        $stub = str_replace('{{ baseGroup }}', Str::afterLast($baseGroupFQN, '\\'), $stub);
        $stub = str_replace('{{ prefix }}', $this->option('prefix'), $stub);

        $this->files->put($path, $stub);
    }

    /**
     * @throws \Exception
     */
    protected function getServiceProviderPath(): string
    {
        $default = app_path('Providers/SingleFileRoutesServiceProvider.php');

        if (file_exists($default)) {
            return $default;
        }

        $classes = ClassFinder::getClassesInNamespace(
            $this->laravel->getNamespace(),
            ClassFinder::RECURSIVE_MODE
        );

        $found = Arr::first($classes, function (string $class) {
            return is_subclass_of($class, RouteServiceProvider::class);
        });

        if (! $found) {
            throw new \Exception("SingleFileRoutesServiceProvider could not be determined.\n".
                'Did you run php artisan single-file-routes:install?');
        }

        return $found;
    }

    protected function registerGroupToServiceProvider(): void
    {
        $path = $this->getServiceProviderPath();
        $search = 'protected array $groups = [';
        $class = $this->option('namespace').'\\'.$this->option('name');
        $contents = file_get_contents($path);

        if (Str::contains($contents, "{$class}::class")) {
            return;
        }

        $suffix = Str::contains($contents, 'protected array $groups = []') ? PHP_EOL.'    ' : '';

        file_put_contents($path, str_replace(
            $search,
            $search.PHP_EOL.'        \\'."{$class}::class,".$suffix,
            $contents
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function getNameInput(): string
    {
        return trim($this->option('name'));
    }

    /**
     * {@inheritdoc}
     */
    protected function getArguments(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    protected function getOptions(): array
    {
        return [
            ['name', null, InputOption::VALUE_REQUIRED, 'The name of the route group'],
            ['prefix', null, InputOption::VALUE_OPTIONAL, 'The prefix for the route group'],
            ['namespace', null, InputOption::VALUE_REQUIRED, 'The namespace of the route group'],
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the Route already exists'],
        ];
    }
}
