<?php

namespace MicaelDias\SingleFileRoutes\Routing;

use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Boot the service.
     *
     * @throws \ReflectionException
     */
    public function boot(Router $router): void
    {
        $this->registerRoutes($router);
    }

    /**
     * Register the routes defined by the application.
     *
     * @throws \ReflectionException
     */
    protected function registerRoutes(Router $router): void
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        $classes = ClassFinder::getClassesInNamespace(
            $this->getAppNamespace(),
            ClassFinder::RECURSIVE_MODE
        );

        foreach ($classes as $class) {
            $reflection = new ReflectionClass($class);

            foreach ($reflection->getAttributes(Route::class) as $routeAttribute) {
                $this->registerRoute($router, $routeAttribute, $class);
            }

            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                foreach ($method->getAttributes(Route::class) as $routeAttribute) {
                    $this->registerRoute(
                        $router,
                        $routeAttribute,
                        [$class, $method->getName()],
                        "{$class}::{$method->getName()}()"
                    );
                }
            }
        }
    }

    protected function registerRoute(
        Router $router,
        ReflectionAttribute $routeAttribute,
        $action,
        string $name = null
    ): void {
        /** @var Route $route */
        $route = $routeAttribute->newInstance();

        /** @var RouteGroup|string|null $group */
        $group = $route->group;

        if ($group) {
            $register = ($domain = $group::domain()) ? $router->domain($domain) : $router;
            $middleware = $group::middleware();
            $attrs = ['prefix' => $group::prefix()];
        } else {
            $register = $router;
            $middleware = [];
            $attrs = [];
        }

        $register->group($attrs, function (Router $router) use ($route, $middleware, $action, $name) {
            $router->match(
                $route->method,
                $route->uri,
                $action
            )
                ->name($route->name ?? $name ?? $action)
                ->middleware(array_merge($middleware, $route->middleware));
        });
    }

    protected function getAppNamespace(): string
    {
        return $this->app->getNamespace();
    }
}
