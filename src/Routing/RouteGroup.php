<?php

namespace MicaelDias\SingleFileRoutes\Routing;

use HaydenPierce\ClassFinder\ClassFinder;

use function config;

abstract class RouteGroup
{
    /**
     * The URI prefix for all routes on this group.
     */
    public static $prefix = '';

    /**
     * The middleware used for all routes on this group.
     */
    public static $middleware = [];

    /**
     * The routes belonging to this group.
     */
    public static $routes = [];

    /**
     * The routes belonging to this group.
     *
     * @throws \Exception
     */
    public static function routes(): array
    {
        if (! config('single-file-routes.auto-discovery', true)) {
            return static::$routes;
        }

        $reflection = new \ReflectionClass(static::class);

        $classes = ClassFinder::getClassesInNamespace(
            $reflection->getNamespaceName(),
            ClassFinder::RECURSIVE_MODE
        );

        $found = array_filter($classes, function (string $class) {
            return is_subclass_of($class, Route::class);
        });

        return array_values(array_unique(array_merge($found, static::$routes)));
    }
}
