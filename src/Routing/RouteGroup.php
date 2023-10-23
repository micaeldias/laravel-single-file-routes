<?php

namespace MicaelDias\SingleFileRoutes\Routing;

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
}
