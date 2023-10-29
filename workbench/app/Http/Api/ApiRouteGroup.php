<?php

namespace Workbench\App\Http\Api;

use MicaelDias\SingleFileRoutes\Routing\RouteGroup;

class ApiRouteGroup extends RouteGroup
{
    public static string $prefix = '/api';

    public static array $middleware = [];

    public static array $routes = [
        \Workbench\App\Http\Api\User\Get::class,
    ];
}
