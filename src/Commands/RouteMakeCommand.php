<?php

namespace MicaelDias\SingleFileRoutes\Commands;

use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use MicaelDias\SingleFileRoutes\Routing\RouteGroup;
use ReflectionClass;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class RouteMakeCommand extends GeneratorCommand
{
    use CreatesMatchingTest;

    /**
     * Available HTTP methods.
     */
    const METHODS = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

    /**
     * {@inheritdoc}
     */
    protected $name = 'make:route';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Create a new route';

    /**
     * {@inheritdoc}
     */
    protected $type = 'Route';

    /**
     * {@inheritdoc}
     *
     * @throws \ReflectionException
     */
    public function handle(): ?bool
    {
        if ($message = $this->decorateOptions()) {
            $this->components->error($message);

            return false;
        }

        if (parent::handle() === false) {
            return false;
        }

        $this->decorateStub();

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function interact(InputInterface $input, OutputInterface $output): void
    {
        try {
            parent::interact($input, $output);
        } catch (\Throwable $e) {
            $this->components->error($e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    protected function promptForMissingArguments(InputInterface $input, OutputInterface $output)
    {
        $this->determineRouteGroup($input);
        $this->determineMethod($input);
        $this->determineUri($input);
        $this->determineNamespace($input);
        $this->determineName($input);

        parent::promptForMissingArguments($input, $output);
    }

    /**
     * @throws \Exception
     */
    protected function determineRouteGroup(InputInterface &$input): void
    {
        $group = $input->getOption('group');

        if (is_null($group)) {
            $groups = $this->getRouteGroupClasses();

            $group = select(
                label: 'What route group should this route be under?',
                options: ['None', ...$groups],
                default: 'None',
            );
        }

        $input->setOption('group', $group !== 'None' ? $group : null);
    }

    protected function determineMethod(InputInterface &$input): void
    {
        $method = $input->getOption('method');

        if (in_array($method, static::METHODS)) {
            return;
        }

        $input->setOption('method', select(
            'What HTTP method should this route respond to?',
            static::METHODS
        ));
    }

    protected function determineUri(InputInterface &$input): void
    {
        $uri = $input->getOption('uri');

        if (is_null($uri)) {
            /** @var RouteGroup|string|null $group */
            $group = $input->getOption('group');

            $prefix = $group ? $group::prefix() : null;

            $uri = text(
                'What URI should this route respond to?',
                '',
                '',
                true,
                null,
                $prefix ? "The route group's prefix is included ({$prefix})" : ''
            );
        }

        $input->setOption('uri', $uri);
    }

    /**
     * @throws \ReflectionException
     */
    protected function determineNamespace(InputInterface &$input)
    {
        $namespace = $input->getOption('namespace');

        if ($namespace) {
            return;
        }

        $routesNamespace = config('single-file-routes.routes-namespace');

        if (! config('single-file-routes.generate-from-uri')) {
            $input->setOption('namespace', $routesNamespace);

            return;
        }

        /** @var RouteGroup|string|null $group */
        $group = $input->getOption('group');

        if ($group) {
            $fullUri = $group::prefix() . Str::start($input->getOption('uri'), '/');
        } else {
            $fullUri = $input->getOption('uri');
        }

        $uri = Str::replaceStart('/', '', $fullUri);

        $uriToNamespace = collect(explode('/', $uri))
            ->filter(function ($part) {
                return ! Str::startsWith($part, '{') && ! Str::endsWith($part, '}');
            })
            ->map(function ($part) {
                if (Str::contains($part, '-')) {
                    $part = collect(explode('-', $part))
                        ->map(function ($part) {
                            return Str::ucfirst($part);
                        })
                        ->implode('');
                }

                return Str::ucfirst($part);
            })
            ->implode('\\');

        $input->setOption('namespace', $routesNamespace . "\\" . $uriToNamespace);
    }

    protected function determineName(InputInterface &$input): void
    {
        $name = $input->getOption('name');

        if ($name) {
            return;
        }

        $namespace = $input->getOption('namespace').'\\';

        $name = text(
            'Where should this route be placed?',
            '',
            $namespace,
            true,
            null,
            'E.g. App\\Http\\Routes\\User\\Get'
        );

        $input->setOption('namespace', Str::beforeLast($name, '\\'));
        $input->setOption('name', Str::afterLast($name, '\\'));
    }

    /**
     * Get the route groups of the application.
     *
     * @throws \Exception
     */
    protected function getRouteGroupClasses(): array
    {
        $classes = ClassFinder::getClassesInNamespace(
            $this->laravel->getNamespace(),
            ClassFinder::RECURSIVE_MODE
        );

        return array_values(array_filter($classes, function ($class) {
            return is_subclass_of($class, RouteGroup::class);
        }));
    }

    /**
     * {@inheritdoc}
     */
    protected function getStub(): string
    {
        return __DIR__.'/stubs/Route.php.stub';
    }

    protected function decorateOptions(): ?string
    {
        $group = $this->option('group');

        if ($group && ! is_subclass_of($this->option('group'), RouteGroup::class)) {
            return 'Invalid route group provided.';
        }

        if (! in_array($this->option('method'), static::METHODS)) {
            return 'Invalid HTTP method provided.';
        }

        $uri = $this->option('uri');

        $this->input->setOption('uri', Str::lower(Str::startsWith($uri, '/') ? $uri : "/{$uri}"));

        $this->input->setOption(
            'name',
            $this->option('namespace').'\\'.Str::ucfirst($this->option('name'))
        );

        return null;
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function decorateStub(): void
    {
        $name = $this->qualifyClass($this->getNameInput());
        $path = $this->getPath($name);
        $stub = $this->files->get($path);

        /** @var RouteGroup|string|null $groupFQN */
        $groupFQN = $this->option('group');

        $uri = $this->option('uri');

        if ($groupFQN) {
            $group = Str::afterLast($groupFQN, '\\');

            $fullUri = $groupFQN::prefix().$uri;
            $groupFQN = "\nuse {$groupFQN};";
            $group =  ", group: {$group}::class";
        } else {
            $fullUri = $uri;
            $groupFQN = "";
            $group = "";
        }

        $extraArgs = Str::matchAll("/({\w+})/", $fullUri)
            ->map(function ($arg) {
                return '$'.Str::replace(['{', '}'], '', $arg);
            })
            ->toArray();

        $stub = str_replace('{{ groupFQN }}', $groupFQN, $stub);
        $stub = str_replace('{{ group }}', $group, $stub);
        $stub = str_replace('{{ method }}', $this->option('method'), $stub);
        $stub = str_replace('{{ uri }}', $this->option('uri'), $stub);
        $stub = str_replace(
            '{{ extraArgs }}',
            empty($extraArgs) ? '' : ', '.implode(', ', $extraArgs),
            $stub
        );

        $this->files->put($path, $stub);
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
            ['namespace', null, InputOption::VALUE_REQUIRED, 'The namespace this route should registered under'],
            ['group', null, InputOption::VALUE_REQUIRED, 'The route group this route should be under'],
            ['method', null, InputOption::VALUE_REQUIRED, 'The HTTP method of the route'],
            ['uri', null, InputOption::VALUE_REQUIRED, 'The URI of the route'],
            ['name', null, InputOption::VALUE_REQUIRED, 'The name of the route'],
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the Route already exists'],
        ];
    }
}
