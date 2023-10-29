<?php

namespace MicaelDias\SingleFileRoutes\Routing;

abstract class RouteGroup
{
    /**
     * The URI prefix for all routes on this group.
     */
    public static string $prefix = '';

    /**
     * The middleware used for all routes on this group.
     *
     *  @var string[]
     */
    public static array $middleware = [];

    /**
     * The routes belonging to this group.
     *
     *  @var Route[]|string[]
     */
    public static array $routes = [];
}
