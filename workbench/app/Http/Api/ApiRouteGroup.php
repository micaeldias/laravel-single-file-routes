<?php

namespace Workbench\App\Http\Api;

use MicaelDias\SingleFileRoutes\Routing\RouteGroup;

class ApiRouteGroup extends RouteGroup
{
    public static $prefix = '/api';

    public static $middleware = [];
}
