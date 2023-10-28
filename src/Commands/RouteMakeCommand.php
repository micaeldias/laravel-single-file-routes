<?php

namespace MicaelDias\SingleFileRoutes\Commands;

use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use MicaelDias\SingleFileRoutes\Routing\RouteGroup;
use ReflectionClass;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class RouteMakeCommand extends GeneratorCommand
{
    use CreatesMatchingTest;

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
     * Any extra args to be passed to the __invoke function.
     */
    protected $extraArgs = [];

    /**
     * {@inheritdoc}
     *
     * @throws \ReflectionException
     */
    public function handle()
    {
        parent::handle();

        $this->decorateStub();
        $this->registerRouteToGroup();
    }

    public function interact(InputInterface $input, OutputInterface $output)
    {
        try {
            parent::interact($input, $output);
        } catch (\Throwable $e) {
            $this->components->error($e->getMessage());

            return false;
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
        $this->determineName($input);
        $this->determineExtraArgs($input);

        parent::promptForMissingArguments($input, $output);
    }

    /**
     * @throws \Exception
     */
    protected function determineRouteGroup(InputInterface &$input): void
    {
        $group = $input->getArgument('route-group');

        if (is_null($group)) {
            $groups = $this->getRouteGroupClasses();

            if (empty($groups)) {
                throw new \Exception(
                    'You need to have at least one route group before creating '.
                    'routes. Did you run php artisan make:route-group?'
                );
            }

            $group = select(
                'What route group should this route be under?',
                $groups
            );
        }

        if (! is_subclass_of($group, RouteGroup::class)) {
            throw new \Exception('Invalid route group provided.');
        }

        $input->setArgument('route-group', $group);
    }

    protected function determineMethod(InputInterface &$input): void
    {
        $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];
        $method = $input->getArgument('method');

        if (in_array($method, $methods)) {
            return;
        }

        $input->setArgument('method', select(
            'What HTTP method should this route respond to?',
            $methods
        ));
    }

    protected function determineUri(InputInterface &$input): void
    {
        $uri = $input->getArgument('uri');

        if (is_null($uri)) {
            $group = $input->getArgument('route-group');
            $prefix = $group::$prefix;

            $uri = text(
                'What URI should this route respond to?',
                '',
                '',
                true,
                null,
                $prefix ? "The route group's prefix is included ({$prefix})" : ''
            );
        }

        $uri = Str::lower(Str::startsWith($uri, '/') ? $uri : "/{$uri}");

        $input->setArgument('uri', $uri);
    }

    /**
     * @throws \ReflectionException
     */
    protected function getNamespaceForName(InputInterface &$input)
    {
        $namespace = $input->getArgument('namespace');

        if (! $namespace) {
            $groupReflection = new ReflectionClass($input->getArgument('route-group'));

            $uriToNamespace = collect(explode('/', $input->getArgument('uri')))
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

            $namespace = $groupReflection->getNamespaceName().$uriToNamespace;
        }

        $input->setArgument('namespace', $namespace);

        return $namespace;
    }

    /**
     * @throws \ReflectionException
     */
    protected function determineName(InputInterface &$input): void
    {
        $namespace = $this->getNamespaceForName($input).'\\';
        $name = $input->getArgument('name');

        if (is_null($name)) {
            $name = text(
                'Where should this route be placed?',
                '',
                $namespace,
                true,
                null,
                'E.g. Index, Get, SignIn, etc'
            );
        } else {
            $name = $namespace.Str::ucfirst($name);
        }

        $input->setArgument('name', $name);
    }

    protected function determineExtraArgs(InputInterface $input): void
    {
        /** @var RouteGroup|string $group */
        $group = $input->getArgument('route-group');
        $fullUri = $group::$prefix.$input->getArgument('uri');

        $this->extraArgs = Str::matchAll("/({\w+})/", $fullUri)
            ->map(function ($arg) {
                return '$'.Str::replace(['{', '}'], '', $arg);
            })
            ->toArray();
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

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function decorateStub(): void
    {
        $name = $this->qualifyClass($this->getNameInput());
        $path = $this->getPath($name);
        $stub = $this->files->get($path);

        $groupFQN = $this->argument('route-group');
        $group = Str::afterLast($groupFQN, '\\');

        $stub = str_replace('{{ groupFQN }}', $groupFQN, $stub);
        $stub = str_replace('{{ group }}', $group, $stub);
        $stub = str_replace('{{ method }}', $this->argument('method'), $stub);
        $stub = str_replace('{{ uri }}', $this->argument('uri'), $stub);
        $stub = str_replace(
            '{{ extraArgs }}',
            empty($this->extraArgs) ? '' : ', '.implode(', ', $this->extraArgs),
            $stub
        );

        $this->files->put($path, $stub);
    }

    /**
     * @throws \ReflectionException
     */
    protected function registerRouteToGroup(): void
    {
        $groupReflection = new ReflectionClass($this->argument('route-group'));
        $class = $this->argument('name');

        if (config('single-file-routes.auto-discovery', true)
            && Str::startsWith($class, $groupReflection->getNamespaceName())
        ) {
            return;
        }

        $path = $groupReflection->getFileName();
        $contents = file_get_contents($path);
        $search = 'public static $routes = [';
        $suffix = Str::contains($contents, 'public static $routes = []') ? '    ' : '';

        file_put_contents($path, str_replace(
            $search,
            $search.PHP_EOL.'        \\'."{$class}::class,".PHP_EOL.$suffix,
            $contents
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function getArguments(): array
    {
        return [
            ['namespace', InputArgument::REQUIRED, 'The namespace this route should registered under'],
            ['route-group', InputArgument::REQUIRED, 'The route group this route should be under'],
            ['method', InputArgument::REQUIRED, 'The HTTP method of the route'],
            ['uri', InputArgument::REQUIRED, 'The URI of the route'],
            ['name', InputArgument::REQUIRED, 'The name of the route'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getOptions(): array
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the Route already exists'],
        ];
    }
}
